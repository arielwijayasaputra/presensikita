const express = require('express');
const {
    default: makeWASocket,
    useMultiFileAuthState,
    DisconnectReason,
    fetchLatestBaileysVersion,
    Browsers
} = require('@whiskeysockets/baileys');
const pino = require('pino');
const qrcode = require('qrcode-terminal');
const QRCode = require('qrcode');
const fs = require('fs');
const path = require('path');

const logFile = path.join(__dirname, 'bot.log');
function log(msg) {
    const timestamp = new Date().toISOString();
    const line = `[${timestamp}] ${msg}\n`;
    try {
        fs.appendFileSync(logFile, line);
    } catch (e) {}
    try {
        console.log(msg);
    } catch (e) {}
}

if (process.stdout) {
    process.stdout.on('error', (err) => {
        if (err.code === 'EPIPE') return;
    });
}
if (process.stderr) {
    process.stderr.on('error', (err) => {
        if (err.code === 'EPIPE') return;
    });
}

process.on('uncaughtException', (err) => {
    log(`[UncaughtException] ${err.stack || err}`);
});

process.on('unhandledRejection', (reason) => {
    log(`[UnhandledRejection] ${reason}`);
});

// Heartbeat agar event loop tidak pernah idle/exit
setInterval(() => {}, 60000);

const app = express();
const PORT = process.env.PORT || 3000;

app.use(express.json());

let sock = null;
let isConnected = false;
let qrCodeString = null;
let isStarting = false;

// In-memory message store untuk menjawab retry receipt kunci enkripsi E2EE
const sentMessages = new Map();
const msgRetryCounterMap = new Map();

async function startWhatsApp() {
    if (isStarting) return;
    isStarting = true;

    try {
        const authPath = path.join(__dirname, 'auth_info_baileys');
        const { state, saveCreds } = await useMultiFileAuthState(authPath);
        const { version } = await fetchLatestBaileysVersion().catch(() => ({ version: [2, 3000, 1015901307] }));

        log(`🤖 Memulai WhatsApp Bot Gateway (Baileys v${version.join('.')})`);

        if (sock) {
            try {
                sock.ev.removeAllListeners();
                sock.end(undefined);
            } catch (e) {}
            sock = null;
        }

        sock = makeWASocket({
            version,
            auth: state,
            logger: pino({ level: 'silent' }),
            printQRInTerminal: false,
            browser: Browsers.windows('Desktop'),
            syncFullHistory: false,
            markOnlineOnConnect: true,
            msgRetryCounterCache: {
                get: (key) => msgRetryCounterMap.get(key),
                set: (key, val) => msgRetryCounterMap.set(key, val),
                del: (key) => msgRetryCounterMap.delete(key),
            },
            getMessage: async (key) => {
                if (key.id && sentMessages.has(key.id)) {
                    return sentMessages.get(key.id);
                }
                return {
                    conversation: 'Notifikasi PresensiKita'
                };
            }
        });

        sock.ev.on('creds.update', async () => {
            try {
                await saveCreds();
                log('💾 Sesi kredensial WhatsApp berhasil disimpan.');
            } catch (err) {
                log(`❌ Gagal menyimpan creds: ${err.message || err}`);
            }
        });

        sock.ev.on('connection.update', (update) => {
            const { connection, lastDisconnect, qr } = update;

            if (qr) {
                qrCodeString = qr;
                log('📱 QR Code baru telah dibuat, menunggu pemindaian.');
                try {
                    if (process.stdout && process.stdout.isTTY) {
                        qrcode.generate(qr, { small: true });
                    }
                } catch (e) {}
            }

            if (connection === 'close') {
                isConnected = false;
                const statusCode = lastDisconnect?.error?.output?.statusCode;
                const shouldReconnect = statusCode !== DisconnectReason.loggedOut;

                log(`⚠️ Koneksi terputus (status: ${statusCode}). Reconnect: ${shouldReconnect}`);

                if (shouldReconnect) {
                    setTimeout(() => {
                        log('🔄 Mencoba menghubungkan kembali...');
                        isStarting = false;
                        startWhatsApp().catch((e) => log(`Gagal reconnect: ${e}`));
                    }, 2500);
                } else {
                    log('❌ Sesi telah logout dari WhatsApp.');
                }
            } else if (connection === 'open') {
                isConnected = true;
                qrCodeString = null;
                const userPhone = sock.user?.id?.split(':')[0] || 'Unknown';
                log(`✅ WhatsApp Bot BERHASIL TERHUBUNG! Nomor: ${userPhone}`);
            }
        });
    } catch (err) {
        log(`❌ Error saat inisialisasi WhatsApp: ${err.message || err}`);
    } finally {
        isStarting = false;
    }
}

