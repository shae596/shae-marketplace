@echo off
chcp 65001 >nul
echo.
echo ========================================
echo   SHAE - Afficher le site sur Render
echo ========================================
echo.
echo ETAPE 1 - Pousser le code (si pas deja fait)
echo   git push origin main
echo.
echo ETAPE 2 - Sur https://dashboard.render.com
echo   Ouvrez le service "shae" ^> Environment
echo.
echo   Mettez EXACTEMENT ces valeurs :
echo.
echo   DB_HOST          = localhost
echo   DB_USERNAME      = root
echo   DB_PASSWORD      = (VIDE - supprimez tout)
echo   DB_DATABASE      = shae
echo   DB_CONNECTION    = mysql
echo   SESSION_DRIVER   = file
echo   CACHE_STORE      = file
echo   APP_URL          = https://shae.onrender.com
echo   LABPAY_CALLBACK_URL = https://shae.onrender.com/payments/callback
echo   LABPAY_API_KEY   = (votre token Labyrinthe)
echo.
echo ETAPE 3 - Cliquez "Save, rebuild, and deploy"
echo.
echo ETAPE 4 - Attendez "Live" (5-15 min)
echo.
echo ETAPE 5 - Testez dans le navigateur :
echo   https://shae.onrender.com/login
echo.
echo   Si ca marche : page de connexion SHAE (pas "500")
echo.
pause
start https://dashboard.render.com
