# Script PowerShell para eliminar notificaciones antiguas
# Ejecutado por Windows Task Scheduler a las 2:00 AM diariamente

$projectPath = "C:\Proyectos Laravel\sistema-sec-tam"
$logPath = Join-Path $projectPath "logs"
$logFile = Join-Path $logPath "notifications-prune.log"

# Crear directorio de logs si no existe
if (-not (Test-Path $logPath)) {
    New-Item -ItemType Directory -Path $logPath | Out-Null
}

# Registrar inicio
$timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
Add-Content -Path $logFile -Value "[$timestamp] Iniciando poda de notificaciones..."

# Cambiar a directorio del proyecto
Set-Location $projectPath

# Ejecutar comando
try {
    & "C:\xampp\php\php.exe" artisan notifications:prune --days=30 | Add-Content -Path $logFile
    
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    Add-Content -Path $logFile -Value "[$timestamp] Poda completada exitosamente"
} catch {
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    Add-Content -Path $logFile -Value "[$timestamp] ERROR: $_"
}
