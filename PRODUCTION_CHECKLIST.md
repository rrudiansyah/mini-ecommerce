# Production Checklist — Mini E-Commerce Builder

## ✅ Security (sudah diimplementasi)
- [x] CSRF protection di semua form POST
- [x] Rate limiting login (5x / 5 menit per IP)
- [x] Session httponly, samesite=strict, use_strict_mode
- [x] Session secure flag (auto aktif jika HTTPS)
- [x] Password hashing bcrypt (password_hash/verify)
- [x] PDO prepared statements (SQL injection protection)
- [x] Input sanitization htmlspecialchars
- [x] XSS protection di semua views
- [x] Security headers di Caddy (X-Frame, XSS-Protection, HSTS)
- [x] Upload directory PHP execution blocked
- [x] Error display dimatikan di production
- [x] storage/ dan rate_limit/ tidak bisa diakses publik

## 📋 Langkah Deploy

### 1. Persiapkan server
```bash
# Minimal: Ubuntu 22.04, Docker, Docker Compose
sudo apt update && sudo apt install -y docker.io docker-compose-plugin
```

### 2. Setup .env production
```bash
cp .env.production .env
# Edit .env: ganti domain, password DB, APP_URL
nano .env
```

### 3. Setup Caddy HTTPS
```bash
cp docker/Caddyfile.production docker/Caddyfile
# Edit: ganti yourdomain.com dengan domain asli
nano docker/Caddyfile
```

### 4. Build & run production
```bash
docker compose -f docker-compose.prod.yml up -d --build
```

### 5. Import database
```bash
docker compose exec db mysql -u root -p ecommerce_builder < database.sql
```

### 6. Buat super admin pertama
```bash
# Masuk ke container
docker compose exec app php -r "
require_once '/app/config/database.php';
\$db = Database::getInstance();
\$hash = password_hash('PASSWORD_KUAT_DISINI', PASSWORD_BCRYPT);
\$db->exec(\"INSERT INTO super_admins (name, username, password) VALUES ('Super Admin', 'superadmin', '\$hash')\");
echo 'Super admin created!';
"
```

### 7. Verify
- [ ] Akses https://yourdomain.com → redirect ke login
- [ ] Login admin toko berfungsi
- [ ] Akses https://yourdomain.com/superadmin/login
- [ ] Upload produk berfungsi
- [ ] Demo mode: https://yourdomain.com/demo

## 🔒 Ganti sebelum go-live
- [ ] Password DB di .env (min. 16 karakter, campuran)
- [ ] APP_URL sesuai domain
- [ ] APP_ENV=production
- [ ] Hapus phpMyAdmin dari production (sudah di-profile debug)

## 📦 Backup rutin (rekomendasi)
```bash
# Backup DB harian via cron
0 2 * * * docker compose exec -T db mysqldump -u root -pPASSWORD ecommerce_builder > /backup/db_$(date +\%Y\%m\%d).sql
```
