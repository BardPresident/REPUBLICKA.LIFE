@echo off
setlocal ENABLEDELAYEDEXPANSION

REM Rainbow Bridge Base64 encoder
REM Reads bridge.txt and writes bridge.b64.txt in the same folder.

set "APPDIR=%~dp0"
set "SRC=%APPDIR%bridge.txt"
set "DST=%APPDIR%bridge.b64.txt"

if not exist "%SRC%" (
  echo [ERROR] bridge.txt not found in %APPDIR%
  pause
  goto :eof
)

REM Use PowerShell to do proper UTF‑8 -> Base64
powershell -NoLogo -NoProfile -Command ^
  "$p = '%SRC%';" ^
  "$t = Get-Content -LiteralPath $p -Raw -Encoding UTF8;" ^
  "$b = [System.Text.Encoding]::UTF8.GetBytes($t);" ^
  "$s = [System.Convert]::ToBase64String($b);" ^
  "Set-Content -LiteralPath '%DST%' -Value $s -NoNewline -Encoding ASCII"

if errorlevel 1 (
  echo [ERROR] encoding failed.
) else (
  echo [OK] bridge.txt encoded to bridge.b64.txt
)

pause
endlocal
