@echo off
REM Script para crear tarea en Windows Task Scheduler para notificaciones
REM Configuración idéntica a Laravel Auto-Delete Reports

setlocal enabledelayedexpansion

echo.
echo ============================================================
echo Creando tarea: Laravel Notificaciones Auto-Delete
echo ============================================================
echo.

REM Verificar si la tarea ya existe
schtasks /query /tn "Laravel Notificaciones Auto-Delete" >nul 2>&1
if %errorlevel% equ 0 (
    echo Tarea existente encontrada, eliminando...
    schtasks /delete /tn "Laravel Notificaciones Auto-Delete" /f >nul 2>&1
)

REM Crear la nueva tarea (IDÉNTICA a Laravel Auto-Delete Reports)
echo Creando tarea programada...
echo.

REM Opción 1: Ejecutar CADA MINUTO (recomendado - mismo que reportes)
schtasks /create /tn "Laravel Notificaciones Auto-Delete" ^
    /tr "cmd.exe /c C:\Proyectos\ Laravel\sistema-sec-tam\schedule.bat" ^
    /sc minute ^
    /ru %USERNAME% ^
    /f

if %errorlevel% equ 0 (
    echo.
    echo ✓ Tarea creada exitosamente
    echo   - Nombre: Laravel Notificaciones Auto-Delete
    echo   - Frecuencia: Cada minuto
    echo   - Usuario: %USERNAME%
    echo   - Comando: cmd.exe /c C:\Proyectos\ Laravel\sistema-sec-tam\schedule.bat
    echo   - Directorio de inicio: (por defecto del usuario)
    echo.
    echo NOTA: Esta tarea ejecuta schedule.bat cada minuto, que a su vez
    echo       ejecuta "php artisan schedule:run"
    echo.
    echo       Laravel Scheduler (Kernel.php) luego decide QUÉ comandos
    echo       ejecutar según la hora configurada.
    echo.
) else (
    echo.
    echo ✗ Error al crear la tarea
    echo.
)

REM Mostrar los detalles de la tarea creada
echo Verificando tarea...
schtasks /query /tn "Laravel Notificaciones Auto-Delete" /fo list /v

pause
