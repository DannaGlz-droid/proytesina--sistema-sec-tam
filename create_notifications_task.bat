@echo off
REM Script para crear tarea en Windows Task Scheduler para eliminación de notificaciones

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

REM Crear la nueva tarea
echo Creando tarea programada para las 2:00 AM diariamente...
schtasks /create /tn "Laravel Notificaciones Auto-Delete" ^
    /tr "cmd.exe /c \"C:\Proyectos Laravel\sistema-sec-tam\notifications-schedule.bat\"" ^
    /sc daily ^
    /st 02:00 ^
    /ru %USERNAME% ^
    /f

if %errorlevel% equ 0 (
    echo.
    echo ✓ Tarea creada exitosamente
    echo   - Nombre: Laravel Notificaciones Auto-Delete
    echo   - Hora: 02:00 AM
    echo   - Frecuencia: Diariamente
    echo   - Usuario: %USERNAME%
    echo   - Comando: C:\Proyectos Laravel\sistema-sec-tam\notifications-schedule.bat
    echo.
) else (
    echo.
    echo ✗ Error al crear la tarea
    echo   (Es posible que necesites ejecutar como Administrador)
    echo.
)

REM Mostrar los detalles de la tarea creada
echo Verificando tarea...
schtasks /query /tn "Laravel Notificaciones Auto-Delete" /fo list /v

pause
