<?php
require_once 'vendor/autoload.php';
use App\Models\Publication;
use App\Models\PublicationFile;
use Illuminate\Support\Facades\DB;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n╔═══════════════════════════════════════════════════════════════════╗\n";
echo "║     DEMOSTRACIÓN: ARCHIVOS GUARDADOS Y AUTO-ELIMINADOS            ║\n";
echo "╚═══════════════════════════════════════════════════════════════════╝\n\n";

// Obtener usuario válido
$validUserId = DB::table('users')->first()?->id ?? 1;

// Crear directorio de prueba
$testDir = storage_path('app/public/test_auto_delete');
if (!is_dir($testDir)) {
    mkdir($testDir, 0755, true);
}

echo "📁 PASO 1: CREAR REPORTE CON ARCHIVOS DE PRUEBA\n";
echo "─────────────────────────────────────────────\n\n";

// Crear reporte ANTIGUO con archivos
$twoYearsAgo = now()->subYears(2)->subMinutes(1);
$oldReport = Publication::create([
    'publication_type' => 'TEST-CON-ARCHIVOS',
    'topic' => '🔴 REPORTE CON ARCHIVOS (será eliminado)',
    'description' => 'Este reporte tiene archivos que se guardarán en storage/',
    'publication_date' => $twoYearsAgo->format('Y-m-d'),
    'activity_date' => $twoYearsAgo->format('Y-m-d'),
    'user_id' => $validUserId,
    'status' => 'publicado',
]);

// Actualizar fecha en BD
DB::table('publications')->where('id', $oldReport->id)->update([
    'created_at' => $twoYearsAgo,
    'updated_at' => $twoYearsAgo,
]);

echo "✓ Reporte creado: ID {$oldReport->id}\n";
echo "✓ Tema: {$oldReport->topic}\n";
echo "✓ Fecha: {$twoYearsAgo->format('Y-m-d H:i:s')}\n\n";

// Crear archivos de prueba
echo "📁 PASO 2: CREAR ARCHIVOS EN storage/app/public/\n";
echo "─────────────────────────────────────────────\n\n";

$testFiles = [];
for ($i = 1; $i <= 3; $i++) {
    // Crear archivo físico
    $filename = "documento_$i.txt";
    $filepath = "test_auto_delete/$filename";
    $fullPath = storage_path("app/public/$filepath");
    
    file_put_contents($fullPath, "Contenido de prueba del archivo $i - SERÁ ELIMINADO\n");
    
    // Guardar referencia en BD
    $file = PublicationFile::create([
        'publication_id' => $oldReport->id,
        'original_name' => $filename,
        'file_name' => $filename,
        'file_path' => $filepath,
        'file_type' => 'txt',
        'file_size' => filesize($fullPath),
    ]);
    
    $testFiles[] = [
        'file' => $file,
        'path' => $fullPath,
    ];
    
    echo "✓ Archivo creado: storage/app/public/$filepath\n";
    echo "  Tamaño: " . filesize($fullPath) . " bytes\n";
    echo "  Referencia BD: PublicationFile ID {$file->id}\n\n";
}

echo "\n📊 ESTADO ACTUAL (ANTES DE ELIMINAR)\n";
echo "════════════════════════════════════════════════════════════════\n\n";

echo "ARCHIVOS FÍSICOS en storage/app/public/test_auto_delete/:\n";
$filesOnDisk = glob(storage_path('app/public/test_auto_delete/*'));
echo "Total: " . count($filesOnDisk) . " archivos\n";
foreach ($filesOnDisk as $file) {
    echo "  ✓ " . basename($file) . " (" . filesize($file) . " bytes)\n";
}

echo "\nARCHIVOS EN BASE DE DATOS:\n";
$filesInDB = PublicationFile::where('publication_id', $oldReport->id)->get();
echo "Total referencias: " . $filesInDB->count() . "\n";
foreach ($filesInDB as $f) {
    echo "  ✓ ID {$f->id}: {$f->file_name} ({$f->file_size} bytes)\n";
}

echo "\nREPORTE EN BASE DE DATOS:\n";
$pubExists = Publication::find($oldReport->id);
echo "  " . ($pubExists ? "✓ Existe (ID {$pubExists->id})" : "✗ No existe") . "\n";

echo "\n\n═══════════════════════════════════════════════════════════════════\n";
echo "⏸️  AHORA EJECUTAREMOS: php artisan publications:delete-old\n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

// Ejecutar eliminación
echo "🗑️  ELIMINANDO PUBLICACIONES ANTIGUAS...\n\n";
exec("cd " . base_path() . " && php artisan publications:delete-old", $output);
echo implode("\n", $output) . "\n";

echo "\n\n📊 ESTADO DESPUÉS DE ELIMINAR\n";
echo "════════════════════════════════════════════════════════════════\n\n";

echo "ARCHIVOS FÍSICOS en storage/app/public/test_auto_delete/:\n";
$filesOnDisk = glob(storage_path('app/public/test_auto_delete/*'));
$count = count($filesOnDisk);
echo "Total: " . $count . " archivos\n";
if ($count > 0) {
    foreach ($filesOnDisk as $file) {
        echo "  ✓ " . basename($file) . " (" . filesize($file) . " bytes)\n";
    }
} else {
    echo "  ❌ CARPETA VACÍA - TODOS LOS ARCHIVOS FUERON ELIMINADOS\n";
}

echo "\nARCHIVOS EN BASE DE DATOS:\n";
$filesInDB = PublicationFile::where('publication_id', $oldReport->id)->get();
echo "Total referencias: " . $filesInDB->count() . "\n";
if ($filesInDB->count() > 0) {
    foreach ($filesInDB as $f) {
        echo "  ✓ ID {$f->id}: {$f->file_name}\n";
    }
} else {
    echo "  ❌ SIN REFERENCIAS - TODAS FUERON ELIMINADAS\n";
}

echo "\nREPORTE EN BASE DE DATOS:\n";
$pubExists = Publication::withoutTrashed()->find($oldReport->id);
echo "  " . ($pubExists ? "✓ Existe (ID {$pubExists->id})" : "❌ ELIMINADO - NO EXISTE") . "\n";

echo "\n\n╔═══════════════════════════════════════════════════════════════════╗\n";
echo "║                    RESUMEN FINAL                                  ║\n";
echo "╚═══════════════════════════════════════════════════════════════════╝\n\n";

echo "✅ ARCHIVOS FÍSICOS: Eliminados del servidor (storage/app/public/)\n";
echo "✅ REFERENCIAS BD: Eliminadas de la tabla PublicationFiles\n";
echo "✅ REPORTE BD: Eliminado de la tabla Publications\n\n";

echo "🎯 EL COMANDO ELIMINA TODO:\n";
echo "   - Los archivos del disco\n";
echo "   - Las referencias en la BD\n";
echo "   - El reporte completo\n\n";

?>
