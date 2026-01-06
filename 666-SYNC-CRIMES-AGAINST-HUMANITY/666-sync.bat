@echo off
set "WGET=%~dp0wget.exe"

rem ===== 666crimes-against-humanity999 =====
mkdir "%~dp0666crimes" 2>nul
pushd "%~dp0666crimes"
"%WGET%" -r -np -nH --cut-dirs=1 -e robots=off ^
  "https://archive.org/download/666crimes-against-humanity999/"
popd

rem ===== trepublic receipts =====
mkdir "%~dp0receipts" 2>nul
pushd "%~dp0receipts"
"%WGET%" -r -np -nH --cut-dirs=1 -e robots=off ^
  "https://trepublic.net/receipts/"
popd

rem ===== trepublic shards =====
mkdir "%~dp0shards" 2>nul
pushd "%~dp0shards"
"%WGET%" -r -np -nH --cut-dirs=1 -e robots=off ^
  "https://trepublic.net/shards/"
popd

rem ===== trepublic emblems =====
mkdir "%~dp0emblems" 2>nul
pushd "%~dp0emblems"
"%WGET%" -r -np -nH --cut-dirs=1 -e robots=off ^
  "https://trepublic.net/emblems/"
popd

echo.
echo DONE.
pause
