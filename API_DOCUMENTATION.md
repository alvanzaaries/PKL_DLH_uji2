# API Laporan Upload - Documentation

## Base URL
```
http://localhost:8000/api
```

## Endpoints

### 1. Upload Laporan
Upload file Excel laporan dengan validasi otomatis.

**Endpoint:** `POST /laporan/upload`

**Content-Type:** `multipart/form-data`

#### Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `file_excel` | file | Yes | File Excel (.xlsx atau .xls), max 5MB |
| `industri_id` | integer | Yes | ID industri (dari database) |
| `bulan` | integer | Yes | Bulan laporan (1-12) |
| `tahun` | integer | Yes | Tahun laporan (minimal 2020) |
| `jenis_laporan` | string | Yes | Jenis laporan (lihat daftar di bawah) |

#### Jenis Laporan yang Valid
- `Laporan Penerimaan Kayu Bulat`
- `Laporan Mutasi Kayu Bulat (LMKB)`
- `Laporan Penerimaan Kayu Olahan`
- `Laporan Mutasi Kayu Olahan (LMKO)`
- `Laporan Penjualan Kayu Olahan`

#### Success Response (201 Created)
```json
{
  "success": true,
  "message": "Laporan berhasil diupload dan disimpan.",
  "data": {
    "laporan_id": 123,
    "jenis_laporan": "Laporan Penerimaan Kayu Bulat",
    "periode": {
      "bulan": 1,
      "tahun": 2024
    },
    "total_rows": 150
  }
}
```

#### Error Responses

**Validation Error (422 Unprocessable Entity)**
```json
{
  "success": false,
  "message": "File Excel memiliki error validasi. Mohon perbaiki file dan upload ulang.",
  "errors": [
    "Baris 15: Jumlah Batang tidak boleh kosong",
    "Baris 16: Volume harus berupa angka"
  ],
  "total_rows": 200,
  "valid_rows": 198,
  "error_code": "VALIDATION_ERROR"
}
```

**Duplicate Error (409 Conflict)**
```json
{
  "success": false,
  "message": "Laporan jenis \"Laporan Penerimaan Kayu Bulat\" untuk bulan 1 tahun 2024 sudah ada.",
  "error_code": "DUPLICATE_LAPORAN"
}
```

**Server Error (500 Internal Server Error)**
```json
{
  "success": false,
  "message": "Terjadi kesalahan saat memproses laporan: [error message]",
  "error_code": "SERVER_ERROR"
}
```

### 2. Health Check
Cek status API server.

**Endpoint:** `GET /health`

**Response:**
```json
{
  "status": "ok",
  "timestamp": "2024-01-21T07:30:00Z"
}
```

## Usage Examples

### cURL
```bash
curl -X POST http://localhost:8000/api/laporan/upload \
  -F "file_excel=@laporan_januari_2024.xlsx" \
  -F "industri_id=1" \
  -F "bulan=1" \
  -F "tahun=2024" \
  -F "jenis_laporan=Laporan Penerimaan Kayu Bulat"
```

### Python (requests)
```python
import requests

url = "http://localhost:8000/api/laporan/upload"

files = {
    'file_excel': ('laporan.xlsx', open('laporan.xlsx', 'rb'), 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
}

data = {
    'industri_id': 1,
    'bulan': 1,
    'tahun': 2024,
    'jenis_laporan': 'Laporan Penerimaan Kayu Bulat'
}

response = requests.post(url, files=files, data=data)
print(response.json())
```

### Python (Bulk Upload Script)
Gunakan script `bulk_upload_laporan.py` yang sudah disediakan:

```python
python bulk_upload_laporan.py
```

Edit konfigurasi di dalam file untuk menyesuaikan dengan file-file yang akan diupload.

## Error Codes

| Code | Description |
|------|-------------|
| `VALIDATION_ERROR` | File Excel memiliki error validasi |
| `DUPLICATE_LAPORAN` | Laporan untuk periode yang sama sudah ada |
| `SERVER_ERROR` | Kesalahan server internal |
| `FILE_NOT_FOUND` | File tidak ditemukan (Python client) |
| `INVALID_JENIS` | Jenis laporan tidak valid (Python client) |
| `TIMEOUT` | Request timeout (Python client) |
| `CONNECTION_ERROR` | Error koneksi ke server (Python client) |

## Notes

### Validasi Otomatis
API menggunakan `LaporanValidationService` yang sama dengan web form, sehingga:
- ✅ Validasi format Excel konsisten
- ✅ Validasi data (required fields, format angka, dll) sama
- ✅ Validasi business logic (misal: persediaan akhir = awal + penambahan - penggunaan)

### File Upload
- Max file size: 5MB
- Supported formats: .xlsx, .xls
- File disimpan sementara, divalidasi, lalu dihapus setelah selesai

### Duplicate Prevention
Sistem otomatis mencegah duplicate berdasarkan:
- `industri_id`
- `jenis_laporan`
- `bulan`
- `tahun`

Satu industri hanya bisa upload satu laporan per jenis per bulan.

## Future Enhancements

- [ ] Token-based authentication (Laravel Sanctum)
- [ ] Rate limiting
- [ ] Batch upload endpoint (multiple files in one request)
- [ ] Webhook untuk notifikasi hasil upload
- [ ] Download template via API
