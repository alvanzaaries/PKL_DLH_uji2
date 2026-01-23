#!/usr/bin/env python3
"""
Script Python untuk bulk upload laporan ke API DLHK
Menggunakan API endpoint /api/laporan/upload

Requirements:
    pip install requests

Usage:
    python bulk_upload_laporan.py
"""

import requests
import json
import os
from pathlib import Path
from typing import Dict, List, Optional

# Konfigurasi
API_BASE_URL = "http://localhost:8000/api"  # Sesuaikan dengan URL server Anda
UPLOAD_ENDPOINT = f"{API_BASE_URL}/laporan/upload"

# API Key untuk autentikasi (harus sama dengan INTERNAL_API_KEY di .env)
API_KEY = "dlhk_internal_api_key_2024_secure_random_string"

# Jenis laporan yang tersedia
JENIS_LAPORAN = [
    "Laporan Penerimaan Kayu Bulat",
    "Laporan Mutasi Kayu Bulat (LMKB)",
    "Laporan Penerimaan Kayu Olahan",
    "Laporan Mutasi Kayu Olahan (LMKO)",
    "Laporan Penjualan Kayu Olahan"
]


def upload_laporan(
    file_path: str,
    industri_id: int,
    bulan: int,
    tahun: int,
    jenis_laporan: str
) -> Dict:
    """
    Upload satu file laporan ke API
    
    Args:
        file_path: Path ke file Excel (.xlsx atau .xls)
        industri_id: ID industri (dari database)
        bulan: Bulan laporan (1-12)
        tahun: Tahun laporan (minimal 2020)
        jenis_laporan: Jenis laporan (harus sesuai dengan JENIS_LAPORAN)
    
    Returns:
        Dictionary response dari API
    """
    
    # Validasi file exists
    if not os.path.exists(file_path):
        return {
            "success": False,
            "message": f"File tidak ditemukan: {file_path}",
            "error_code": "FILE_NOT_FOUND"
        }
    
    # Validasi jenis laporan
    if jenis_laporan not in JENIS_LAPORAN:
        return {
            "success": False,
            "message": f"Jenis laporan tidak valid. Harus salah satu dari: {', '.join(JENIS_LAPORAN)}",
            "error_code": "INVALID_JENIS"
        }
    
    # Prepare data
    data = {
        'industri_id': industri_id,
        'bulan': bulan,
        'tahun': tahun,
        'jenis_laporan': jenis_laporan
    }
    
    # Prepare headers with API key
    headers = {
        'X-API-Key': API_KEY
    }
    
    # Prepare file
    with open(file_path, 'rb') as f:
        files = {'file_excel': (os.path.basename(file_path), f, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')}
        
        try:
            response = requests.post(UPLOAD_ENDPOINT, data=data, files=files, headers=headers, timeout=60)
            
            # Parse JSON response
            try:
                result = response.json()
            except json.JSONDecodeError:
                result = {
                    "success": False,
                    "message": f"Invalid JSON response from server. Status code: {response.status_code}",
                    "error_code": "INVALID_RESPONSE"
                }
            
            # Tambahkan status code ke result
            result['http_status'] = response.status_code
            
            return result
            
        except requests.exceptions.Timeout:
            return {
                "success": False,
                "message": "Request timeout. Server tidak merespons dalam waktu yang ditentukan.",
                "error_code": "TIMEOUT"
            }
        except requests.exceptions.RequestException as e:
            return {
                "success": False,
                "message": f"Error saat menghubungi server: {str(e)}",
                "error_code": "CONNECTION_ERROR"
            }


def bulk_upload(upload_configs: List[Dict]) -> Dict[str, int]:
    """
    Upload banyak laporan sekaligus
    
    Args:
        upload_configs: List of dict dengan keys: file_path, industri_id, bulan, tahun, jenis_laporan
    
    Returns:
        Dictionary dengan statistik upload
    """
    stats = {
        'success': 0,
        'failed': 0,
        'duplicate': 0,
        'validation_error': 0
    }
    
    results = []
    
    print(f"Memulai upload {len(upload_configs)} laporan...\n")
    
    for i, config in enumerate(upload_configs, 1):
        print(f"[{i}/{len(upload_configs)}] Uploading {config['file_path']}...")
        print(f"  Industri ID: {config['industri_id']}, Periode: {config['bulan']}/{config['tahun']}")
        print(f"  Jenis: {config['jenis_laporan']}")
        
        result = upload_laporan(
            file_path=config['file_path'],
            industri_id=config['industri_id'],
            bulan=config['bulan'],
            tahun=config['tahun'],
            jenis_laporan=config['jenis_laporan']
        )
        
        if result.get('success'):
            stats['success'] += 1
            print(f"  ✅ SUCCESS - Laporan ID: {result['data']['laporan_id']}")
        else:
            error_code = result.get('error_code', 'UNKNOWN')
            
            if error_code == 'DUPLICATE_LAPORAN':
                stats['duplicate'] += 1
                print(f"  ⚠️  DUPLICATE - {result['message']}")
            elif error_code == 'VALIDATION_ERROR':
                stats['validation_error'] += 1
                print(f"  ❌ VALIDATION ERROR")
                if 'errors' in result:
                    for error in result['errors'][:3]:  # Show first 3 errors
                        print(f"     - {error}")
                    if len(result['errors']) > 3:
                        print(f"     ... dan {len(result['errors']) - 3} error lainnya")
            else:
                stats['failed'] += 1
                print(f"  ❌ FAILED - {result['message']}")
        
        results.append(result)
        print()
    
    return stats, results


# Contoh penggunaan
if __name__ == "__main__":
    # Contoh konfigurasi upload
    upload_configs = [
        {
            'file_path': 'laporan_penerimaan_kb_jan_2024.xlsx',
            'industri_id': 1,
            'bulan': 1,
            'tahun': 2024,
            'jenis_laporan': 'Laporan Penerimaan Kayu Bulat'
        },
        {
            'file_path': 'laporan_mutasi_kb_jan_2024.xlsx',
            'industri_id': 1,
            'bulan': 1,
            'tahun': 2024,
            'jenis_laporan': 'Laporan Mutasi Kayu Bulat (LMKB)'
        },
        {
            'file_path': 'laporan_penerimaan_ko_jan_2024.xlsx',
            'industri_id': 1,
            'bulan': 1,
            'tahun': 2024,
            'jenis_laporan': 'Laporan Penerimaan Kayu Olahan'
        }
    ]
    
    # Test koneksi terlebih dahulu
    print("Testing API connection...")
    try:
        health_check = requests.get(f"{API_BASE_URL}/health", timeout=5)
        if health_check.status_code == 200:
            print("✅ API server is reachable\n")
        else:
            print(f"⚠️  API responded with status code: {health_check.status_code}\n")
    except requests.exceptions.RequestException as e:
        print(f"❌ Cannot connect to API server: {e}")
        print("Please check API_BASE_URL configuration and make sure the server is running.\n")
        exit(1)
    
    # Jalankan bulk upload
    stats, results = bulk_upload(upload_configs)
    
    # Print summary
    print("="*60)
    print("UPLOAD SUMMARY")
    print("="*60)
    print(f"Total: {len(upload_configs)}")
    print(f"✅ Success: {stats['success']}")
    print(f"⚠️  Duplicate: {stats['duplicate']}")
    print(f"❌ Validation Error: {stats['validation_error']}")
    print(f"❌ Failed: {stats['failed']}")
    print("="*60)
