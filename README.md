# 🚗 SIPETAK - Sistem Informasi Pemesanan Kendaraan Dinas

Aplikasi web berbasis **Laravel 11** untuk mengelola pemesanan kendaraan dinas dengan fitur **analisis data mining K-Means Clustering** (Euclidean Distance) untuk mengelompokkan kendaraan berdasarkan pola pemakaian.

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql)
![Tailwind](https://img.shields.io/badge/Tailwind-3.x-06B6D4?style=flat&logo=tailwindcss)

---

## ✨ Fitur Utama

### 1. Autentikasi & Role Management
- Login menggunakan email & password
- 4 role: **Admin**, **Pemohon**, **Operasional**, **Manajemen/Atasan**
- Session-based authentication dengan proteksi middleware

### 2. Master Data (CRUD)
- **Data Kendaraan** (nama, plat, jenis, status, kapasitas)
- **Data Driver** (nama, telepon, SIM, status)

### 3. Transaksi Pemesanan
- Form pengajuan pemesanan
- Tabel approval dengan validasi konflik jadwal
- Status: `pending` → `disetujui` / `ditolak` → `selesai`

### 4. Analisis Data Mining - K-Means Clustering 🧠
- Implementasi **native PHP** (tanpa library eksternal)
- Atribut: **Frequency**, **Duration**, **Intensity**
- Perhitungan jarak: **Euclidean Distance**
- Normalisasi min-max untuk konsistensi skala
- Inisialisasi centroid dengan metode K-Means++ (deterministik)
- Iterasi hingga **konvergen** (max_shift < tolerance)
- **History iterasi centroid** ditampilkan hingga stabil
- Hasil disimpan ke tabel `kmeans_results`

### 5. Dashboard & Laporan
- Statistik ringkas (total pemesanan, kendaraan, driver)
- Chart.js untuk grafik pemesanan 6 bulan terakhir
- Ringkasan cluster K-Means
- Tabel pemesanan terbaru

---

## 📋 Persyaratan Sistem

- **PHP** >= 8.2
- **Composer** 2.x
- **Node.js** >= 18 & **NPM**
- **MySQL** >= 5.7 atau MariaDB >= 10.3

Ekstensi PHP yang dibutuhkan:
- BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, Fileinfo, cURL

---

## 🚀 Langkah-langkah Setup

### 1. Clone / Extract Project
```bash
cd sistem-pemesanan-kendaraan
```

### 2. Install Dependency PHP
```bash
composer install
```

### 3. Install Dependency JavaScript
```bash
npm install
```

### 4. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env` dan sesuaikan bagian database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistem_pemesanan_kendaraan
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Buat Database MySQL
```sql
CREATE DATABASE sistem_pemesanan_kendaraan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Jalankan Migration
```bash
php artisan migrate
```

### 7. Seed Data Dummy
```bash
php artisan db:seed
```
Seeder akan menambahkan:
- 7 user (1 admin, 3 pemohon, 2 operasional, 1 manajemen)
- 10 kendaraan
- 8 driver
- **34 transaksi pemesanan** dengan pola bervariasi untuk pengujian K-Means

### 8. Build Asset Frontend
```bash
# Untuk development
npm run dev

# Untuk production
npm run build
```

### 9. Jalankan Server
```bash
php artisan serve
```

Buka browser dan akses: **http://localhost:8000**

---

## 🔐 Akun Default

| Email | Password | Role |
|-------|----------|------|
| `maulidul@gmail.com` | `12345678` | **Admin** |
| `budi@dishub.go.id` | `12345678` | Pemohon |
| `siti@dishub.go.id` | `12345678` | Pemohon |
| `andi@dishub.go.id` | `12345678` | Pemohon |
| `dewi@dishub.go.id` | `12345678` | Operasional |
| `rudi@dishub.go.id` | `12345678` | Operasional |
| `hendra@dishub.go.id` | `12345678` | Manajemen |

---

## 📁 Struktur Project

```
sistem-pemesanan-kendaraan/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/LoginController.php
│   │   │   ├── BookingController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── DriverController.php
│   │   │   ├── KMeansController.php       ← Kontroler utama K-Means
│   │   │   ├── ProfileController.php
│   │   │   └── VehicleController.php
│   │   └── Middleware/EnsureUserHasRole.php
│   ├── Models/
│   │   ├── User.php          (with role: admin/pemohon/operasional/manajemen)
│   │   ├── Vehicle.php
│   │   ├── Driver.php
│   │   ├── Booking.php
│   │   └── KmeansResult.php
│   └── Services/
│       └── KMeansService.php ← Algoritma K-Means native PHP
├── database/
│   ├── migrations/           (7 migration files)
│   └── seeders/              (4 seeder files + DatabaseSeeder)
├── resources/
│   └── views/
│       ├── auth/login.blade.php
│       ├── layouts/app.blade.php
│       ├── dashboard/index.blade.php
│       ├── vehicles/         (index, create, edit)
│       ├── drivers/          (index, create, edit)
│       ├── bookings/         (index, create, edit, approval)
│       ├── kmeans/           (index, result)
│       └── profile/edit.blade.php
├── routes/
│   ├── web.php               ← Semua route aplikasi
│   └── console.php
└── config/                   (app, auth, cache, database, session)
```

---

## 🧠 Tentang Algoritma K-Means

### Atribut yang Dianalisis

| Atribut | Deskripsi | Sumber |
|---------|-----------|--------|
| **Frequency** | Jumlah pemesanan per kendaraan | `COUNT(*) FROM bookings` |
| **Duration** | Rata-rata durasi pemakaian (hari) | `AVG(DATEDIFF(tanggal_selesai, tanggal_mulai) + 1)` |
| **Intensity** | Intensitas (pemesanan per bulan aktif) | `frequency / bulan_aktif` |

### Algoritma

1. **Normalisasi Min-Max** — agar skala tiap atribut setara (0-1)
2. **Inisialisasi Centroid** — K-Means++ (titik terjauh dari centroid existing)
3. **Assignment Step** — hitung jarak Euclidean ke setiap centroid, pilih terdekat
4. **Update Step** — centroid baru = rata-rata titik dalam cluster
5. **Cek Konvergensi** — jika max pergeseran centroid < tolerance, **konvergen**
6. **Iterasi ulang** sampai konvergen atau max iterasi tercapai

### Formula Euclidean Distance

```
d(p, q) = √((p₁-q₁)² + (p₂-q₂)² + (p₃-q₃)²)
```

### Cara Menjalankan

1. Login ke aplikasi
2. Buka menu **K-Means Clustering** di sidebar
3. Atur parameter (default: K=3, max_iter=100, tolerance=0.0001)
4. Klik **🚀 Jalankan Analisis**
5. Hasil akan menampilkan:
   - Ringkasan per cluster (Tinggi/Sedang/Rendah)
   - Tabel pengelompokan kendaraan + jarak Euclidean
   - **History iterasi centroid** hingga konvergen

---

## 🛣️ Daftar Route

### Publik
- `GET /` → redirect ke `/dashboard`
- `GET /login` → Halaman login
- `POST /login` → Proses login
- `POST /logout` → Logout

### Terproteksi Auth
- `GET /dashboard` → Dashboard
- `GET/POST/PUT/DELETE /profile` → Manajemen profil

### Master Data
- `GET /vehicles` → Daftar kendaraan (semua role)
- `GET/POST /vehicles/create` → Tambah kendaraan (admin/operasional)
- `GET/PUT/DELETE /vehicles/{id}/edit` → Edit/hapus (admin/operasional)
- Sama untuk `/drivers`

### Pemesanan
- `GET /bookings` → Daftar pemesanan
- `GET/POST /bookings/create` → Ajukan pemesanan
- `GET/PUT/DELETE /bookings/{id}/edit` → Edit/hapus
- `GET /bookings-approval` → Tabel approval (admin/operasional)
- `PATCH /bookings/{id}/process-approval` → Proses approval

### K-Means
- `GET /kmeans` → Halaman analisis
- `POST /kmeans/analyze` → Jalankan analisis
- `POST /kmeans/reset` → Reset hasil

---

## 🧪 Testing

```bash
# Jalankan seluruh test
php artisan test

# Atau pakai PHPUnit langsung
./vendor/bin/phpunit
```

---

## 🎨 Stack Teknologi

| Layer | Teknologi |
|-------|-----------|
| **Backend** | Laravel 11, PHP 8.2 |
| **Frontend** | Blade Templates, Tailwind CSS 3 |
| **Build Tool** | Vite |
| **Charts** | Chart.js |
| **Database** | MySQL 8.0 |
| **Authentication** | Native Laravel Auth + Custom Middleware |

---

## 📝 Lisensi

MIT License - bebas digunakan untuk keperluan akademik & komersial.

---

## 👤 Author

**Maulidul** — Dibuat sebagai implementasi Tugas / Skripsi Sistem Informasi Pemesanan Kendaraan Dinas dengan Analisis K-Means Clustering.

---

## 🐛 Troubleshooting

### Error: "SQLSTATE[42000]: Syntax error or access violation: 1071 Specified key was too long"

Pastikan MySQL Anda mendukung InnoDB dengan `utf8mb4`. Tambahkan di `AppServiceProvider::boot()`:
```php
Schema::defaultStringLength(191);
```
*( Sudah sudah ditambahkan di project ini )*

### Error: "Class 'PDO' not found"

Install ekstensi PHP:
```bash
# Ubuntu/Debian
sudo apt install php8.2-mysql php8.2-mbstring php8.2-xml

# macOS (Homebrew)
brew install php@8.2
```

### Frontend tidak load (CSS/JS tidak muncul)

```bash
npm run build
php artisan storage:link
php artisan view:clear
php artisan cache:clear
```

### Tidak bisa login setelah seeding

Pastikan password sudah di-hash dengan benar:
```bash
php artisan tinker
> \App\Models\User::first()->update(['password' => bcrypt('12345678')]);
```

---

Semoga bermanfaat! 🎉