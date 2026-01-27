@extends('Industri.layouts.sidebar')

@section('title', 'Tambah Industri Sekunder')

@push('styles')
<style>
    .container {
        max-width: 100%;
        margin: 0;
        padding: 20px 30px;
    }

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

    .alert {
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .alert-error {
        background: #fee2e2;
        border: 1px solid #fca5a5;
        color: #991b1b;
    }

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

    .required {
        color: #dc2626;
    }

    .form-input,
    .form-textarea,
    .form-select {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 14px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        transition: border-color 0.2s;
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

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .jenis-produksi-container {
        border: 2px dashed var(--border);
        border-radius: 8px;
        padding: 20px;
        background: #f8fafc;
    }

    .jenis-produksi-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .jenis-produksi-header h4 {
        color: var(--primary);
        font-size: 16px;
        font-weight: 600;
    }

    .jenis-produksi-item {
        background: white;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 12px;
    }

    .jenis-produksi-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .jenis-produksi-number {
        background: var(--accent);
        color: white;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 13px;
    }

    .btn-remove-item {
        background: #fee2e2;
        color: #dc2626;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 500;
    }

    .btn-remove-item:hover {
        background: #fecaca;
    }

    .btn-add-jenis {
        background: var(--accent);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-add-jenis:hover {
        background: #166534;
    }

    .file-upload-wrapper {
        border: 2px dashed #cbd5e1;
        border-radius: 8px;
        padding: 24px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
    }

    .file-upload-wrapper:hover {
        border-color: var(--accent);
        background: #f0fdf4;
    }

    .file-info {
        font-size: 12px;
        color: #64748b;
        margin-top: 6px;
    }

    .error-message {
        color: #dc2626;
        font-size: 13px;
        margin-top: 6px;
    }

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
    }

    .btn-secondary {
        background: #f1f5f9;
        color: var(--text-main);
    }

    .btn-secondary:hover {
        background: #e2e8f0;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-header">
        <h1 class="page-title">Tambah Industri Sekunder (PBUI)</h1>
        <p class="page-subtitle">Formulir pendaftaran industri sekunder pengolahan hasil hutan</p>
    </div>

    @if($errors->any())
    <div class="alert alert-error">
        <strong>Terdapat kesalahan:</strong>
        <ul style="margin: 8px 0 0 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="form-card">
        <form action="{{ route('industri-sekunder.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label">Nama Industri <span class="required">*</span></label>
                <input type="text" name="nama" class="form-input" placeholder="Contoh: PT. Mebel Jaya Indonesia" value="{{ old('nama') }}" required>
                @error('nama')<div class="error-message">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Alamat Lengkap <span class="required">*</span></label>
                <textarea name="alamat" class="form-textarea" placeholder="Masukkan alamat lengkap industri" required>{{ old('alamat') }}</textarea>
                @error('alamat')<div class="error-message">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Penanggung Jawab/Direktur <span class="required">*</span></label>
                    <input type="text" name="penanggungjawab" class="form-input" placeholder="Nama lengkap direktur" value="{{ old('penanggungjawab') }}" required>
                    @error('penanggungjawab')<div class="error-message">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Kabupaten/Kota <span class="required">*</span></label>
                    <select name="kabupaten" class="form-select" id="kabupatenSelect" required>
                        <option value="">-- Pilih Kabupaten/Kota --</option>
                    </select>
                    <div class="file-info">Memuat data wilayah...</div>
                    @error('kabupaten')<div class="error-message">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Latitude</label>
                    <input type="number" step="0.00000001" name="latitude" class="form-input" 
                           placeholder="Contoh: -7.250445" value="{{ old('latitude') }}">
                    <div class="file-info">Koordinat lintang (-90 sampai 90)</div>
                    @error('latitude')<div class="error-message">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Longitude</label>
                    <input type="number" step="0.00000001" name="longitude" class="form-input" 
                           placeholder="Contoh: 110.408447" value="{{ old('longitude') }}">
                    <div class="file-info">Koordinat bujur (-180 sampai 180)</div>
                    @error('longitude')<div class="error-message">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Kontak <span class="required">*</span></label>
                    <input type="text" name="kontak" class="form-input" placeholder="Nomor telepon/email" value="{{ old('kontak') }}" required>
                    @error('kontak')<div class="error-message">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Pemberi Izin <span class="required">*</span></label>
                    <select name="pemberi_izin" class="form-select" required>
                        <option value="">-- Pilih Pemberi Izin --</option>
                        <option value="Menteri Kehutanan" {{ old('pemberi_izin') == 'Menteri Kehutanan' ? 'selected' : '' }}>Menteri Kehutanan</option>
                        <option value="BKPM" {{ old('pemberi_izin') == 'BKPM' ? 'selected' : '' }}>BKPM</option>
                        <option value="Gubernur" {{ old('pemberi_izin') == 'Gubernur' ? 'selected' : '' }}>Gubernur</option>
                        <option value="Bupati/Walikota" {{ old('pemberi_izin') == 'Bupati/Walikota' ? 'selected' : '' }}>Bupati/Walikota</option>
                    </select>
                    @error('pemberi_izin')<div class="error-message">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Jenis Produksi & Kapasitas <span class="required">*</span></label>
                <div class="jenis-produksi-container">
                    <div class="jenis-produksi-header">
                        <h4>Daftar Jenis Produksi</h4>
                        <button type="button" class="btn-add-jenis" onclick="addJenisProduksi()">+ Tambah Jenis Produksi</button>
                    </div>
                    <div id="jenisProduksiList"></div>
                </div>
                @error('jenis_produksi')<div class="error-message">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tanggal SK <span class="required">*</span></label>
                    <input type="date" name="tanggal" class="form-input" value="{{ old('tanggal') }}" required>
                    @error('tanggal')<div class="error-message">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor SK/NIB/SS <span class="required">*</span></label>
                    <input type="text" name="nomor_izin" class="form-input" placeholder="Masukkan nomor izin/NIB/SS" value="{{ old('nomor_izin') }}" required>
                    @error('nomor_izin')<div class="error-message">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('industri-sekunder.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const masterJenisProduksi = @json($masterJenisProduksi);
    let jenisProduksiCounter = 0;
    let lainnyaId = null;

    // Cari ID untuk opsi "Lainnya"
    masterJenisProduksi.forEach(jp => {
        if (jp.nama === 'Lainnya') {
            lainnyaId = jp.id;
        }
    });

    function addJenisProduksi() {
        jenisProduksiCounter++;
        const container = document.getElementById('jenisProduksiList');
        
        let optionsHTML = '<option value="">-- Pilih Jenis Produksi --</option>';
        masterJenisProduksi.forEach(jp => {
            optionsHTML += `<option value="${jp.id}">${jp.nama}</option>`;
        });
        
        const item = document.createElement('div');
        item.className = 'jenis-produksi-item';
        item.setAttribute('data-index', jenisProduksiCounter);
        item.innerHTML = `
            <div class="jenis-produksi-item-header">
                <div class="jenis-produksi-number">${jenisProduksiCounter}</div>
                <button type="button" class="btn-remove-item" onclick="removeJenisProduksi(${jenisProduksiCounter})">
                    ✕ Hapus
                </button>
            </div>
            <div class="form-row">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Jenis Produksi</label>
                    <select name="jenis_produksi[]" class="form-select jenis-select" 
                            data-index="${jenisProduksiCounter}" 
                            onchange="toggleCustomInput(${jenisProduksiCounter})" required>
                        ${optionsHTML}
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Kapasitas Izin</label>
                    <input type="number" name="kapasitas_izin[]" class="form-input" 
                           placeholder="Contoh: 1000" min="0" required>
                </div>
            </div>
            <div class="form-group custom-input-container" id="customInput_${jenisProduksiCounter}" style="margin-top: 12px; display: none;">
                <label class="form-label">Sebutkan Jenis Produksi</label>
                <input type="text" name="nama_custom[]" class="form-input" 
                       placeholder="Masukkan jenis produksi..." 
                       data-index="${jenisProduksiCounter}">
                <div class="file-info" style="margin-top: 4px;">Isi kolom ini karena Anda memilih "Lainnya"</div>
            </div>
        `;
        container.appendChild(item);
    }

    function toggleCustomInput(index) {
        const select = document.querySelector(`.jenis-select[data-index="${index}"]`);
        const customContainer = document.getElementById(`customInput_${index}`);
        const customInput = customContainer.querySelector('input[name="nama_custom[]"]');
        
        if (select.value == lainnyaId) {
            customContainer.style.display = 'block';
            customInput.required = true;
        } else {
            customContainer.style.display = 'none';
            customInput.required = false;
            customInput.value = '';
        }
    }

    function removeJenisProduksi(index) {
        const item = document.querySelector(`.jenis-produksi-item[data-index="${index}"]`);
        if (item) {
            item.remove();
            renumberItems();
        }
    }

    function renumberItems() {
        const items = document.querySelectorAll('.jenis-produksi-item');
        items.forEach((item, idx) => {
            const number = item.querySelector('.jenis-produksi-number');
            if (number) number.textContent = idx + 1;
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Load kabupaten
        const kabupatenSelect = document.getElementById('kabupatenSelect');
        const loadingInfo = kabupatenSelect.nextElementSibling;
        
        fetch('https://www.emsifa.com/api-wilayah-indonesia/api/regencies/33.json')
            .then(response => response.json())
            .then(data => {
                loadingInfo.textContent = 'Pilih kabupaten/kota di Jawa Tengah';
                data.sort((a, b) => a.name.localeCompare(b.name));
                data.forEach(kabupaten => {
                    const option = document.createElement('option');
                    option.value = kabupaten.name;
                    option.textContent = kabupaten.name;
                    kabupatenSelect.appendChild(option);
                });
            })
            .catch(() => {
                loadingInfo.textContent = 'Gagal memuat data wilayah';
                loadingInfo.style.color = '#dc2626';
            });

        // Add initial item
        addJenisProduksi();
    });
</script>
@endpush
@endsection
