# SIP-Jateng (Rekonsiliasi PNBP + Visualisasi Data Industri)

Aplikasi web gabungan:
- **Admin/Internal (HEAD)**: Upload & analisis data rekonsiliasi (Excel) per periode (Tahun/Triwulan) dengan pemisahan akses Admin/User.
- **Public/Visualisasi (Incoming)**: Dashboard publik untuk data industri kehutanan (Primer, Sekunder, TPTKB, Perajin).

## Fitur Utama

### Modul Admin (Rekonsiliasi)
- **RBAC (Admin vs User)**
	- Admin: dashboard statistik, kelola user, lihat semua upload, detail data, download file.
	- User: upload data, lihat riwayat upload miliknya.
- **Upload Excel** dengan UI drag & drop + spinner.
- **KPH saat upload** (wajib) dan **filter KPH** di dashboard admin.
- **Dashboard Admin** dengan filter:
	- Tahun
	- Triwulan spesifik, atau **Akumulasi "Sampai Dengan Triwulan"**
	- KPH
- **Detail Rekonsiliasi**: ringkasan Nilai LHP, Billing, Setor, total setor, selisih (LHP − Setor), dan rekap per jenis/wilayah/bank.

### Modul Publik (Visualisasi)
- **Dashboard Publik** statistik industri (tanpa login).
- **Data Industri Primer** (PBPHH).
- **Data Industri Sekunder** (PBUI).
- **Data TPTKB**.
- **Data Perajin**.
- **Laporan & Rekap** (dengan auth).

## Tech Stack

- Laravel 12
- PHP ^8.2
- Vite + Tailwind
- PhpSpreadsheet (parsing Excel)
- Default DB: SQLite (bisa diganti MySQL/PostgreSQL)
- Timezone: **Asia/Jakarta (UTC+7)**

## Prasyarat

- PHP 8.2+ (ext umum Laravel + ext yang dibutuhkan PhpSpreadsheet)
- Composer
- Node.js + npm

## Setup Cepat

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

```bash
type nul > database\database.sqlite
```

`.env`:
```
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

#### Opsi B: MySQL

`.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sip_jateng
DB_USERNAME=root
DB_PASSWORD=
```

Lalu jalankan:
```bash
php artisan migrate
php artisan db:seed
```

### 4) Jalankan aplikasi

```bash
npm run dev
php artisan serve
```

## Akun Default (Seeder)

| Role  | Email                   | Password |
|-------|-------------------------|----------|
| Admin | admin@sipjateng.test      | password |
| User  | user1@sipjateng.test      | password |
| User  | user2@sipjateng.test      | password |
| User  | user3@sipjateng.test      | password |

## URL Penting

### Admin/Internal
- Beranda: `/`
- Login: `/login`
- Admin Dashboard: `/dashboard`
- User Upload: `/user/upload`
- User Riwayat: `/user/history`

### Public/Visualisasi
- Dashboard Publik: `/public/dashboard`
- Industri Primer: `/industri-primer`
- Industri Sekunder: `/industri-sekunder`
- TPTKB: `/tptkb`
- Perajin: `/perajin`
- Laporan: `/laporan`

## Format Excel (Upload Rekonsiliasi)

Parser membaca data per sheet dan **mengabaikan sheet yang judulnya mengandung "REKAP"**.

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
- `L` = No NTPN/Setor
- `M` = Tgl Setor
- `N` = Nilai Setor