function formatJid(number) {
    let clean = (number || '').toString().replace(/\D/g, '');
    if (clean.startsWith('0')) {
        clean = '62' + clean.slice(1);
    }
    if (!clean.endsWith('@s.whatsapp.net')) {
        clean = clean + '@s.whatsapp.net';
    }
    return clean;
}

// ── REST API ENDPOINTS ──

// 1. Cek Status Kesehatan Bot
app.get('/status', (req, res) => {
    if (isConnected) {
        return res.json({
            online: true,
            status: 'connected',
            message: 'Server bot WhatsApp aktif dan terhubung.',
            user: sock?.user?.id?.split(':')[0] || null
        });
    }

    const credsPath = path.join(__dirname, 'auth_info_baileys', 'creds.json');
    if (fs.existsSync(credsPath)) {
        try {
            const credsData = JSON.parse(fs.readFileSync(credsPath, 'utf-8'));
            if (credsData && credsData.me) {
                return res.json({
                    online: false,
                    status: 'connecting',
                    message: 'Sesi tersimpan, sedang menghubungkan...',
                    user: credsData.me?.id?.split(':')[0] || null
                });
            }
        } catch (e) {}
    }

    return res.json({
        online: false,
        status: qrCodeString ? 'waiting_qr' : 'disconnected',
        message: qrCodeString
            ? 'Bot sedang menunggu scan QR Code.'
            : 'Bot WhatsApp sedang offline / mencoba menghubungkan...'
    });
});

// 2. Ambil Gambar QR Code (Data URL) untuk Tampilan Browser
app.get('/qr', async (req, res) => {
    if (isConnected) {
        return res.json({
            status: 'connected',
            message: 'WhatsApp Bot sudah terhubung dan aktif.',
            user: sock?.user?.id?.split(':')[0] || null
        });
    }

    // Cek apakah sesi sudah ada dan sedang proses menghubungkan
    const credsPath = path.join(__dirname, 'auth_info_baileys', 'creds.json');
    if (fs.existsSync(credsPath)) {
        try {
            const credsData = JSON.parse(fs.readFileSync(credsPath, 'utf-8'));
            if (credsData && credsData.me) {
                return res.json({
                    status: 'connecting',
                    message: 'Sesi tersimpan, sedang memulihkan koneksi...',
                    user: credsData.me?.id?.split(':')[0] || null
                });
            }
        } catch (e) {}
    }

    if (!qrCodeString) {
        return res.json({
            status: 'waiting',
            message: 'QR Code sedang disiapkan...'
        });
    }

    try {
        const qrDataUrl = await QRCode.toDataURL(qrCodeString, { width: 280, margin: 2 });
        return res.json({
            status: 'waiting_qr',
            qr_image: qrDataUrl,
            message: 'Silakan scan QR Code dengan WhatsApp di HP Anda.'
        });
    } catch (err) {
        return res.status(500).json({ status: 'error', message: err.message });
    }
});

function clearAuthDir() {
    const authPath = path.join(__dirname, 'auth_info_baileys');
    if (fs.existsSync(authPath)) {
        try {
            fs.rmSync(authPath, { recursive: true, force: true });
        } catch (e) {
            try {
                const files = fs.readdirSync(authPath);
                for (const file of files) {
                    fs.unlinkSync(path.join(authPath, file));
                }
            } catch (err) {}
        }
    }
}

