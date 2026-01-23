# API Laporan Upload - Documentation

## Authentication

**All API endpoints require authentication using an API key.**

Include the API key in the request header:
```
X-API-Key: your-api-key-here
```

The API key must match the `INTERNAL_API_KEY` configured in the server's `.env` file.

## Rate Limiting

API endpoints are rate limited to **60 requests per minute** per IP address.

## Base URL
```
http://localhost:8000/api
```

## Endpoints

### 1. Upload Laporan
Upload file Excel laporan dengan validasi otomatis.

**Endpoint:** `POST /laporan/upload`

**Authentication:** Required (X-API-Key header)

**Rate Limit:** 60 requests/minute

**Content-Type:** `multipart/form-data`

#### Request Headers

| Header | Required | Description |
|--------|----------|-------------|
| `X-API-Key` | Yes | API key untuk autentikasi |

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

**Unauthorized (401)**
```json
{
  "success": false,
  "message": "Invalid or missing API key. Please provide a valid X-API-Key header.",
  "error_code": "UNAUTHORIZED"
}
```

**Validation Error (422 Unprocessable Entity)**
```json
{
  "success": false,
  "message": "File Excel memiliki error validasi. Mohon perbaiki file dan upload ulang.",
  "errors": [
    "Baris Excel 16 (Data #1): Jumlah Batang tidak boleh kosong",
    "Baris Excel 17 (Data #2): Volume harus berupa angka"
  ],
  "total_rows": 200,
  "valid_rows": 198,
  "error_code": "VALIDATION_ERROR"
}
```

**Rate Limit Exceeded (429)**
```json
{
  "message": "Too Many Attempts."
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
  -H "X-API-Key: dlhk_internal_api_key_2024_secure_random_string" \
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

headers = {
    'X-API-Key': 'dlhk_internal_api_key_2024_secure_random_string'
}

files = {
    'file_excel': ('laporan.xlsx', open('laporan.xlsx', 'rb'), 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
}

data = {
    'industri_id': 1,
    'bulan': 1,
    'tahun': 2024,
    'jenis_laporan': 'Laporan Penerimaan Kayu Bulat'
}

response = requests.post(url, files=files, data=data, headers=headers)
print(response.json())
```

### Python (Bulk Upload Script)
Gunakan script `bulk_upload_laporan.py` yang sudah disediakan:

1. Edit konfigurasi API key di dalam file (baris 24):
```python
API_KEY = "your-api-key-here"  # Harus sama dengan INTERNAL_API_KEY di .env
```

2. Edit konfigurasi upload di bagian `if __name__ == "__main__":`

3. Jalankan script:
```bash
python bulk_upload_laporan.py
```

## Error Codes

| Code | Description |
|------|-------------|
| `UNAUTHORIZED` | API key tidak valid atau tidak ada |
| `VALIDATION_ERROR` | File Excel memiliki error validasi |
| `DUPLICATE_LAPORAN` | Laporan untuk periode yang sama sudah ada |
| `SERVER_ERROR` | Kesalahan server internal |
| `SERVER_MISCONFIGURATION` | API key tidak dikonfigurasi di server |
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

## Security

### API Key Management

1. **Generate Secure Key**: Gunakan random string yang kuat untuk production
   ```bash
   # Generate random string (Linux/Mac)
   openssl rand -base64 32
   
   # Generate random string (Windows PowerShell)
   -join ((65..90) + (97..122) + (48..57) | Get-Random -Count 32 | % {[char]$_})
   ```

2. **Update .env**: Set `INTERNAL_API_KEY` di file `.env`

3. **Update Python Script**: Set `API_KEY` di `bulk_upload_laporan.py`

4. **Keep Secret**: Jangan commit API key ke git repository

### Rate Limiting

- Default: 60 requests per minute per IP
- Dapat diubah di `routes/api.php`

## Future Enhancements

- [x] API key authentication
- [x] Rate limiting
- [ ] IP whitelist (optional, untuk keamanan tambahan)
- [ ] Batch upload endpoint (multiple files in one request)
- [ ] Webhook untuk notifikasi hasil upload
- [ ] Download template via API
