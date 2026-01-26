# Kebutuhan Server - Sistem Informasi Laporan Industri DLH

## Ringkasan Spesifikasi

| Komponen | Minimum | Production-Ready |
|----------|---------|------------------|
| **vCPU** | 2 Core | 4 Core |
| **RAM** | 4 GB | 8 GB |
| **Storage** | 40 GB SSD | 80 GB SSD |
| **Database** | MySQL 8.0+ | MySQL 8.0+ |
| **Bandwidth** | 100 Mbps | 1 Gbps |

---

## Karakteristik Aplikasi

### Beban Kerja
- **Users**: 1 admin aktif, ~30 pengunjung
- **Data Volume**: 
  - 700 industri × 5 laporan/bulan = 3,500 laporan/bulan
  - 21 KPH × 4 PNBP/tahun = 84 laporan PNBP/tahun
- **File Processing**: Excel dengan 20-1500 baris per file
- **Peak Load**: Akhir bulan (deadline pengumpulan)

### Fitur Berat
1. **Excel Processing** (PhpSpreadsheet)
   - Upload & validasi
   - Export rekap & detail
2. **Database Operations**
   - Filtering & sorting
   - Dashboard aggregation

---

## Breakdown Kebutuhan

### 1. CPU (vCPU)

**Minimum: 2 vCPU**
- Cukup untuk operasi normal
- Bottleneck saat multiple Excel uploads

**Ideal: 4 vCPU**
- Headroom untuk peak load
- Support background processing
- Future scaling

### 2. Memory (RAM)

**Breakdown 4 GB (Minimum):**
- OS: 512 MB
- PHP-FPM: 1.5 GB
- MySQL: 1.5 GB
- Nginx: 256 MB
- Buffer: 256 MB

**Breakdown 8 GB (Ideal):**
- OS: 1 GB
- PHP-FPM: 3 GB
- MySQL: 3 GB
- Nginx: 512 MB
- Buffer: 512 MB

**Critical**: PhpSpreadsheet butuh `memory_limit = 512M` per process

### 3. Storage

**Estimasi Pertumbuhan:**
- Database: +2 GB/tahun
- File Upload: +5 GB/tahun
- Logs: +2 GB/tahun
- Backup: +10 GB/tahun

**Total**: ~19 GB/tahun

---

## Konfigurasi PHP (Wajib)

```ini
memory_limit = 512M
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 120
```

## Konfigurasi MySQL

```ini
innodb_buffer_pool_size = 2G
max_connections = 100
```

---

## Rekomendasi VPS

### Option 1: DigitalOcean (Recommended)
```
Droplet: Basic 4 GB / 2 vCPU / 80 GB SSD
Price:   $24/month (~Rp 380,000)
Region:  Singapore
```

### Option 2: Vultr
```
Instance: High Frequency 4 GB / 2 vCPU / 80 GB SSD
Price:    $24/month
Region:   Singapore
```

### Option 3: Niagahoster/Dewaweb (Lokal)
```
VPS:   Cloud VPS 4 GB
Price: Rp 400,000 - 600,000/bulan
Pro:   Support Indonesia, payment mudah
```

---

## Risiko & Mitigasi

### ⚠️ High Priority

**1. Memory Exhaustion**
- **Risiko**: PhpSpreadsheet crash saat file >5 MB
- **Mitigasi**: 
  - Limit file size (max 10 MB)
  - Limit rows (max 3000)
  - Implement Laravel Queue

**2. Concurrent Upload**
- **Risiko**: Multiple upload crash server
- **Mitigasi**: 
  - Laravel Queue + Redis
  - Limit `pm.max_children = 4`

**3. Backup**
- **Risiko**: Data loss (compliance issue)
- **Mitigasi**: 
  - Daily automated backup
  - External storage (S3/Google Cloud)

### ⚠️ Medium Priority

**4. Database Indexing**
- **Mitigasi**: Index pada `industri_id`, `tanggal`, `jenis_laporan`

**5. Log Rotation**
- **Mitigasi**: Logrotate setiap 30 hari

---

## Upgrade Path

### Tahap 1 (Bulan 1-6)
```
VPS: 4 GB RAM / 2 vCPU / 80 GB SSD
Price: $24/month
```

### Tahap 2 (Bulan 6-12)
- Upgrade ke 8 GB RAM (jika usage >80%)
- Implement Laravel Queue + Redis

### Tahap 3 (Tahun 2+)
- Separate database server (jika DB >50 GB)
- Load balancer (jika traffic >100 concurrent)

---

## Monitoring Wajib

1. **Server**: Netdata / Grafana
   - Monitor CPU, RAM, Disk
2. **Application**: Laravel Telescope
   - Monitor slow queries
3. **Uptime**: UptimeRobot
   - Alert jika server down

---

## Kesimpulan

Untuk use case DLH (700 industri, 1 admin, 30 pengunjung):

✅ **VPS 4 GB RAM / 2 vCPU sudah cukup** untuk tahap awal

🔑 **Yang paling penting:**
1. Optimasi memory (PhpSpreadsheet)
2. Proper backup strategy (data compliance)
3. Monitoring & alerting

💰 **Budget**: ~Rp 400,000/bulan (VPS + backup)
