@extends('Industri.layouts.sidebar')

@section('title', 'Edit Industri Sekunder')

@push('styles')
<style>
        :root {
            --primary: #0f172a;
            --accent: #15803d;
            --bg-body: #f8fafc;
            --text-main: #334155;
            --white: #ffffff;
            --border: #e2e8f0;
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

        /* Navigation */
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

        /* Form Card */
        .form-card {
            background: var(--white);
            padding: 35px;
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .form-header {
            margin-bottom: 30px;
        }

        .form-title {
            font-size: 26px;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .form-subtitle {
            color: #64748b;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .required {
            color: #dc2626;
        }

        .form-input,
        .form-textarea,
        .form-select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s;
        }

        .form-input:focus,
        .form-textarea:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--accent);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid var(--border);
        }

        .btn {
            padding: 12px 28px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
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

        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .form-card {
                padding: 25px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="form-card">
            <div class="page-header" style="margin:0 0 20px 0; padding:18px 0;">
                <h1 class="page-title" style="font-size:24px; margin:0;">Edit Industri Sekunder (PBUI)</h1>
                <p class="page-subtitle" style="margin:6px 0 0 0;">Perbarui informasi perusahaan industri sekunder (PBUI)</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    ✓ {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <strong>Terjadi kesalahan:</strong>
                    <ul style="margin: 8px 0 0 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('industri-sekunder.update', $industriSekunder->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Data Perusahaan -->
                <h3 style="font-size: 18px; color: var(--primary); margin-bottom: 20px; padding-bottom: 6px;">
                    Data Perusahaan
                </h3>

                <div class="form-group">
                    <label class="form-label">Nama Perusahaan <span class="required">*</span></label>
                    <input type="text" name="nama" class="form-input" value="{{ old('nama', $industriSekunder->industri->nama) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Lengkap <span class="required">*</span></label>
                    <textarea name="alamat" class="form-textarea" required>{{ old('alamat', $industriSekunder->industri->alamat) }}</textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Kabupaten/Kota <span class="required">*</span></label>
                        <select name="kabupaten" id="kabupaten" class="form-select" required>
                            <option value="">-- Pilih Kabupaten/Kota --</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Penanggung Jawab <span class="required">*</span></label>
                        <input type="text" name="penanggungjawab" class="form-input" value="{{ old('penanggungjawab', $industriSekunder->industri->penanggungjawab) }}" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Kontak (Telepon/Email) <span class="required">*</span></label>
                        <input type="text" name="kontak" class="form-input" value="{{ old('kontak', $industriSekunder->industri->kontak) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nomor SK <span class="required">*</span></label>
                        <input type="text" name="nomor_izin" class="form-input" value="{{ old('nomor_izin', $industriSekunder->industri->nomor_izin) }}" required>
                    </div>
                </div>

                <!-- Data Produksi -->
                <h3 style="font-size: 18px; color: var(--primary); margin: 30px 0 20px; padding-bottom: 6px;">
                    Data Produksi & Izin
                </h3>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Pemberi Izin <span class="required">*</span></label>
                        <select name="pemberi_izin" class="form-select" required>
                            <option value="">-- Pilih Pemberi Izin --</option>
                            <option value="Menteri Kehutanan" {{ old('pemberi_izin', $industriSekunder->pemberi_izin) == 'Menteri Kehutanan' ? 'selected' : '' }}>Menteri Kehutanan</option>
                            <option value="BKPM" {{ old('pemberi_izin', $industriSekunder->pemberi_izin) == 'BKPM' ? 'selected' : '' }}>BKPM</option>
                            <option value="Gubernur" {{ old('pemberi_izin', $industriSekunder->pemberi_izin) == 'Gubernur' ? 'selected' : '' }}>Gubernur</option>
                            <option value="Bupati/Walikota" {{ old('pemberi_izin', $industriSekunder->pemberi_izin) == 'Bupati/Walikota' ? 'selected' : '' }}>Bupati/Walikota</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jenis Produksi <span class="required">*</span></label>
                        <input type="text" name="jenis_produksi" class="form-input" value="{{ old('jenis_produksi', $industriSekunder->jenis_produksi) }}" placeholder="Contoh: Plywood, Furniture" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Kapasitas Izin <span class="required">*</span></label>
                        <input type="text" name="kapasitas_izin" class="form-input" value="{{ old('kapasitas_izin', $industriSekunder->kapasitas_izin) }}" placeholder="Contoh: 5000 m³/tahun" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal SK <span class="required">*</span></label>
                        <input type="date" name="tanggal" class="form-input" value="{{ old('tanggal', $industriSekunder->industri->tanggal) }}" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('industri-sekunder.index') }}" class="btn btn-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Load kabupaten dari API wilayah.id
        async function loadKabupaten() {
            const kabupatenSelect = document.getElementById('kabupaten');
            const currentKabupaten = "{{ old('kabupaten', $industriSekunder->industri->kabupaten) }}";
            
            try {
                const response = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/regencies/33.json');
                const data = await response.json();
                
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.name;
                    option.textContent = item.name;
                    
                    if (item.name === currentKabupaten) {
                        option.selected = true;
                    }
                    
                    kabupatenSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading kabupaten:', error);
                // Fallback: set current value
                const option = document.createElement('option');
                option.value = currentKabupaten;
                option.textContent = currentKabupaten;
                option.selected = true;
                kabupatenSelect.appendChild(option);
            }
        }

        // Load data saat halaman dibuka
        loadKabupaten();

        // Auto-hide success alerts
        document.addEventListener('DOMContentLoaded', function() {
            const successAlerts = document.querySelectorAll('.alert-success');
            successAlerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });
    </script>
@endpush
