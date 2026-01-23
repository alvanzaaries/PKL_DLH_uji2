
# SISUDAH — Sistem Informasi PNBP Industri & Laporan 📌

![Build](https://img.shields.io/badge/build-passing-brightgreen)
![Version](https://img.shields.io/badge/version-0.1.0-blue)
![License](https://img.shields.io/badge/license-Internal-lightgrey)
![Tech Stack](https://img.shields.io/badge/stack-Laravel%20%7C%20MySQL%20%7C%20Vite-orange)

SISUDAH adalah aplikasi internal untuk pengelolaan data PNBP industri, validasi, dan pelaporan secara terstruktur. Fokusnya adalah memastikan alur data masuk–proses–laporan berjalan konsisten, auditable, dan mudah ditelusuri.

Dengan modul industri dan laporan yang terintegrasi, tim operasional dapat mempercepat proses administrasi, menjaga kualitas data, serta menghasilkan laporan periodik yang siap ditinjau.

## Fitur Utama 🚀

- Manajemen data industri (primer/sekunder)
- Pengelolaan laporan penerimaan, mutasi, dan penjualan
- Validasi dan rekonsiliasi data
- Upload berkas pendukung dan tracking status
- Export laporan (format standar internal)

## Teknologi yang Digunakan ⚙️

| Kategori | Teknologi |
| --- | --- |
| Bahasa | PHP, JavaScript |
| Framework | Laravel |
| Database | MySQL / MariaDB |
| Frontend Tooling | Vite |
| Dependency Manager | Composer, npm |
| Testing | PHPUnit |

## Struktur Folder Project 📂

```
SISTEM/
├─ app/
│  ├─ Http/
│  ├─ Models/
│  ├─ Providers/
│  └─ Services/
├─ bootstrap/
├─ config/
├─ database/
├─ public/
├─ resources/
├─ routes/
├─ storage/
├─ tests/
├─ vendor/
├─ package.json
├─ composer.json
└─ README.md
```

## Cara Instalasi & Menjalankan Project 🧪

1. Pastikan PHP, Composer, Node.js, dan MySQL sudah terpasang.
2. Install dependency backend:
	```bash
	composer install
	```
3. Install dependency frontend:
	```bash
	npm install
	```
4. Atur variabel lingkungan (lihat bagian Konfigurasi).
5. Generate application key:
	```bash
	php artisan key:generate
	```
6. Migrasi dan seeding database:
	```bash
	php artisan migrate --seed
	```
7. Jalankan aplikasi:
	```bash
	php artisan serve
	```
8. (Opsional) Jalankan asset bundler:
	```bash
	npm run dev
	```

## Cara Penggunaan 📌

1. Login ke aplikasi menggunakan kredensial internal.
2. Pilih modul **Industri** untuk kelola data pelaku usaha.
3. Masuk ke modul **PNBP & Laporan** untuk input, upload, dan validasi.
4. Jalankan rekonsiliasi bila dibutuhkan.
5. Ekspor laporan periodik sesuai kebutuhan.

## Konfigurasi ⚙️

Gunakan variabel lingkungan berikut (sesuaikan dengan environment Anda):

| Variabel | Contoh Nilai | Keterangan |
| --- | --- | --- |
| APP_NAME | SISUDAH | Nama aplikasi |
| APP_ENV | local | Environment |
| APP_URL | http://localhost:8000 | Base URL |
| APP_TIMEZONE | Asia/Jakarta | Zona waktu |
| DB_CONNECTION | mysql | Driver database |
| DB_HOST | 127.0.0.1 | Host database |
| DB_PORT | 3306 | Port database |
| DB_DATABASE | sisudah | Nama database |
| DB_USERNAME | root | Username database |
| DB_PASSWORD | ******** | Password database |
| FILESYSTEM_DISK | public | Storage disk |
| QUEUE_CONNECTION | database | Queue driver |

## Screenshot / Preview 🖼️

> _Placeholder: tambahkan screenshot dashboard, modul industri, dan laporan di sini._

## Kontribusi 🤝

- Gunakan branch fitur dengan penamaan yang jelas.
- Pastikan perubahan teruji sebelum merge.
- Tulis deskripsi singkat dan alasan perubahan pada PR.

## Lisensi 📄

Proprietary — digunakan untuk kebutuhan internal.

## Catatan Tambahan 📌

- Best practice: lakukan backup database sebelum migrasi besar.
- Limitation sementara: proses export besar bisa memerlukan waktu lebih lama.
- Roadmap singkat:
  - Penambahan audit trail yang lebih detail
  - Notifikasi otomatis untuk status laporan
  - Optimasi performa pada modul rekonsiliasi