// 3. Restart / Reconnect Bot
app.post('/restart', async (req, res) => {
    try {
        if (sock) {
            try { sock.end(new Error('Manual restart from web')); } catch (e) {}
        }
        startWhatsApp().catch(() => {});
        return res.json({ status: true, message: 'WhatsApp Bot sedang dimulai ulang.' });
    } catch (err) {
        return res.status(500).json({ status: false, message: err.message });
    }
});

// 4. Putuskan Koneksi / Logout Bot
app.post('/disconnect', async (req, res) => {
    try {
        log('🔌 Menerima permintaan pemutusan koneksi bot WhatsApp...');
        if (sock) {
            try {
                sock.ev.removeAllListeners();
                await sock.logout().catch(() => {});
                sock.end(undefined);
            } catch (e) {}
            sock = null;
        }
        isConnected = false;
        qrCodeString = null;
        sentMessages.clear();
        msgRetryCounterMap.clear();

        // Hapus file auth
        clearAuthDir();

        // Mulai ulang WhatsApp agar langsung generate QR baru
        isStarting = false;
        setTimeout(() => {
            startWhatsApp().catch((e) => log(`Error restart setelah disconnect: ${e}`));
        }, 1000);

        return res.json({
            status: true,
            message: 'Koneksi WhatsApp Bot berhasil diputuskan. Silakan scan QR code baru untuk menghubungkan kembali.'
        });
    } catch (err) {
        log(`❌ Gagal memutuskan bot: ${err.message || err}`);
        return res.status(500).json({ status: false, message: err.message || 'Gagal memutuskan bot.' });
    }
});

app.post('/logout', (req, res) => {
    return res.redirect(307, '/disconnect');
});

// 5. Kirim Pesan WhatsApp
app.post('/send-message', async (req, res) => {
    try {
        const { number, message } = req.body;

        if (!number || !message) {
            return res.status(400).json({
                status: false,
                message: 'Parameter "number" dan "message" wajib diisi.'
            });
        }

        if (!isConnected || !sock) {
            return res.status(503).json({
                status: false,
                message: 'WhatsApp Bot belum terhubung atau sedang offline. Silakan scan QR code terlebih dahulu.'
            });
        }

        const jid = formatJid(number);
        console.log(`📤 Mengirim pesan ke ${jid}...`);

        // Handshake sesi & presence ke WhatsApp server
        try {
            await sock.presenceSubscribe(jid);
            await sock.sendPresenceUpdate('composing', jid);
            await new Promise((resolve) => setTimeout(resolve, 300));
            await sock.sendPresenceUpdate('paused', jid);
        } catch (e) {}

        const result = await sock.sendMessage(jid, { text: message });

        // Simpan salinan pesan untuk melayani retry request E2EE
        if (result?.key?.id && result?.message) {
            sentMessages.set(result.key.id, result.message);
            if (sentMessages.size > 500) {
                const oldestKey = sentMessages.keys().next().value;
                sentMessages.delete(oldestKey);
            }
        }

        console.log(`✅ Pesan berhasil terkirim ke ${jid} (ID: ${result.key?.id})`);

        return res.json({
            status: true,
            message: 'Pesan berhasil dikirim via WhatsApp.',
            message_id: result.key?.id
        });
    } catch (err) {
        console.error('❌ Gagal mengirim pesan:', err);
        return res.status(500).json({
            status: false,
            message: 'Gagal mengirim pesan: ' + (err.message || 'Terjadi kesalahan internal.')
        });
    }
});

// Mulai WhatsApp dan Express Server
startWhatsApp().catch((err) => {
    console.error('Fatal error saat inisialisasi WhatsApp:', err);
});

app.listen(PORT, () => {
    console.log(`🌐 REST API Server berjalan di http://127.0.0.1:${PORT}`);
});
