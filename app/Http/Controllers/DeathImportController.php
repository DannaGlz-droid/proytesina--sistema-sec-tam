<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Death;
use App\Models\DeathCause;
use App\Models\DeathLocation;
use App\Models\Municipality;
use App\Models\Jurisdiction;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class DeathImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB
        ]);
        // wrap entire import in try/catch so the frontend always receives JSON on error
        try {
            $user = $request->user();
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            // store a copy for record/audit, but prefer reading directly from PHP's uploaded tmp file
            // to avoid platform-specific race/OneDrive issues where stored path may be missing.
            $path = $file->store('tmp_imports');
            $storedAbsPath = storage_path('app/' . ltrim($path, '/\\'));

            // Prefer the PHP uploaded tmp path for immediate reads
            $tmpReal = $file->getRealPath();
            $readPath = null;
            if ($tmpReal && file_exists($tmpReal)) {
                $readPath = $tmpReal;
            } elseif (file_exists($storedAbsPath)) {
                $readPath = $storedAbsPath;
            } else {
                // Nothing available to read; include both expected paths in the error for easier debugging
                throw new \Exception('Uploaded file was not found on disk. Checked tmp: ' . ($tmpReal ?: '(null)') . ' and stored: ' . $storedAbsPath);
            }

            // create import record
            $importId = DB::table('imports')->insertGetId([
                'user_id' => $user ? $user->id : null,
                'original_name' => $originalName,
                'path' => $path,
                'status' => 'processing',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Load the spreadsheet via PhpSpreadsheet and extract sheets as arrays
            $sheetNames = [];
            $sheets = [];
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($readPath);
                $sheetNames = $spreadsheet->getSheetNames();
                foreach ($spreadsheet->getAllSheets() as $sheetObj) {
                    $sheets[] = $sheetObj->toArray();
                }
            } catch (\Throwable $e) {
                Log::error('Failed loading spreadsheet for import', ['path' => $readPath, 'error' => $e->getMessage()]);
                throw new \Exception('No se pudo leer el archivo de importación: ' . $e->getMessage());
            }

        // Prepare municipalities lookup with normalized keys for matching (STRICT mode: do not create fallback)
        $municipalityLookup = [];
        $municipalities = Municipality::all();
        foreach ($municipalities as $m) {
            $key = $this->normalizeMunicipalityName($m->name);
            if (!isset($municipalityLookup[$key])) $municipalityLookup[$key] = $m;
        }

        // Prepare death locations and causes lookup (require existing entries for strict import)
        $deathLocationLookup = [];
        foreach (DeathLocation::all() as $loc) {
            $deathLocationLookup[mb_strtolower(trim($loc->name))] = $loc;
        }

        $deathCauseLookup = [];
        foreach (DeathCause::all() as $c) {
            $deathCauseLookup[mb_strtolower(trim($c->name))] = $c;
        }

        $rowsTotal = 0;
        $rowsImported = 0;
        $rowsFailed = 0;
        $rowsConvertedMonths = 0;
        $failedRows = [];

        foreach ($sheets as $sheetIndex => $sheet) {
            if (empty($sheet)) continue;

            // first row as header
            $headerRow = array_shift($sheet);
            $headers = array_map(function ($h) {
                return strtolower(trim(str_replace([' ', '_'], '', (string)$h)));
            }, $headerRow ?: []);

            // map headers to expected keys
            $map = [];
            foreach ($headers as $i => $h) {
                $map[$i] = $h; // keep normalized header
            }

                // Determine cause from sheet name if available and require it exists (strict)
                $causeName = isset($sheetNames[$sheetIndex]) ? trim($sheetNames[$sheetIndex]) : ('Causa ' . ($sheetIndex + 1));
                $normCause = mb_strtolower($causeName);

                // Detect default sheet names (Sheet1, Hoja1, Worksheet) — if so, require per-row 'causa' column
                $isDefaultSheetName = false;
                if ($causeName === '' || preg_match('/^(sheet|hoja|worksheet)\d*$/i', $causeName)) {
                    $isDefaultSheetName = true;
                }

                $deathCause = null;
                if (! $isDefaultSheetName) {
                    $deathCause = $deathCauseLookup[$normCause] ?? null;
                    // if not found, we'll create it later if needed
                }

            foreach ($sheet as $rowNum => $row) {
                $rowsTotal++;
                $rowAssoc = [];
                foreach ($map as $i => $key) {
                    $rowAssoc[$key] = isset($row[$i]) ? $row[$i] : null;
                }

                // If the entire row is empty (all mapped values blank/null), skip it silently
                $allEmpty = true;
                foreach ($rowAssoc as $v) {
                    if (!is_null($v) && trim((string)$v) !== '') { $allEmpty = false; break; }
                }
                if ($allEmpty) {
                    // we incremented rowsTotal earlier for the loop; subtract it back and skip
                    $rowsTotal--;
                    continue;
                }

                // Normalize and map expected fields
                $name = trim((string)($rowAssoc['nombre'] ?? '')) ?: null;
                // government folio may appear under various header names; also accept first column when header missing
                $folio = null;
                $possibleFolioKeys = ['folio','folio_gob','folio_gubernamental','folio_gobierno','id','numero','nfolio','no_folio'];
                foreach ($possibleFolioKeys as $k) {
                    if (isset($rowAssoc[$k]) && trim((string)$rowAssoc[$k]) !== '') { $folio = trim((string)$rowAssoc[$k]); break; }
                }
                // if still null, attempt to use the raw first cell of the $row array (index 0)
                if (is_null($folio) && isset($row[0]) && trim((string)$row[0]) !== '') {
                    $folio = trim((string)$row[0]);
                }
                // Accept both correct header names and older/mistyped ones for backwards compatibility
                $first = trim((string)(($rowAssoc['primerapellido'] ?? $rowAssoc['primerapellid'] ?? ''))) ?: null;
                $second = trim((string)(($rowAssoc['segundoapellido'] ?? $rowAssoc['segundoapellid'] ?? ''))) ?: null;
                $age = isset($rowAssoc['edad']) ? (int)$rowAssoc['edad'] : null;
                $claveEdad = isset($rowAssoc['claveedadd']) ? trim((string)$rowAssoc['claveedadd']) : null;
                $sex = isset($rowAssoc['sexod']) ? strtoupper(trim((string)$rowAssoc['sexod'])) : null;
                $dateRaw = $rowAssoc['fechadefuncion'] ?? ($rowAssoc['fechadefuncion'] ?? null);
                $residenceMunicipalityName = trim((string)($rowAssoc['municipioresidenciad'] ?? '')) ?: null;
                $deathMunicipalityName = trim((string)($rowAssoc['municipiodefunciond'] ?? '')) ?: null;
                $siteName = trim((string)($rowAssoc['sitiodefunciond'] ?? '')) ?: null;

                // basic validations (STRICT: require gov_folio; municipalities outside Tamaulipas map to 'OTRO')
                $errors = [];
                if (!$name) $errors[] = 'Nombre vacío';
                if (!$first) $errors[] = 'Primer apellido vacío';
                if (!$dateRaw) $errors[] = 'Fecha de defunción vacía';

                // parse date
                $deathDate = null;
                if ($dateRaw) {
                    try {
                        if (is_numeric($dateRaw)) {
                            $deathDate = ExcelDate::excelToDateTimeObject($dateRaw);
                        } else {
                            $deathDate = Carbon::parse($dateRaw);
                        }
                    } catch (\Throwable $e) {
                        $errors[] = 'Fecha inválida: ' . $dateRaw;
                    }
                }

                // lookup municipalities using normalized exact match first.
                // If not found (likely outside Tamaulipas), use/create a generic 'OTRO' municipality and jurisdiction.
                $residenceMunicipality = null;
                $deathMunicipality = null;

                $otherJur = Jurisdiction::firstOrCreate(['name' => 'OTRO']);

                if ($residenceMunicipalityName) {
                    $norm = $this->normalizeMunicipalityName($residenceMunicipalityName);
                    if (isset($municipalityLookup[$norm])) {
                        $residenceMunicipality = $municipalityLookup[$norm];
                    } else {
                        // municipality not found in Tamaulipas -> map to OTRO
                        $residenceMunicipality = Municipality::firstOrCreate([
                            'name' => 'OTRO'
                        ], [
                            'jurisdiction_id' => $otherJur->id
                        ]);
                        $municipalityLookup[$this->normalizeMunicipalityName($residenceMunicipality->name)] = $residenceMunicipality;
                    }
                } else {
                    // No residence municipality provided -> set to OTRO
                    $residenceMunicipality = Municipality::firstOrCreate([
                        'name' => 'OTRO'
                    ], [
                        'jurisdiction_id' => $otherJur->id
                    ]);
                    $municipalityLookup[$this->normalizeMunicipalityName($residenceMunicipality->name)] = $residenceMunicipality;
                }

                if ($deathMunicipalityName) {
                    $normD = $this->normalizeMunicipalityName($deathMunicipalityName);
                    if (isset($municipalityLookup[$normD])) {
                        $deathMunicipality = $municipalityLookup[$normD];
                    } else {
                        // municipality not found in Tamaulipas -> map to OTRO
                        $deathMunicipality = Municipality::firstOrCreate([
                            'name' => 'OTRO'
                        ], [
                            'jurisdiction_id' => $otherJur->id
                        ]);
                        $municipalityLookup[$this->normalizeMunicipalityName($deathMunicipality->name)] = $deathMunicipality;
                    }
                } else {
                    // No death municipality provided -> set to OTRO
                    $deathMunicipality = Municipality::firstOrCreate([
                        'name' => 'OTRO'
                    ], [
                        'jurisdiction_id' => $otherJur->id
                    ]);
                    $municipalityLookup[$this->normalizeMunicipalityName($deathMunicipality->name)] = $deathMunicipality;
                }

                // Validate gov_folio strictly: require and format 9 digits
                if (is_null($folio) || !preg_match('/^[0-9]{9}$/', (string)$folio)) {
                    $errors[] = 'Folio gubernamental inválido o ausente (se requieren 9 dígitos)';
                }

                // map or create death location: use existing or create new if missing
                $deathLocation = null;
                if ($siteName) {
                    $normSite = mb_strtolower(trim($siteName));
                    $deathLocation = $deathLocationLookup[$normSite] ?? null;
                    if (!$deathLocation) {
                        // create new DeathLocation and add to lookup
                        $deathLocation = DeathLocation::firstOrCreate(['name' => $siteName]);
                        $deathLocationLookup[mb_strtolower(trim($deathLocation->name))] = $deathLocation;
                    }
                } else {
                    $errors[] = 'Lugar de defunción vacío';
                }

                // map or create cause: prefer per-row 'causa' column.
                // If sheet name is default (Sheet1/Hoja1) and no per-row causa, reject the row.
                if (!empty($rowAssoc['causa']) && trim((string)$rowAssoc['causa']) !== '') {
                    $rowCauseName = trim((string)$rowAssoc['causa']);
                    $dcNorm = mb_strtolower($rowCauseName);
                    $deathCause = $deathCauseLookup[$dcNorm] ?? null;
                    if (!$deathCause) {
                        $deathCause = DeathCause::firstOrCreate(['name' => $rowCauseName]);
                        $deathCauseLookup[mb_strtolower(trim($deathCause->name))] = $deathCause;
                    }
                } else {
                    if ($isDefaultSheetName) {
                        $errors[] = 'Causa no indicada en la hoja ni en la fila (hoja con nombre por defecto).';
                    } else {
                        // Sheet has a meaningful name — create or reuse sheet-level cause
                        if (empty($deathCause)) {
                            $deathCause = DeathCause::firstOrCreate(['name' => $causeName]);
                            $deathCauseLookup[mb_strtolower(trim($deathCause->name))] = $deathCause;
                        }
                    }
                }

                // if errors, mark failed
                if (!empty($errors)) {
                    $rowsFailed++;
                    $failedRows[] = array_merge(['sheet' => $causeName, 'row' => $rowNum + 2, 'errors' => implode('; ', $errors)], $rowAssoc);
                    continue;
                }

                // normalize sex
                if ($sex) {
                    if (in_array($sex, ['F','FEM','FEMENINO','MUJER'])) $sex = 'F';
                    elseif (in_array($sex, ['M','MAS','MASC','MASCULINO','HOMBRE'])) $sex = 'M';
                    else $sex = strtoupper(substr($sex,0,1));
                }

                // deathLocation was mapped earlier in strict mode; $deathLocation is set

                // Deduplication: DO NOT update an existing record when a government folio is provided.
                $existingDeath = null;
                $existsByFolio = Death::where('gov_folio', $folio)->exists();
                if ($existsByFolio) {
                    $rowsFailed++;
                    $failedRows[] = array_merge(['sheet' => $causeName, 'row' => $rowNum + 2, 'errors' => 'Duplicado detectado: folio existente'], $rowAssoc);
                    continue;
                }

                // Determine normalized age fields: age_years / age_months (STRICT: reject months >= 12)
                $ageYears = null;
                $ageMonths = null;
                if ($claveEdad) {
                    $claveNorm = mb_strtolower($claveEdad);
                    $claveNormAscii = iconv('UTF-8', 'ASCII//TRANSLIT', $claveNorm) ?: $claveNorm;
                    if (strpos($claveNormAscii, 'mes') !== false) {
                        $ageYears = 0;
                        $ageMonths = is_null($age) ? null : (int)$age;
                    } elseif (strpos($claveNormAscii, 'ano') !== false || strpos($claveNormAscii, 'año') !== false) {
                        $ageYears = is_null($age) ? null : (int)$age;
                        $ageMonths = null;
                    } else {
                        $ageYears = is_null($age) ? null : (int)$age;
                        $ageMonths = null;
                    }
                } else {
                    $ageYears = is_null($age) ? null : (int)$age;
                    $ageMonths = null;
                }

                if (!is_null($ageMonths) && $ageMonths >= 12) {
                    $rowsFailed++;
                    $failedRows[] = array_merge(['sheet' => $causeName, 'row' => $rowNum + 2, 'errors' => 'Edad inválida: meses debe ser menor a 12'], $rowAssoc);
                    continue;
                }

                // create or update death (prefer gov_folio upsert)
                if ($existingDeath) {
                    $d = $existingDeath;
                } else {
                    $d = new Death();
                }
                $d->name = $name;
                if ($folio) $d->gov_folio = (string)$folio;
                $d->first_last_name = $first;
                $d->second_last_name = $second;
                // keep legacy age as years when available (for compatibility)
                $d->age = $ageYears ?? $age;
                $d->age_years = $ageYears;
                $d->age_months = $ageMonths;
                $d->sex = $sex;
                $d->death_date = $deathDate ? $deathDate->format('Y-m-d') : null;
                $d->residence_municipality_id = $residenceMunicipality ? $residenceMunicipality->id : null;
                $d->death_municipality_id = $deathMunicipality->id;
                // Derive jurisdiction from municipality of residence when possible.
                // If residence municipality is the generic 'OTRO', explicitly set jurisdiction to 'OTRO'.
                if ($residenceMunicipality && $residenceMunicipality->jurisdiction_id) {
                    $d->jurisdiction_id = $residenceMunicipality->jurisdiction_id;
                } elseif ($residenceMunicipality && mb_strtolower(trim($residenceMunicipality->name)) === 'otro') {
                    // Ensure 'OTRO' jurisdiction exists and assign it
                    $otroJur = Jurisdiction::firstOrCreate(['name' => 'OTRO']);
                    $d->jurisdiction_id = $otroJur->id;
                } else {
                    $defaultJur = Jurisdiction::firstOrCreate(['name' => 'NO ENCONTRADA']);
                    $d->jurisdiction_id = $defaultJur->id;
                }
                $d->death_location_id = $deathLocation ? $deathLocation->id : null;
                $d->death_cause_id = $deathCause->id;
                $d->save();

                $rowsImported++;
            }
        }

        // write failed CSV if any
        $errorCsvPath = null;
        if (!empty($failedRows)) {
            $csvName = 'tmp_imports/errors_' . now()->format('Ymd_His') . '.csv';
            $fp = fopen(storage_path('app/' . $csvName), 'w');
            // header
            $first = reset($failedRows);
            fputcsv($fp, array_keys($first));
            foreach ($failedRows as $r) fputcsv($fp, $r);
            fclose($fp);
            $errorCsvPath = $csvName;
        }

        // update imports table
        DB::table('imports')->where('id', $importId)->update([
            'status' => 'completed',
            'rows_total' => $rowsTotal,
            'rows_imported' => $rowsImported,
            'rows_failed' => $rowsFailed,
            'error_csv_path' => $errorCsvPath,
            'updated_at' => now(),
        ]);

        // remove stored copy (we intentionally delete the stored copy; we do not touch PHP tmp file)
        try {
            if (isset($path)) Storage::delete($path);
        } catch (\Throwable $__e) {
            Log::warning('Failed deleting stored uploaded file after import', ['path' => $storedAbsPath, 'error' => $__e->getMessage()]);
        }

        return response()->json([
            'ok' => true,
            'total' => $rowsTotal,
            'imported' => $rowsImported,
            'failed' => $rowsFailed,
            'converted_months' => $rowsConvertedMonths,
            'errors_file' => $errorCsvPath ? Storage::url($errorCsvPath) : null,
        ]);
        } catch (\Throwable $e) {
            // log and update import record if possible
            Log::error('Import error: ' . $e->getMessage(), ['exception' => $e]);
            try {
                if (isset($importId)) {
                    DB::table('imports')->where('id', $importId)->update([
                        'status' => 'failed',
                        'error_message' => substr($e->getMessage(), 0, 1000),
                        'updated_at' => now(),
                    ]);
                }
            } catch (\Throwable $__e) {
                // ignore errors updating imports table
                Log::error('Failed updating imports table: ' . $__e->getMessage());
            }
            // remove temp file if created
            try { if (isset($path)) Storage::delete($path); } catch (\Throwable $__) {}

            return response()->json(['ok' => false, 'message' => $e->getMessage(), 'total' => 0, 'imported' => 0, 'failed' => 0], 500);
        }
    }

    /**
     * Normalize municipality name for matching: lowercase, remove accents, articles, common prefixes and punctuation.
     */
    private function normalizeMunicipalityName(?string $name): string
    {
        if (!$name) return '';
        $s = mb_strtolower(trim($name));
        // remove accents
        $s = iconv('UTF-8', 'ASCII//TRANSLIT', $s) ?: $s;
        // remove common prefixes
        $s = preg_replace('/^\b(ciudad|cd|el|la|los|las|san|santa|municipio|muni|mpio)\b\s*/u', '', $s);
        // remove punctuation and non-alphanumeric
        $s = preg_replace('/[^a-z0-9\s]/u', ' ', $s);
        // collapse spaces
        $s = preg_replace('/\s+/', ' ', $s);
        return trim($s);
    }

    /**
     * Find best municipality match from lookup (normalizedKey => Model). Returns ['model'=>Model,'score'=>float] or null.
     */
    private function findBestMunicipalityMatch(string $norm, array $lookup)
    {
        if ($norm === '') return null;
        $bestScore = 0;
        $bestModel = null;
        $lenNorm = mb_strlen($norm);
        foreach ($lookup as $key => $model) {
            // compute levenshtein on ascii versions
            $a = $norm;
            $b = $key;
            $maxLen = max(mb_strlen($a), mb_strlen($b));
            if ($maxLen === 0) continue;
            $dist = levenshtein($a, $b);
            $score = 1 - ($dist / $maxLen);
            // also consider similar_text percentage as tiebreaker
            similar_text($a, $b, $percent);
            $score = max($score, $percent / 100);
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestModel = $model;
            }
        }

        // accept only reasonable matches
        if ($bestScore >= 0.65) {
            return ['model' => $bestModel, 'score' => $bestScore];
        }
        return null;
    }
}
