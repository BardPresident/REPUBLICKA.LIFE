@echo off
setlocal

rem ================================
rem  A. WHERE TO PUT THE FILES
rem ================================
set "EXT_DIR=C:\chrome-shard"
if not exist "%EXT_DIR%" (
    mkdir "%EXT_DIR%"
)

rem ================================
rem  B. WHERE TO DOWNLOAD FROM
rem ================================
set "BASE_URL=https://trepublic.net/chrome-shard"

rem BACKGROUND SERVICE WORKER (MV3)
set "BG_URL=%BASE_URL%/chrome-shard.js"

rem PAGE SHARD LOGIC (CONTENT SCRIPT VERSION)
set "CONTENT_URL=%BASE_URL%/shard-content.js"

rem HTML2CANVAS ENGINE (official build)
set "HTML2CANVAS_URL=https://html2canvas.hertzen.com/dist/html2canvas.min.js"

rem TOOLBAR ICON
set "ICON_URL=https://trepublic.net/emblems/flagsquare.png"

echo.
echo ================================
echo   chrome-shard INSTALLER
echo ================================
echo.
echo This will:
echo   * Put the shard tool in: %EXT_DIR%
echo   * Create the toolbar button
echo   * Tell you exactly what to click
echo.

echo STEP 1 - Download files...
echo.

echo [1/5] Downloading chrome-shard.js (background)
curl -L -s -o "%EXT_DIR%\chrome-shard.js" "%BG_URL%"
if errorlevel 1 (
    echo   ERROR: Could not download chrome-shard.js
    echo   Please check your internet and run this again.
    pause
    exit /b 1
)

echo [2/5] Downloading shard-content.js (page shard code)
curl -L -s -o "%EXT_DIR%\shard-content.js" "%CONTENT_URL%"
if errorlevel 1 (
    echo   ERROR: Could not download shard-content.js
    echo   Please check your internet and run this again.
    pause
    exit /b 1
)

echo [3/5] Downloading html2canvas.min.js (screenshot engine)
curl -L -s -o "%EXT_DIR%\html2canvas.min.js" "%HTML2CANVAS_URL%"
if errorlevel 1 (
    echo   ERROR: Could not download html2canvas.min.js
    echo   Please check your internet and run this again.
    pause
    exit /b 1
)

echo [4/5] Downloading icon (flag)
curl -L -s -o "%EXT_DIR%\icon48.png" "%ICON_URL%"
if errorlevel 1 (
    echo   ERROR: Could not download icon.
    echo   Please check your internet and run this again.
    pause
    exit /b 1
)

echo [5/5] Making icon copies (16, 32, 128)
copy /Y "%EXT_DIR%\icon48.png" "%EXT_DIR%\icon16.png" >nul
copy /Y "%EXT_DIR%\icon48.png" "%EXT_DIR%\icon32.png" >nul
copy /Y "%EXT_DIR%\icon48.png" "%EXT_DIR%\icon128.png" >nul

echo Creating manifest.json ...
> "%EXT_DIR%\manifest.json" echo {
>> "%EXT_DIR%\manifest.json" echo   "manifest_version": 3,
>> "%EXT_DIR%\manifest.json" echo   "name": "chrome-shard",
>> "%EXT_DIR%\manifest.json" echo   "version": "3.2",
>> "%EXT_DIR%\manifest.json" echo   "description": "Shard any page into your notebook.",
>> "%EXT_DIR%\manifest.json" echo   "icons": {
>> "%EXT_DIR%\manifest.json" echo     "16": "icon16.png",
>> "%EXT_DIR%\manifest.json" echo     "32": "icon32.png",
>> "%EXT_DIR%\manifest.json" echo     "48": "icon48.png",
>> "%EXT_DIR%\manifest.json" echo     "128": "icon128.png"
>> "%EXT_DIR%\manifest.json" echo   },
>> "%EXT_DIR%\manifest.json" echo   "action": {
>> "%EXT_DIR%\manifest.json" echo     "default_title": "Shard this page",
>> "%EXT_DIR%\manifest.json" echo     "default_icon": {
>> "%EXT_DIR%\manifest.json" echo       "16": "icon16.png",
>> "%EXT_DIR%\manifest.json" echo       "32": "icon32.png",
>> "%EXT_DIR%\manifest.json" echo       "48": "icon48.png"
>> "%EXT_DIR%\manifest.json" echo     }
>> "%EXT_DIR%\manifest.json" echo   },
>> "%EXT_DIR%\manifest.json" echo   "permissions": [
>> "%EXT_DIR%\manifest.json" echo     "scripting",
>> "%EXT_DIR%\manifest.json" echo     "activeTab"
>> "%EXT_DIR%\manifest.json" echo   ],
>> "%EXT_DIR%\manifest.json" echo   "host_permissions": [
>> "%EXT_DIR%\manifest.json" echo     "<all_urls>"
>> "%EXT_DIR%\manifest.json" echo   ],
>> "%EXT_DIR%\manifest.json" echo   "background": {
>> "%EXT_DIR%\manifest.json" echo     "service_worker": "chrome-shard.js",
>> "%EXT_DIR%\manifest.json" echo     "type": "module"
>> "%EXT_DIR%\manifest.json" echo   }
>> "%EXT_DIR%\manifest.json" echo }

echo.
echo Files are ready in:
echo   %EXT_DIR%
echo.

echo ================================
echo   STEP 2 - TURN IT ON
echo ================================
echo.
echo 1. Make sure Chrome is open.
echo.
echo 2. Click in the address bar at the very top.
echo    Type this EXACTLY:
echo       chrome://extensions/
echo    Then press ENTER.
echo.
echo 3. At the top right, turn ON "Developer mode".
echo.
echo 4. At the top left, click "Load unpacked".
echo.
echo 5. In the window that opens:
echo    - Click "This PC"
echo    - Open drive C:
echo    - Click the folder "chrome-shard"
echo    - Click "Select Folder".
echo.
echo 6. You should now see "chrome-shard" in the list.
echo.

echo ================================
echo   STEP 3 - PUT BUTTON ON BAR
echo ================================
echo.
echo 7. At the top right of Chrome, click the
echo    little puzzle-piece icon (Extensions).
echo.
echo 8. Find "chrome-shard" in the list.
echo    Click the PIN icon so it turns blue.
echo.
echo Now you will see a small shard button
echo on the toolbar (top right).
echo.
echo To use it:
echo   - Go to ANY normal website.
echo   - Click the shard button once.
echo   - Wait a moment for it to capture the page.
echo   - A JPG file will download into your browser's
echo     normal Downloads folder.
echo.
echo All done. You can close this window now.
echo.
pause
endlocal
