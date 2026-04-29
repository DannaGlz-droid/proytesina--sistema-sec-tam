@echo off
REM Batch para eliminar notificaciones antiguas (> 30 días)
REM Ejecutado por Windows Task Scheduler a las 2:00 AM diariamente

setlocal enabledelayedexpansion

REM Crear log para verificar ejecución
set logfile=C:\Proyectos Laravel\sistema-sec-tam\logs\notifications-prune.log
if not exist "C:\Proyectos Laravel\sistema-sec-tam\logs" mkdir "C:\Proyectos Laravel\sistema-sec-tam\logs"

REM Registrar inicio
echo [%date% %time%] Iniciando poda de notificaciones... >> "!logfile!"

cd /d "C:\Proyectos Laravel\sistema-sec-tam"
"C:\xampp\php\php.exe" artisan notifications:prune --days=30 >> "!logfile!" 2>&1

REM Registrar fin
echo [%date% %time%] Poda completada >> "!logfile!"

