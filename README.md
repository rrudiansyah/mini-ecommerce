# Mini E-Commerce Builder — PHP MVC

## Struktur Folder
```
/
├── app/
│   ├── Controllers/     # Logic request/response
│   ├── Models/          # Query database
│   └── Views/           # Template HTML
├── config/
│   └── database.php     # Konfigurasi DB & konstanta
├── core/
│   ├── Database.php     # Koneksi PDO singleton
│   ├── Model.php        # Base model (CRUD)
│   ├── Controller.php   # Base controller
│   └── Router.php       # URL routing
├── public/
│   ├── index.php        # Entry point
│   ├── .htaccess        # URL rewrite
│   ├── css/admin.css    # Stylesheet admin
│   └── uploads/         # Upload gambar
├── routes/
│   └── web.php          # Definisi semua route
└── database.sql         # Schema + data contoh
```

## Cara Install
1. Import `database.sql` ke phpMyAdmin
2. Edit `config/database.php` sesuaikan DB_USER & DB_PASS
3. Letakkan folder di htdocs/www
4. Akses `http://localhost/ecommerce-builder/public`

## Login Demo
- Email: `admin@kopinusantara.com`
- Password: `admin123`

## Menambah Niche Baru
1. Tambah enum di tabel `stores`
2. Buat folder theme di `public/themes/{niche}`
3. Insert data store + admin baru via SQL
