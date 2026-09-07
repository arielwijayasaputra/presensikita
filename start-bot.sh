#!/usr/bin/env bash
# PresensiKita WhatsApp Bot Gateway Starter for Linux/macOS
cd "$(dirname "$0")/whatsapp-bot" || exit

echo "==================================================="
echo "    PRESENSIKITA WHATSAPP BOT GATEWAY (PORT 3000)   "
echo "==================================================="
echo ""

if [ ! -d "node_modules" ]; then
    echo "[INFO] Menginstal modul Node.js..."
    npm install
fi

echo "[INFO] Menjalankan server WhatsApp Bot..."
node server.js
