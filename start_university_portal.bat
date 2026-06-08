@echo off
setlocal

title University Portal Launcher
cd /d "%~dp0"

set "XAMPP_DIR=C:\xampp"
set "PHP_EXE=%XAMPP_DIR%\php\php.exe"
set "MYSQL_EXE=%XAMPP_DIR%\mysql\bin\mysql.exe"
set "MYSQLADMIN_EXE=%XAMPP_DIR%\mysql\bin\mysqladmin.exe"
set "MYSQL_START=%XAMPP_DIR%\mysql_start.bat"
set "SQL_FILE=%~dp0university_portal.sql"
set "DB_NAME=university_portal"
set "DB_HOST=127.0.0.1"
set "DB_PORT=3306"

echo.
echo ==============================
echo   University Portal Launcher
echo ==============================
echo.

if not exist "%PHP_EXE%" (
    echo PHP was not found at "%PHP_EXE%".
    echo Please install XAMPP in C:\xampp or edit XAMPP_DIR in this file.
    pause
    exit /b 1
)

if not exist "%MYSQL_EXE%" (
    echo MySQL was not found at "%MYSQL_EXE%".
    echo Please install XAMPP in C:\xampp or edit XAMPP_DIR in this file.
    pause
    exit /b 1
)

if not exist "%SQL_FILE%" (
    echo Database file not found:
    echo "%SQL_FILE%"
    echo Keep university_portal.sql in the same folder as this launcher.
    pause
    exit /b 1
)

echo Checking MySQL...
"%MYSQLADMIN_EXE%" --user=root --host=%DB_HOST% --port=%DB_PORT% ping >nul 2>&1
if errorlevel 1 (
    echo MySQL is not running. Starting XAMPP MySQL...
    if exist "%MYSQL_START%" (
        start "XAMPP MySQL" /min "%MYSQL_START%"
    ) else (
        echo Could not find "%MYSQL_START%".
        echo Start MySQL from XAMPP Control Panel, then run this file again.
        pause
        exit /b 1
    )

    for /l %%I in (1,1,30) do (
        "%MYSQLADMIN_EXE%" --user=root --host=%DB_HOST% --port=%DB_PORT% ping >nul 2>&1
        if not errorlevel 1 goto mysql_ready
        timeout /t 1 /nobreak >nul
    )

    echo MySQL did not start within 30 seconds.
    echo Start MySQL from XAMPP Control Panel, then run this file again.
    pause
    exit /b 1
)

:mysql_ready
echo MySQL is ready.

set "DB_EXISTS="
for /f "usebackq tokens=*" %%D in (`"%MYSQL_EXE%" --user=root --host=%DB_HOST% --port=%DB_PORT% --batch --skip-column-names -e "SHOW DATABASES LIKE '%DB_NAME%';" 2^>nul`) do set "DB_EXISTS=%%D"

if "%DB_EXISTS%"=="%DB_NAME%" (
    echo.
    echo Database "%DB_NAME%" already exists.
    set /p "IMPORT_DB=Re-import university_portal.sql and refresh the sample database? [y/N]: "
    if /i "%IMPORT_DB%"=="y" goto import_database
    echo Keeping existing database.
    goto start_server
)

:import_database
echo.
echo Importing %DB_NAME% from university_portal.sql...
"%MYSQL_EXE%" --user=root --host=%DB_HOST% --port=%DB_PORT% < "%SQL_FILE%"
if errorlevel 1 (
    echo Database import failed.
    pause
    exit /b 1
)
echo Database import completed.

:start_server
set "PORT="
for /f %%P in ('powershell -NoProfile -Command "foreach($p in 8080..8085){$c=Get-NetTCPConnection -LocalAddress 127.0.0.1 -LocalPort $p -State Listen -ErrorAction SilentlyContinue; if(-not $c){$p; break}}"') do set "PORT=%%P"

if "%PORT%"=="" (
    echo No free port found between 8080 and 8085.
    pause
    exit /b 1
)

echo.
echo Starting PHP server on http://127.0.0.1:%PORT%/index.php
start "University Portal Server" /min "%PHP_EXE%" -S 127.0.0.1:%PORT% -t "%~dp0"

timeout /t 2 /nobreak >nul
start "" "http://127.0.0.1:%PORT%/index.php"

echo.
echo Done. Keep the PHP server window open while using the project.
echo Login password for all demo accounts: 12345
echo.
pause
