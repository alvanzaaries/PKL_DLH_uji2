# SISUDAH (Rekonsiliasi PNBP)

Aplikasi web untuk upload & analisis data rekonsiliasi (Excel) per periode (Tahun/Triwulan) dengan pemisahan akses **Admin** dan **User**.

## Fitur Utama

- **RBAC (Admin vs User)**
	- Admin: dashboard statistik, kelola user, lihat semua upload, detail data, download file.
	- User: upload data, lihat riwayat upload miliknya.
- **Upload Excel** dengan UI drag & drop + spinner.
- **KPH saat upload** (wajib) dan **filter KPH** di dashboard admin.
- **Dashboard Admin** dengan filter:
	- Tahun
	- Triwulan spesifik, atau **Akumulasi “Sampai Dengan Triwulan”**
	- KPH
- **Detail Rekonsiliasi**: ringkasan Nilai LHP, Billing, Setor, total setor, selisih (LHP − Setor), dan rekap per jenis/wilayah/bank.
- Timezone aplikasi: **Asia/Jakarta (UTC+7)**.

## Tech Stack

- Laravel 12
- PHP ^8.2
- Vite + Tailwind
- PhpSpreadsheet (parsing Excel)
- Default DB: SQLite (bisa diganti MySQL/PostgreSQL)

## Prasyarat

- PHP 8.2+ (ext umum Laravel + ext yang dibutuhkan PhpSpreadsheet)
- Composer
- Node.js + npm

## Setup Cepat

Jalankan dari root project.

### 1) Install dependency

```bash
composer install
npm install
```

### 2) Konfigurasi env

```bash
copy .env.example .env
php artisan key:generate
```

Pastikan env minimal:

- `APP_URL=http://localhost:8000`
- `APP_TIMEZONE=Asia/Jakarta`

### 3) Database

#### Opsi A (default): SQLite

Buat file DB jika belum ada:

```bash
type nul > database\database.sqlite
```

Lalu pastikan `.env`:

```
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

Jalankan migrasi + seeder:

```bash
php artisan migrate
php artisan db:seed
```

#### Opsi B: MySQL

Atur `.env` (contoh):

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sisudah
DB_USERNAME=root
DB_PASSWORD=
```

Lalu:

```bash
php artisan migrate
php artisan db:seed
```

### 4) Jalankan aplikasi

Mode dev (server + vite):

```bash
npm run dev
php artisan serve
```

Atau pakai script composer (menjalankan beberapa proses sekaligus jika environment mendukung):

```bash
composer run dev
```

## Akun Default (Seeder)

Seeder akan membuat akun berikut (password sama):

- Admin
	- Email: `admin@sisudah.test`
	- Password: `password`
- User
	- `user1@sisudah.test` / `password`
	- `user2@sisudah.test` / `password`
	- `user3@sisudah.test` / `password`

## URL Penting

- Beranda: `/`
- Login: `/login`
- Admin Dashboard: `/dashboard`
- User Upload: `/user/upload`
- User Riwayat: `/user/history`

## Format Excel (Upload)

Parser membaca data per sheet dan **mengabaikan sheet yang judulnya mengandung “REKAP”**.

Mapping kolom utama (format fixed):

- `C` = No LHP
- `D` = Tgl LHP
- `E` = Jenis HH
- `F` = Volume
- `G` = Satuan
- `H` = Nilai LHP (Rp)
- `I` = No Billing
- `J` = Tgl Billing
- `K` = Nilai Billing
- `L` = Tgl Setor
- `M` = Bank
- `N` = NTPN
- `O` = NTB
- `P` = Nilai Setor (Rp)

Catatan:

- Tahun, Triwulan, dan **KPH wajib diisi** pada form upload.
- KPH disimpan di header rekonsiliasi dan dipakai untuk filter dashboard admin.

## Troubleshooting

- Jika setelah update view tidak berubah: jalankan `php artisan view:clear`.
- Jika pakai `SESSION_DRIVER=database` dan muncul error table `sessions` tidak ada:
	- Opsi cepat: ubah `.env` menjadi `SESSION_DRIVER=file`, atau
	- Buat tabel sessions: `php artisan session:table` lalu `php artisan migrate`.

## Lisensi

Project ini menggunakan framework Laravel (MIT) dan dependensi open-source lainnya.
