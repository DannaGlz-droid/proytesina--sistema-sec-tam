@echo off
REM Script para crear la tarea de poda de notificaciones en Windows Task Scheduler
REM Requiere permisos de administrador

setlocal enabledelayedexpansion

REM Configuración
set TASK_NAME=Laravel Auto-Delete Notifications
set SCRIPT_PATH=C:\Proyectos Laravel\sistema-sec-tam\schedule.bat
set TRIGGER_TIME=16:30
set TASK_FOLDER=\

REM Verificar si la tarea ya existe y eliminarla
echo Verificando si la tarea ya existe...
tasklist /FI "TASKNAME eq %TASK_NAME%" 2>NUL | find /I /N "%TASK_NAME%">NUL
if "%ERRORLEVEL%"=="0" (
    echo La tarea existe. Eliminándola...
    schtasks /delete /tn "%TASK_NAME%" /f >NUL 2>&1
)

REM Crear la nueva tarea
echo Creando tarea: %TASK_NAME%
schtasks /create ^
    /tn "%TASK_NAME%" ^
    /tr "cmd.exe /c \"%SCRIPT_PATH%\"" ^
    /sc daily ^
    /st %TRIGGER_TIME% ^
    /ru %USERNAME% ^
    /f

REM Verificar si se creó correctamente
if %ERRORLEVEL% EQU 0 (
    echo ✓ Tarea creada exitosamente
    echo   - Nombre: %TASK_NAME%
    echo   - Hora: %TRIGGER_TIME% (4:30 PM)
    echo   - Frecuencia: Diariamente
    echo.
    schtasks /query /tn "%TASK_NAME%" /v /fo table
) else (
    echo ✗ Error al crear la tarea. Asegúrate de ejecutar como Administrador.
    exit /b 1
)

pause
