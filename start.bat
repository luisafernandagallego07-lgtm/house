@echo off
setlocal
cd /d "%~dp0"
where php >nul 2>nul
if errorlevel 1 (
    echo PHP no encontrado. Instala PHP 8.1+ y agregalo al PATH.
    pause
    exit /b 1
)
echo Iniciando STOCKLY en http://localhost:8000
start "" http://localhost:8000
php -S localhost:8000
