@extends('Industri.layouts.sidebar')

@section('title', 'Tambah TPT-KB')

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
<style>
        :root {
            --primary: #0f172a;
            --accent: #15803d;
            --bg-body: #f8fafc;
            --text-main: #334155;
            --white: #ffffff;
            --border: #e2e8f0;
            --error: #dc2626;
            --success: #16a34a;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            line-height: 1.6;
        }

        .container {
            max-width: 100%;
            margin: 0 ;
            padding: 20px 30px;
        }

        nav {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 20px 0;
            margin-bottom: 30px;
        }

        .nav-content {
            max-width: 100%;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-text {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary);
        }

        .back-link {
            text-decoration: none;
            color: var(--accent);
            font-weight: 500;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: opacity 0.2s;
        }

        .back-link:hover {
            opacity: 0.7;
        }

        /* Header Section */
        .page-header {
            background: var(--white);
            padding: 30px;
            border-radius: 12px;
            border: 1px solid var(--border);
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 28px;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .page-subtitle {
            color: #64748b;
            font-size: 14px;
        }

        /* Alert Success */
        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }

        .alert-success {
            background: #dcfce7;
            border: 1px solid #86efac;
            color: #166534;
        }

        .alert-error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        /* Form Card */
        .form-card {
            background: var(--white);
            padding: 40px;
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-label .required {
            color: var(--error);
            margin-left: 2px;
        }

        .form-input,
        .form-textarea,
        .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-input:focus,
        .form-textarea:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(21, 128, 61, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .file-info {
            font-size: 12px;
            color: #64748b;
            margin-top: 6px;
        }

        .error-message {
            color: var(--error);
            font-size: 13px;
            margin-top: 6px;
        }

        /* Grid Layout untuk form */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* Buttons */
        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: var(--accent);
            color: white;
        }

        .btn-primary:hover {
            background: #166534;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(21, 128, 61, 0.3);
        }

        .btn-secondary {
            background: #f1f5f9;
            color: var(--text-main);
        }

        .btn-secondary:hover {
            background: #e2e8f0;
        }

        .btn-add-more {
            background: #15803d;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            margin-top: 15px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-add-more:hover {
            background: #166534;
            transform: translateY(-1px);
        }

        .btn-remove-sumber {
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 500;
            white-space: nowrap;
        }

        .btn-remove-sumber:hover {
            background: #dc2626;
        }

        .sumber-item {
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid var(--border);
        }

        .item-number {
            font-size: 14px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--accent);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-card {
                padding: 24px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Tambah TPT-KB</h1>
            <p class="page-subtitle">Formulir pendaftaran Tempat Penampungan Tebangan Kayu Bulat</p>
        </div>

        <!-- Success Alert -->
        @if(session('success'))
        <div class="alert alert-success">
            <span>✓</span>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <!-- Error Alert -->
        @if($errors->any())
        <div class="alert alert-error">
            <span>⚠</span>
            <span>Terdapat kesalahan pada form. Silakan periksa kembali.</span>
        </div>
        @endif

        <!-- Form Card -->
        <div class="form-card">
            <form action="{{ route('tptkb.store') }}" method="POST">
                @csrf

                <!-- Nama Perusahaan -->
                <div class="form-group">
                    <label class="form-label">
                        Nama Perusahaan <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nama" 
                        class="form-input" 
                        placeholder="Contoh: PT. Kayu Jaya Makmur"
                        value="{{ old('nama') }}"
                        required
                    >
                    @error('nama')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Alamat -->
                <div class="form-group">
                    <label class="form-label">
                        Alamat Lengkap <span class="required">*</span>
                    </label>
                    <textarea 
                        name="alamat" 
                        class="form-textarea" 
                        placeholder="Masukkan alamat lengkap perusahaan"
                        required
                    >{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Row: Kabupaten & Penanggung Jawab -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            Kabupaten/Kota <span class="required">*</span>
                        </label>
                        <select 
                            name="kabupaten" 
                            class="form-select" 
                            required
                            id="kabupatenSelect"
                        >
                            <option value="">-- Pilih Kabupaten/Kota --</option>
                        </select>
                        <div class="file-info">Memuat data wilayah...</div>
                        @error('kabupaten')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Penanggung Jawab/Direktur <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="penanggungjawab" 
                            class="form-input" 
                            placeholder="Nama lengkap direktur"
                            value="{{ old('penanggungjawab') }}"
                            required
                        >
                        @error('penanggungjawab')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Row: Kontak & Nomor Izin -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            Kontak <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="kontak" 
                            class="form-input" 
                            placeholder="Nomor telepon/email"
                            value="{{ old('kontak') }}"
                            required
                        >
                        @error('kontak')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Nomor SK <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="nomor_izin" 
                            class="form-input" 
                            placeholder="Masukkan nomor izin"
                            value="{{ old('nomor_izin') }}"
                            required
                        >
                        @error('nomor_izin')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Row: Latitude & Longitude -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Latitude</label>
                        <input 
                            type="number" 
                            step="0.00000001" 
                            name="latitude" 
                            id="latitude"
                            class="form-input" 
                            placeholder="Contoh: -7.250445" 
                            value="{{ old('latitude') }}"
                            onchange="updateMapFromInputs()"
                        >
                        <div class="file-info">Koordinat lintang (-90 sampai 90)</div>
                        @error('latitude')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Longitude</label>
                        <input 
                            type="number" 
                            step="0.00000001" 
                            name="longitude" 
                            id="longitude"
                            class="form-input" 
                            placeholder="Contoh: 110.408447" 
                            value="{{ old('longitude') }}"
                            onchange="updateMapFromInputs()"
                        >
                        <div class="file-info">Koordinat bujur (-180 sampai 180)</div>
                        @error('longitude')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Map Container -->
                <div class="form-group">
                    <label class="form-label">Pilih Lokasi dari Peta</label>
                    <div id="map" style="height: 400px; border-radius: 8px; border: 1px solid var(--border);"></div>
                    <div class="file-info" style="margin-top: 8px;">
                        Klik pada peta untuk memilih lokasi, atau isi koordinat secara manual di atas
                    </div>
                </div>

                <!-- Row: Pemberi Izin & Sumber Bahan Baku -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            Pemberi Izin <span class="required">*</span>
                        </label>
                        <select 
                            name="pemberi_izin" 
                            class="form-select" 
                            required
                        >
                            <option value="">-- Pilih Pemberi Izin --</option>
                            <option value="Gubernur" {{ old('pemberi_izin') == 'Gubernur' ? 'selected' : '' }}>Gubernur</option>
                        </select>
                        @error('pemberi_izin')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Sumber Bahan Baku (Dynamic) -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 30px;">
                    <div>
                        <div class="section-title" style="margin: 0; padding-bottom: 10px; border-bottom: 2px solid var(--accent);">
                            Sumber Bahan Baku & Kapasitas <span class="required">*</span>
                        </div>
                        <div style="font-size: 14px; color: #64748b; margin-top: 10px;">Daftar Sumber Bahan Baku</div>
                    </div>
                    <button type="button" class="btn btn-add-more" onclick="addSumber()">
                        + Tambah Sumber Bahan Baku
                    </button>
                </div>
                
                <div id="sumber-container" style="margin-top: 20px;">
                    <div class="sumber-item" data-index="0">
                        <div style="display: flex; align-items: flex-start; gap: 15px;">
                            <div style="width: 40px; padding-top: 35px;">
                                <span class="item-number" style="display: inline-block; width: 32px; height: 32px; background: #15803d; color: white; border-radius: 50%; text-align: center; line-height: 32px; font-weight: 600;">1</span>
                            </div>
                            
                            <div style="flex: 1;">
                                <div class="form-group" style="margin-bottom: 15px;">
                                    <label class="form-label">Sumber Bahan Baku</label>
                                    <select 
                                        name="sumber_id[]" 
                                        class="form-select sumber-select" 
                                        onchange="toggleCustomSumber(this)"
                                        required
                                    >
                                        <option value="">-- Pilih Sumber Bahan Baku --</option>
                                        @foreach($masterSumber as $sumber)
                                            <option value="{{ $sumber->id }}" data-nama="{{ $sumber->nama }}">{{ $sumber->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="custom-sumber-container" style="display: none; margin-bottom: 15px;">
                                    <label class="form-label">Nama Sumber (Lainnya)</label>
                                    <input 
                                        type="text" 
                                        name="sumber_custom[]" 
                                        class="form-input custom-sumber-input" 
                                        placeholder="Masukkan nama sumber bahan baku"
                                    >
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Kapasitas Izin</label>
                                    <input 
                                        type="number" 
                                        step="0.01"
                                        name="kapasitas[]" 
                                        class="form-input" 
                                        placeholder="Contoh: 1000"
                                        required
                                    >
                                </div>
                            </div>

                            <div style="width: 80px; padding-top: 35px;">
                                <button type="button" class="btn-remove-sumber" onclick="removeSumber(this)" style="display: none;">
                                    <span>✕ Hapus</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row: Tanggal SK & Masa Berlaku -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            Tanggal SK <span class="required">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="tanggal" 
                            class="form-input" 
                            value="{{ old('tanggal') }}"
                            required
                        >
                        @error('tanggal')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Masa Berlaku -->
                <div class="form-group">
                    <label class="form-label">
                        Masa Berlaku Izin <span class="required">*</span>
                    </label>
                    <input 
                        type="date" 
                        name="masa_berlaku" 
                        class="form-input" 
                        value="{{ old('masa_berlaku') }}"
                        required
                    >
                    @error('masa_berlaku')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('industri.dashboard') }}" class="btn btn-secondary">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                         Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Load data kabupaten Jawa Tengah dari API wilayah.id
        // ID Provinsi Jawa Tengah = 33
        document.addEventListener('DOMContentLoaded', function() {
            const kabupatenSelect = document.getElementById('kabupatenSelect');
            const loadingInfo = kabupatenSelect.nextElementSibling;
            const oldValue = "{{ old('kabupaten') }}";

            fetch('https://www.emsifa.com/api-wilayah-indonesia/api/regencies/33.json')
                .then(response => response.json())
                .then(data => {
                    loadingInfo.textContent = 'Pilih kabupaten/kota di Jawa Tengah';
                    loadingInfo.style.color = '#64748b';
                    
                    data.sort((a, b) => a.name.localeCompare(b.name));
                    
                    data.forEach(kabupaten => {
                        const option = document.createElement('option');
                        option.value = kabupaten.name;
                        option.textContent = kabupaten.name;
                        if (oldValue && oldValue === kabupaten.name) {
                            option.selected = true;
                        }
                        kabupatenSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading wilayah data:', error);
                    loadingInfo.textContent = 'Gagal memuat data wilayah';
                    loadingInfo.style.color = 'var(--error)';
                });
        });

        // Auto-hide success alert after 5 seconds
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.transition = 'opacity 0.3s';
                successAlert.style.opacity = '0';
                setTimeout(() => successAlert.remove(), 300);
            }, 5000);
        }

        // Map variables
        let map;
        let marker;
        const defaultLat = -7.150975; // Jawa Tengah center
        const defaultLng = 110.1402594;

        function initMap() {
            // Initialize the map centered on Jawa Tengah
            map = L.map('map').setView([defaultLat, defaultLng], 8);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            // Add click event to map
            map.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                
                // Update marker position
                updateMarker(lat, lng);
                
                // Update input fields
                document.getElementById('latitude').value = lat.toFixed(8);
                document.getElementById('longitude').value = lng.toFixed(8);
            });

            // Load existing coordinates if any
            const existingLat = document.getElementById('latitude').value;
            const existingLng = document.getElementById('longitude').value;
            
            if (existingLat && existingLng) {
                const lat = parseFloat(existingLat);
                const lng = parseFloat(existingLng);
                if (!isNaN(lat) && !isNaN(lng)) {
                    updateMarker(lat, lng);
                    map.setView([lat, lng], 13);
                }
            }
        }

        function updateMarker(lat, lng) {
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng], {
                    draggable: true
                }).addTo(map);
                
                // Add drag event to marker
                marker.on('dragend', function(e) {
                    const position = marker.getLatLng();
                    document.getElementById('latitude').value = position.lat.toFixed(8);
                    document.getElementById('longitude').value = position.lng.toFixed(8);
                });
            }
            
            marker.bindPopup(`Lokasi: ${lat.toFixed(6)}, ${lng.toFixed(6)}`).openPopup();
        }

        function updateMapFromInputs() {
            const lat = parseFloat(document.getElementById('latitude').value);
            const lng = parseFloat(document.getElementById('longitude').value);
            
            if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                updateMarker(lat, lng);
                map.setView([lat, lng], 13);
            }
        }

        // Dynamic Sumber Bahan Baku
        let sumberIndex = 1;

        function toggleCustomSumber(selectElement) {
            const sumberItem = selectElement.closest('.sumber-item');
            const customContainer = sumberItem.querySelector('.custom-sumber-container');
            const customInput = sumberItem.querySelector('.custom-sumber-input');
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const namaSumber = selectedOption.getAttribute('data-nama');
            
            if (namaSumber === 'Lainnya') {
                customContainer.style.display = 'block';
                customInput.required = true;
            } else {
                customContainer.style.display = 'none';
                customInput.required = false;
                customInput.value = '';
            }
        }

        function addSumber() {
            const container = document.getElementById('sumber-container');
            const newItem = document.createElement('div');
            newItem.className = 'sumber-item';
            newItem.setAttribute('data-index', sumberIndex);
            
            sumberIndex++;
            const itemNumber = sumberIndex;
            
            newItem.innerHTML = `
                <div style="display: flex; align-items: flex-start; gap: 15px;">
                    <div style="width: 40px; padding-top: 35px;">
                        <span class="item-number" style="display: inline-block; width: 32px; height: 32px; background: #15803d; color: white; border-radius: 50%; text-align: center; line-height: 32px; font-weight: 600;">${itemNumber}</span>
                    </div>
                    
                    <div style="flex: 1;">
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label class="form-label">Sumber Bahan Baku</label>
                            <select 
                                name="sumber_id[]" 
                                class="form-select sumber-select" 
                                onchange="toggleCustomSumber(this)"
                                required
                            >
                                <option value="">-- Pilih Sumber Bahan Baku --</option>
                                @foreach($masterSumber as $sumber)
                                    <option value="{{ $sumber->id }}" data-nama="{{ $sumber->nama }}">{{ $sumber->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="custom-sumber-container" style="display: none; margin-bottom: 15px;">
                            <label class="form-label">Nama Sumber (Lainnya)</label>
                            <input 
                                type="text" 
                                name="sumber_custom[]" 
                                class="form-input custom-sumber-input" 
                                placeholder="Masukkan nama sumber bahan baku"
                            >
                        </div>

                        <div class="form-group">
                            <label class="form-label">Kapasitas Izin</label>
                            <input 
                                type="number" 
                                step="0.01"
                                name="kapasitas[]" 
                                class="form-input" 
                                placeholder="Contoh: 1000"
                                required
                            >
                        </div>
                    </div>

                    <div style="width: 80px; padding-top: 35px;">
                        <button type="button" class="btn-remove-sumber" onclick="removeSumber(this)">
                            <span>✕ Hapus</span>
                        </button>
                    </div>
                </div>
            `;
            
            container.appendChild(newItem);
            updateRemoveButtons();
            updateItemNumbers();
        }

        function removeSumber(button) {
            const sumberItem = button.closest('.sumber-item');
            sumberItem.remove();
            updateRemoveButtons();
            updateItemNumbers();
        }

        function updateRemoveButtons() {
            const items = document.querySelectorAll('.sumber-item');
            items.forEach((item, index) => {
                const removeBtn = item.querySelector('.btn-remove-sumber');
                if (removeBtn) {
                    removeBtn.style.display = items.length > 1 ? 'flex' : 'none';
                }
            });
        }

        function updateItemNumbers() {
            const items = document.querySelectorAll('.sumber-item');
            items.forEach((item, index) => {
                const numberSpan = item.querySelector('.item-number');
                if (numberSpan) {
                    numberSpan.textContent = index + 1;
                }
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateRemoveButtons();
            initMap();
        });
    </script>
@endpush

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>
