@echo off
title PresensiKita WhatsApp Bot Gateway
color 0A
echo ===================================================
echo     PRESENSIKITA WHATSAPP BOT GATEWAY (PORT 3000)
echo ===================================================
echo.
cd /d "%~dp0whatsapp-bot"

set "NODE_BIN=node"
if exist "D:\nodeJS\node.exe" (
    set "NODE_BIN=D:\nodeJS\node.exe"
) else if exist "C:\Program Files\nodejs\node.exe" (
    set "NODE_BIN=C:\Program Files\nodejs\node.exe"
)

if not exist node_modules (
    echo [INFO] Menginstal modul Node.js...
    call npm install
)

echo [INFO] Menjalankan server WhatsApp Bot...
"%NODE_BIN%" server.js
pause
