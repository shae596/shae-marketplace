@echo off
set SHA=C:\Users\HP\Projects\shae
set TGT=C:\Users\HP\Projects\examen-php-laravel-l3

echo Copie controllers, requests, mail, views...
xcopy "%SHA%\app\Http\Controllers" "%TGT%\app\Http\Controllers\" /E /I /Y /Q
xcopy "%SHA%\app\Http\Requests" "%TGT%\app\Http\Requests\" /E /I /Y /Q
xcopy "%SHA%\app\Mail" "%TGT%\app\Mail\" /E /I /Y /Q
xcopy "%SHA%\resources\views" "%TGT%\resources\views\" /E /I /Y /Q
xcopy "%SHA%\resources\lang" "%TGT%\resources\lang\" /E /I /Y /Q
xcopy "%SHA%\docs" "%TGT%\docs\" /E /I /Y /Q

echo.
echo Termine! Lancez ensuite:
echo   cd %TGT%
echo   composer install
echo   composer require laravel/sanctum barryvdh/laravel-dompdf
echo   copy .env.example .env
echo   php artisan key:generate
echo   php artisan migrate --seed
echo   php artisan storage:link
echo   php artisan serve
pause
