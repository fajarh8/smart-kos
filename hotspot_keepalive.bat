PowerShell -Command "Set-ExecutionPolicy Unrestricted" >> "%TEMP%\StartupLog.txt" 2>&1
PowerShell C:\xampp\htdocs\smart-kos\hotspot_keepalive.ps1 >> "%TEMP%\StartupLog.txt" 2>&1