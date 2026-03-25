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
use App\Models\FailedImportRecord;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class DeathImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB, only Excel
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

            // Preliminary pass: count occurrences of gov_folio across all sheets
            $folioCounts = [];
            $possibleFolioKeys = ['folio','folio_gob','folio_gubernamental','folio_gobierno','id','numero','nfolio','no_folio'];
            foreach ($sheets as $sheet) {
                if (empty($sheet)) continue;
                $tmp = $sheet; // keep original intact
                $headerRow = $tmp[0] ?? [];
                $headers = array_map(function ($h) {
                    return strtolower(trim(str_replace([' ', '_'], '', (string)$h)));
                }, $headerRow ?: []);
                // map index => header key
                $map = [];
                foreach ($headers as $i => $h) $map[$i] = $h;

                // iterate rows (starting at index 1)
                for ($r = 1; $r < count($tmp); $r++) {
                    $row = $tmp[$r];
                    // build assoc for this row minimally to find folio
                    $rowAssoc = [];
                    foreach ($map as $i => $key) {
                        $rowAssoc[$key] = isset($row[$i]) ? $row[$i] : null;
                    }
                    // attempt to find folio using known keys
                    $folio = null;
                    foreach ($possibleFolioKeys as $k) {
                        if (isset($rowAssoc[$k]) && trim((string)$rowAssoc[$k]) !== '') { $folio = trim((string)$rowAssoc[$k]); break; }
                    }
                    if (is_null($folio) && isset($row[0]) && trim((string)$row[0]) !== '') {
                        $folio = trim((string)$row[0]);
                    }
                    if ($folio !== null && $folio !== '') {
                        $folioCounts[$folio] = ($folioCounts[$folio] ?? 0) + 1;
                    }
                }
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

                // Validate that death date is not in the future
                if ($deathDate) {
                    try {
                        // Ensure we have a Carbon instance for comparison
                        if (!($deathDate instanceof \Carbon\Carbon)) {
                            $deathDate = Carbon::instance($deathDate);
                        }
                        $today = Carbon::today();
                        // compare date portion only
                        if ($deathDate->startOfDay()->gt($today->startOfDay())) {
                            $errors[] = 'Fecha futura: la fecha de defunción no puede ser mayor a hoy';
                        }
                    } catch (\Throwable $__e) {
                        // If anything goes wrong comparing dates, treat as invalid date
                        $errors[] = 'Fecha inválida: error al validar fecha';
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

                // If the folio is duplicated inside the uploaded file, reject ALL its occurrences
                if (!is_null($folio) && isset($folioCounts[$folio]) && $folioCounts[$folio] > 1) {
                    $rowsFailed++;
                    $rowData = array_merge(['sheet' => $causeName, 'row' => $rowNum + 2], $rowAssoc);
                    $failedRows[] = $rowData;
                    // Save to failed_import_records table for manual correction
                    FailedImportRecord::create([
                        'import_id' => $importId,
                        'original_row_data' => $rowData,
                        'error_message' => 'Folio duplicado en el archivo (todas las ocurrencias rechazadas)',
                        'status' => 'pending',
                    ]);
                    continue;
                }

                // If errors, mark failed
                if (!empty($errors)) {
                    $rowsFailed++;
                    $rowData = array_merge(['sheet' => $causeName, 'row' => $rowNum + 2], $rowAssoc);
                    $failedRows[] = $rowData;
                    // Save to failed_import_records table for manual correction
                    FailedImportRecord::create([
                        'import_id' => $importId,
                        'original_row_data' => $rowData,
                        'error_message' => implode('; ', $errors),
                        'status' => 'pending',
                    ]);
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
                    $rowData = array_merge(['sheet' => $causeName, 'row' => $rowNum + 2], $rowAssoc);
                    $failedRows[] = $rowData;
                    // Save to failed_import_records table for manual correction
                    FailedImportRecord::create([
                        'import_id' => $importId,
                        'original_row_data' => $rowData,
                        'error_message' => 'Duplicado detectado: folio existente',
                        'status' => 'pending',
                    ]);
                    continue;
                }

                // Determine normalized age fields: age_years / age_months / age_days
                // Note: DB does not currently store age_days; we validate it and keep age fields compatible.
                $ageYears = null;
                $ageMonths = null;
                $ageDays = null;
                if ($claveEdad) {
                    // Normalize unit string: trim, uppercase, remove accents and non-alphanumerics
                    $claveNorm = mb_strtoupper(trim($claveEdad));
                    $claveNormAscii = iconv('UTF-8', 'ASCII//TRANSLIT', $claveNorm) ?: $claveNorm;
                    // collapse to letters/numbers only to simplify matching (e.g. 'DÍAS' -> 'DIAS')
                    $claveNormAscii = preg_replace('/[^A-Z0-9]/', '', $claveNormAscii);

                    if (strpos($claveNormAscii, 'MES') !== false) {
                        $ageYears = 0;
                        $ageMonths = is_null($age) ? null : (int)$age;
                    } elseif (strpos($claveNormAscii, 'DIA') !== false || strpos($claveNormAscii, 'DIAS') !== false) {
                        // Unit in days: validate days but DB doesn't store separate days field.
                        $ageYears = 0;
                        $ageMonths = 0;
                        $ageDays = is_null($age) ? null : (int)$age;
                    } elseif (strpos($claveNormAscii, 'ANO') !== false || strpos($claveNormAscii, 'ANOS') !== false || strpos($claveNormAscii, 'A') === 0) {
                        $ageYears = is_null($age) ? null : (int)$age;
                        $ageMonths = null;
                    } else {
                        // Unknown unit: keep as years by default (backwards-compatible)
                        $ageYears = is_null($age) ? null : (int)$age;
                        $ageMonths = null;
                    }
                } else {
                    // No unit provided: keep existing behavior (treat as years)
                    $ageYears = is_null($age) ? null : (int)$age;
                    $ageMonths = null;
                }
                // Validate months
                if (!is_null($ageMonths)) {
                    if ($ageMonths < 0) {
                        $rowsFailed++;
                        $rowData = array_merge(['sheet' => $causeName, 'row' => $rowNum + 2], $rowAssoc);
                        $failedRows[] = $rowData;
                        FailedImportRecord::create([
                            'import_id' => $importId,
                            'original_row_data' => $rowData,
                            'error_message' => 'Edad inválida: meses debe ser mayor o igual a 0',
                            'status' => 'pending',
                        ]);
                        continue;
                    }
                    if ($ageMonths >= 12) {
                        $rowsFailed++;
                        $rowData = array_merge(['sheet' => $causeName, 'row' => $rowNum + 2], $rowAssoc);
                        $failedRows[] = $rowData;
                        FailedImportRecord::create([
                            'import_id' => $importId,
                            'original_row_data' => $rowData,
                            'error_message' => 'Edad inválida: meses debe ser menor a 12',
                            'status' => 'pending',
                        ]);
                        continue;
                    }
                }

                // Validate days (we don't persist days separately yet) — require 0..30 so it doesn't roll into a month
                if (!is_null($ageDays)) {
                    if ($ageDays < 0) {
                        $rowsFailed++;
                        $rowData = array_merge(['sheet' => $causeName, 'row' => $rowNum + 2], $rowAssoc);
                        $failedRows[] = $rowData;
                        FailedImportRecord::create([
                            'import_id' => $importId,
                            'original_row_data' => $rowData,
                            'error_message' => 'Edad inválida: días debe ser mayor o igual a 0',
                            'status' => 'pending',
                        ]);
                        continue;
                    }
                    if ($ageDays > 30) {
                        $rowsFailed++;
                        $rowData = array_merge(['sheet' => $causeName, 'row' => $rowNum + 2], $rowAssoc);
                        $failedRows[] = $rowData;
                        FailedImportRecord::create([
                            'import_id' => $importId,
                            'original_row_data' => $rowData,
                            'error_message' => 'Edad inválida: días debe ser menor o igual a 30',
                            'status' => 'pending',
                        ]);
                        continue;
                    }
                }

                // Validate years maximum and non-negative
                if (!is_null($ageYears)) {
                    if ($ageYears < 0) {
                        $rowsFailed++;
                        $rowData = array_merge(['sheet' => $causeName, 'row' => $rowNum + 2], $rowAssoc);
                        $failedRows[] = $rowData;
                        FailedImportRecord::create([
                            'import_id' => $importId,
                            'original_row_data' => $rowData,
                            'error_message' => 'Edad inválida: años debe ser mayor o igual a 0',
                            'status' => 'pending',
                        ]);
                        continue;
                    }
                    if ($ageYears > 150) {
                        $rowsFailed++;
                        $rowData = array_merge(['sheet' => $causeName, 'row' => $rowNum + 2], $rowAssoc);
                        $failedRows[] = $rowData;
                        FailedImportRecord::create([
                            'import_id' => $importId,
                            'original_row_data' => $rowData,
                            'error_message' => 'Edad inválida: años debe ser menor o igual a 150',
                            'status' => 'pending',
                        ]);
                        continue;
                    }
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
                // Persist days when provided (we added age_days column)
                if (!is_null($ageDays)) {
                    $d->age_days = $ageDays;
                } else {
                    $d->age_days = null;
                }
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
                // Track which import batch this record came from
                $d->import_id = $importId;
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
     * Reverse/undo an import: delete all deaths that were imported in this batch
     * and mark the import as reversed, tracking who did it and when
     */
    public function reverseImport(Request $request, $importId)
    {
        try {
            $user = $request->user();

            // Only admins can reverse imports
            if (!$user) {
                return response()->json(['ok' => false, 'message' => 'No autenticado'], 403);
            }
            
            // El rol puede ser un objeto o un string, obtener el nombre
            $roleName = is_object($user->role) ? $user->role->name : $user->role;
            
            \Log::info("Intento de reversión - Usuario: {$user->name}, Rol: {$roleName}");
            
            if ($roleName !== 'Administrador') {
                return response()->json(['ok' => false, 'message' => "No tienes permiso. Tu rol es: '{$roleName}' (se requiere 'Administrador')"], 403);
            }

            // Get import record
            $import = DB::table('imports')->where('id', $importId)->first();
            if (!$import) {
                return response()->json(['ok' => false, 'message' => 'Importación no encontrada'], 404);
            }

            // Check if already reversed
            if ($import->is_reversed) {
                return response()->json(['ok' => false, 'message' => 'Esta importación ya fue revertida'], 400);
            }

            // Start transaction
            DB::beginTransaction();

            try {
                // Get all deaths imported with this import_id
                // We assume Death model has import_id field to track source
                $deathsToDelete = Death::where('import_id', $importId)->get();
                $deathCount = $deathsToDelete->count();

                // Delete all deaths from this import
                Death::where('import_id', $importId)->delete();

                // Mark import as reversed
                DB::table('imports')->where('id', $importId)->update([
                    'is_reversed' => true,
                    'reversed_at' => now(),
                    'reversed_by_user_id' => $user->id,
                    'updated_at' => now(),
                ]);

                DB::commit();

                Log::info("Import reversed successfully", [
                    'import_id' => $importId,
                    'reversed_by' => $user->id,
                    'deaths_deleted' => $deathCount,
                ]);

                return response()->json([
                    'ok' => true,
                    'message' => "Importación revertida correctamente. Se eliminaron {$deathCount} registros.",
                    'deaths_deleted' => $deathCount,
                ]);

            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error("Failed to reverse import: " . $e->getMessage(), ['import_id' => $importId]);
                return response()->json(['ok' => false, 'message' => 'Error al revertir: ' . $e->getMessage()], 500);
            }

        } catch (\Throwable $e) {
            Log::error("Reverse import error: " . $e->getMessage());
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get list of all imports with their reversal status
     */
    public function getImportHistory()
    {
        try {
            $imports = DB::table('imports')
                ->leftJoin('users as creator', 'imports.user_id', '=', 'creator.id')
                ->leftJoin('users as reverser', 'imports.reversed_by_user_id', '=', 'reverser.id')
                ->select([
                    'imports.id',
                    'imports.original_name',
                    'imports.status',
                    'imports.rows_total',
                    'imports.rows_imported',
                    'imports.rows_failed',
                    'imports.is_reversed',
                    'imports.created_at',
                    'imports.updated_at',
                    'imports.reversed_at',
                    'creator.name as created_by',
                    'reverser.name as reversed_by',
                ])
                ->orderBy('imports.created_at', 'desc')
                ->paginate(25);

            return response()->json(['ok' => true, 'data' => $imports]);
        } catch (\Throwable $e) {
            Log::error("Error fetching import history: " . $e->getMessage());
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get failed records for a specific import, with pagination
     */
    public function getFailedRecords($importId)
    {
        try {
            $failedRecords = FailedImportRecord::where('import_id', $importId)
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json(['ok' => true, 'data' => $failedRecords]);
        } catch (\Throwable $e) {
            Log::error("Error fetching failed records: " . $e->getMessage());
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Save corrections to a failed record
     */
    public function saveFailedRecordCorrection(Request $request, $recordId)
    {
        try {
            $request->validate([
                'corrected_data' => 'required|array',
            ]);

            $failedRecord = FailedImportRecord::findOrFail($recordId);

            // Update with corrected data
            $failedRecord->corrected_data = $request->input('corrected_data');
            $failedRecord->status = 'corrected';
            $failedRecord->save();

            return response()->json([
                'ok' => true,
                'message' => 'Correcciones guardadas exitosamente',
                'data' => $failedRecord,
            ]);
        } catch (\Throwable $e) {
            Log::error("Error saving correction: " . $e->getMessage());
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Retry a failed record with corrected data
     */
    public function retryFailedRecord(Request $request, $recordId)
    {
        try {
            $failedRecord = FailedImportRecord::findOrFail($recordId);

            if ($failedRecord->status === 'discarded') {
                return response()->json(['ok' => false, 'message' => 'Este registro fue descartado y no puede ser reintentado'], 400);
            }

            // Use corrected data if available, otherwise use original
            $rowData = $failedRecord->corrected_data ?? $failedRecord->original_row_data;

            // Validate and import the corrected record
            $errors = [];

            $name = trim((string)($rowData['nombre'] ?? '')) ?: null;
            $first = trim((string)(($rowData['primerapellido'] ?? $rowData['primerapellid'] ?? ''))) ?: null;
            
            // Extract folio - try multiple possible keys and be flexible
            $folio = null;
            $possibleFolioKeys = ['folio', 'folio_gob', 'folio_gubernamental', 'folio_gobierno', 'id', 'numero', 'nfolio', 'no_folio'];
            foreach ($possibleFolioKeys as $k) {
                if (isset($rowData[$k]) && !is_null($rowData[$k]) && trim((string)$rowData[$k]) !== '') { 
                    $folio = trim((string)$rowData[$k]); 
                    break; 
                }
            }

            // Additional cleanup: remove any non-numeric characters if any slipped through
            if ($folio) {
                $folioClean = preg_replace('/[^0-9]/', '', $folio);
                if (strlen($folioClean) === 9) {
                    $folio = $folioClean;
                }
            }

            // Validate basic required fields
            if (!$name) $errors[] = 'Nombre vacío';
            if (!$first) $errors[] = 'Primer apellido vacío';
            if (is_null($folio) || !preg_match('/^[0-9]{9}$/', (string)$folio)) {
                $errors[] = 'Folio gubernamental inválido o ausente (se requieren 9 dígitos). Folio recibido: ' . ($folio ?? 'vacío');
            }
            
            // Validate age is present (required field)
            $age = isset($rowData['edad_valor']) ? (int)$rowData['edad_valor'] : (isset($rowData['edad']) ? (int)$rowData['edad'] : null);
            if (is_null($age)) {
                $errors[] = 'Edad vacía o inválida';
            }

            // Validate sex is present and valid
            $sex = isset($rowData['sexod']) ? strtoupper(trim((string)$rowData['sexod'])) : null;
            // Accept both M/F and HOMBRE/MUJER
            if ($sex === 'HOMBRE') $sex = 'M';
            elseif ($sex === 'MUJER') $sex = 'F';
            
            if (!$sex || !in_array($sex, ['M', 'F'])) {
                $errors[] = 'Sexo inválido o ausente (debe ser M, F, HOMBRE o MUJER)';
            }

            // Check if folio already exists in database
            if (!empty($folio) && Death::where('gov_folio', $folio)->exists()) {
                $errors[] = 'Folio duplicado detectado en la base de datos';
            }

            if (!empty($errors)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'El registro aún tiene errores de validación',
                    'errors' => $errors,
                ], 422);
            }

            // Import the corrected record following the import() logic
            // For now, we'll create a simplified death record
            $dateRaw = $rowData['fechadefuncion'] ?? null;
            $deathDate = null;
            if ($dateRaw) {
                try {
                    if (is_numeric($dateRaw)) {
                        $deathDate = ExcelDate::excelToDateTimeObject($dateRaw);
                    } else {
                        $deathDate = Carbon::parse($dateRaw);
                    }
                    if (!($deathDate instanceof \Carbon\Carbon)) {
                        $deathDate = Carbon::instance($deathDate);
                    }
                } catch (\Throwable $e) {
                    return response()->json(['ok' => false, 'message' => 'Fecha inválida: ' . $dateRaw], 422);
                }
            }

            // Validate date is not in future
            if ($deathDate && $deathDate->startOfDay()->gt(Carbon::today()->startOfDay())) {
                return response()->json(['ok' => false, 'message' => 'La fecha de defunción no puede ser mayor a hoy'], 422);
            }

            // Check if folio already exists (excluding the current failed record if it was partially created)
            if (!empty($folio)) {
                $existingDeath = Death::where('gov_folio', $folio)
                    ->where('id', '!=', $failedRecord->id)
                    ->exists();
                if ($existingDeath) {
                    $errors[] = 'El folio ya existe en la base de datos';
                }
            }

            if (!empty($errors)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'El registro aún tiene errores de validación',
                    'errors' => $errors,
                ], 422);
            }

            DB::beginTransaction();

            try {
                // Create death record with full validation
                $death = new Death();
                $death->import_id = $failedRecord->import_id;
                $death->name = $name;
                $death->gov_folio = (string)$folio;
                $death->first_last_name = $first;
                $death->second_last_name = trim((string)(($rowData['segundoapellido'] ?? $rowData['segundoapellid'] ?? ''))) ?: null;
                
                // Handle age fields - check both 'edad' (original) and 'edad_valor' (corrected form)
                // Age was already validated to not be null above
                $death->age = $age;
                $death->age_years = $age;
                
                $sex = isset($rowData['sexod']) ? strtoupper(trim((string)$rowData['sexod'])) : null;
                if ($sex && in_array($sex, ['F','FEM','FEMENINO','MUJER'])) $sex = 'F';
                elseif ($sex && in_array($sex, ['M','MAS','MASC','MASCULINO','HOMBRE'])) $sex = 'M';
                // Sex is already validated above, so this should always be M or F
                $death->sex = $sex;
                
                $death->death_date = $deathDate ? $deathDate->format('Y-m-d') : null;
                
                // Try to find location if provided, otherwise use default
                $siteName = trim((string)($rowData['sitiodefunciond'] ?? '')) ?: null;
                $deathLocation = null;
                if ($siteName) {
                    $deathLocation = DeathLocation::where('name', $siteName)->first();
                    if (!$deathLocation) {
                        $deathLocation = DeathLocation::firstOrCreate(['name' => $siteName]);
                    }
                } else {
                    $deathLocation = DeathLocation::firstOrCreate(['name' => 'NO ESPECIFICADO']);
                }
                $death->death_location_id = $deathLocation->id;
                
                // Try to find cause using the sheet name (like the main import does)
                // Sheet name was stored as 'sheet' key in original data
                $sheetCauseName = trim((string)($rowData['sheet'] ?? '')) ?: null;
                
                // If no sheet name, try to use code or full description
                $causeCode = trim((string)($rowData['ciecausabasica'] ?? '')) ?: null;
                $causeName = trim((string)($rowData['causa'] ?? $rowData['ciecausabasicad'] ?? '')) ?: null;
                
                $deathCause = null;
                
                // First, try to find by sheet name (like main import does)
                if ($sheetCauseName && $sheetCauseName !== 'Causa ') {
                    $deathCause = DeathCause::where('name', 'like', '%' . trim($sheetCauseName) . '%')->first();
                }
                
                // If not found by sheet name, try by code
                if (!$deathCause && $causeCode) {
                    $deathCause = DeathCause::where('code', $causeCode)->first();
                }
                
                // If not found by code, try by full name description
                if (!$deathCause && $causeName) {
                    $deathCause = DeathCause::where('name', 'like', '%' . $causeName . '%')->first();
                }
                
                // If still not found, create one with sheet name if available
                if (!$deathCause) {
                    $createName = $sheetCauseName ?: ($causeName ?: 'NO ESPECIFICADA');
                    $deathCause = DeathCause::firstOrCreate(
                        ['name' => $createName],
                        ['code' => $causeCode]
                    );
                }
                
                $death->death_cause_id = $deathCause->id;
                
                // Set municipalities (try to find them, use OTRO as default)
                $residenceMunicipalityName = trim((string)($rowData['municipioresidenciad'] ?? '')) ?: null;
                $deathMunicipalityName = trim((string)($rowData['municipiodefunciond'] ?? '')) ?: null;
                $otherJur = Jurisdiction::firstOrCreate(['name' => 'OTRO']);
                $otherMuni = Municipality::firstOrCreate(['name' => 'OTRO'], ['jurisdiction_id' => $otherJur->id]);
                
                if ($residenceMunicipalityName) {
                    $residenceMuni = Municipality::where('name', 'like', '%' . $residenceMunicipalityName . '%')->first();
                    $death->residence_municipality_id = $residenceMuni ? $residenceMuni->id : $otherMuni->id;
                } else {
                    $death->residence_municipality_id = $otherMuni->id;
                }
                
                if ($deathMunicipalityName) {
                    $deathMuni = Municipality::where('name', 'like', '%' . $deathMunicipalityName . '%')->first();
                    $death->death_municipality_id = $deathMuni ? $deathMuni->id : $otherMuni->id;
                } else {
                    $death->death_municipality_id = $otherMuni->id;
                }
                
                $death->jurisdiction_id = $otherJur->id;
                $death->import_id = $failedRecord->import_id;
                $death->save();

                // Mark the failed record as imported
                $failedRecord->status = 'imported';
                $failedRecord->save();

                // Update import stats
                $import = DB::table('imports')->where('id', $failedRecord->import_id)->first();
                if ($import) {
                    DB::table('imports')->where('id', $failedRecord->import_id)->update([
                        'rows_imported' => $import->rows_imported + 1,
                        'rows_failed' => max(0, $import->rows_failed - 1),
                        'updated_at' => now(),
                    ]);
                }

                DB::commit();

                Log::info("Failed record imported successfully", [
                    'failed_record_id' => $recordId,
                    'import_id' => $failedRecord->import_id,
                ]);

                return response()->json([
                    'ok' => true,
                    'message' => 'Registro importado exitosamente',
                    'data' => $death,
                ]);
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error("Error saving corrected record: " . $e->getMessage());
                return response()->json(['ok' => false, 'message' => 'Error al guardar: ' . $e->getMessage()], 500);
            }
        } catch (\Throwable $e) {
            Log::error("Error retrying failed record: " . $e->getMessage());
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Discard a failed record
     */
    public function discardFailedRecord($recordId)
    {
        try {
            $failedRecord = FailedImportRecord::findOrFail($recordId);
            $failedRecord->status = 'discarded';
            $failedRecord->save();

            // Update import stats: decrement rows_failed
            $import = DB::table('imports')->where('id', $failedRecord->import_id)->first();
            if ($import) {
                DB::table('imports')->where('id', $failedRecord->import_id)->update([
                    'rows_failed' => max(0, $import->rows_failed - 1),
                    'updated_at' => now(),
                ]);
            }

            return response()->json([
                'ok' => true,
                'message' => 'Registro descartado',
            ]);
        } catch (\Throwable $e) {
            Log::error("Error discarding failed record: " . $e->getMessage());
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
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
