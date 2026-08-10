## Workflow

**Sebelum coding:**
```bash
git pull origin main
```

**Selesai coding:**
```bash
git add .
git commit -m "deskripsi singkat perubahan yang dilakukan"
```

**Sebelum push:**
```bash
git pull origin main
git push origin main
```

**Aturan utama:**
- Jangan pernah commit `.env`
- Jangan push langsung ke `main` kalau kerja tim — pakai branch + Pull Request

**Fitur besar?** Kerja di branch terpisah:
```bash
git checkout -b nama-fitur
git push -u origin nama-fitur
```

**Mau gabung jadi contributor?** Minta di-invite dulu di GitHub, terus:
```bash
git clone git@github.com:arielwijayasaputra/presensikita.git
cd presensikita
composer install
```