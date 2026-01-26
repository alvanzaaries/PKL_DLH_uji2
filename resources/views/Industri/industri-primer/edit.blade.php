@extends('Industri.layouts.sidebar')

@section('title', 'Edit Industri Primer')

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

    .current-file {
        padding: 12px;
        background: #f1f5f9;
        border-radius: 6px;
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .current-file a {
        color: var(--accent);
        font-weight: 500;
        text-decoration: none;
    }

    .current-file a:hover {
        text-decoration: underline;
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
        <h1 class="page-title">Edit Data Industri Primer</h1>
        <p class="page-subtitle">Perbarui informasi perusahaan industri primer (PBPHH)</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">✓ {{ session('success') }}</div>
    @endif

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
        <form action="{{ route('industri-primer.update', $industriPrimer->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Nama Industri <span class="required">*</span></label>
                <input type="text" name="nama" class="form-input" value="{{ old('nama', $industriPrimer->industri->nama) }}" required>
                @error('nama')<div class="error-message">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Alamat Lengkap <span class="required">*</span></label>
                <textarea name="alamat" class="form-textarea" required>{{ old('alamat', $industriPrimer->industri->alamat) }}</textarea>
                @error('alamat')<div class="error-message">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Penanggung Jawab/Direktur <span class="required">*</span></label>
                    <input type="text" name="penanggungjawab" class="form-input" value="{{ old('penanggungjawab', $industriPrimer->industri->penanggungjawab) }}" required>
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
                    <label class="form-label">Kontak <span class="required">*</span></label>
                    <input type="text" name="kontak" class="form-input" value="{{ old('kontak', $industriPrimer->industri->kontak) }}" required>
                    @error('kontak')<div class="error-message">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Pemberi Izin <span class="required">*</span></label>
                    <select name="pemberi_izin" class="form-select" required>
                        <option value="">-- Pilih Pemberi Izin --</option>
                        @foreach(['Menteri Kehutanan', 'BKPM', 'Gubernur', 'Bupati/Walikota'] as $pemberi)
                            <option value="{{ $pemberi }}" {{ old('pemberi_izin', $industriPrimer->pemberi_izin) == $pemberi ? 'selected' : '' }}>{{ $pemberi }}</option>
                        @endforeach
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
                    <input type="date" name="tanggal" class="form-input" value="{{ old('tanggal', $industriPrimer->industri->tanggal) }}" required>
                    @error('tanggal')<div class="error-message">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor SK/NIB/SS <span class="required">*</span></label>
                    <input type="text" name="nomor_izin" class="form-input" value="{{ old('nomor_izin', $industriPrimer->industri->nomor_izin) }}" required>
                    @error('nomor_izin')<div class="error-message">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Status Industri <span class="required">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="Aktif" {{ old('status', $industriPrimer->industri->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Tidak Aktif" {{ old('status', $industriPrimer->industri->status) == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
                @error('status')<div class="error-message">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Upload Dokumen Izin (PDF)</label>
                @if($industriPrimer->dokumen_izin)
                    <div class="current-file" style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <a href="{{ route('industri-primer.view-dokumen', $industriPrimer->id) }}" 
                               target="_blank"
                               style="color: #15803d; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-weight: 500; padding: 8px 16px; background: #f0fdf4; border-radius: 6px; border: 1px solid #bbf7d0; transition: all 0.2s;"
                               onmouseover="this.style.background='#dcfce7'"
                               onmouseout="this.style.background='#f0fdf4'">
                                <i class="fas fa-eye" style="color: #15803d;"></i>
                                <span>Lihat Dokumen</span>
                                <i class="fas fa-external-link-alt" style="font-size: 12px;"></i>
                            </a>
                            <a href="{{ route('industri-primer.download-dokumen', $industriPrimer->id) }}" 
                               style="color: #dc2626; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-weight: 500; padding: 8px 16px; background: #fef2f2; border-radius: 6px; border: 1px solid #fecaca; transition: all 0.2s;"
                               onmouseover="this.style.background='#fee2e2'"
                               onmouseout="this.style.background='#fef2f2'">
                                <i class="fas fa-download" style="color: #dc2626;"></i>
                                <span>Download PDF</span>
                            </a>
                        </div>
                        <button type="button" onclick="confirmDeleteDokumen()" style="background: #fee2e2; color: #dc2626; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 500;">
                            ✕ Hapus Dokumen
                        </button>
                    </div>
                @endif
                <div class="file-upload-wrapper" onclick="document.getElementById('dokumenInput').click()">
                    <div style="font-size: 48px; color: #94a3b8; margin-bottom: 12px;">📄</div>
                    <div style="font-size: 14px; color: #475569;">Klik untuk {{ $industriPrimer->dokumen_izin ? 'ganti' : 'pilih' }} file PDF</div>
                    <div class="file-info">Format: PDF | Maksimal: 5 MB</div>
                    <input type="file" id="dokumenInput" name="dokumen_izin" accept=".pdf" style="display: none;">
                    <input type="hidden" name="hapus_dokumen" id="hapusDokumenFlag" value="0">
                </div>
                @error('dokumen_izin')<div class="error-message">{{ $message }}</div>@enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('industri-primer.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Update Data</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const masterJenisProduksi = @json($masterJenisProduksi);
    const existingJenisProduksi = @json($industriPrimer->jenisProduksi);
    let jenisProduksiCounter = 0;
    let lainnyaId = null;

    // Cari ID untuk opsi "Lainnya"
    masterJenisProduksi.forEach(jp => {
        if (jp.nama === 'Lainnya') {
            lainnyaId = jp.id;
        }
    });

    function addJenisProduksi(selectedId = null, kapasitas = '', customName = '') {
        jenisProduksiCounter++;
        const container = document.getElementById('jenisProduksiList');
        
        let optionsHTML = '<option value="">-- Pilih Jenis Produksi --</option>';
        masterJenisProduksi.forEach(jp => {
            const selected = selectedId && jp.id == selectedId ? 'selected' : '';
            optionsHTML += `<option value="${jp.id}" ${selected}>${jp.nama}</option>`;
        });
        
        const showCustom = selectedId == lainnyaId ? 'block' : 'none';
        const requiredCustom = selectedId == lainnyaId ? 'required' : '';
        
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
                    <input type="text" name="kapasitas_izin[]" class="form-input" 
                           placeholder="Contoh: 1000 m³/tahun" value="${kapasitas}" required>
                </div>
            </div>
            <div class="form-group custom-input-container" id="customInput_${jenisProduksiCounter}" 
                 style="margin-top: 12px; display: ${showCustom};">
                <label class="form-label">Sebutkan Jenis Produksi</label>
                <input type="text" name="nama_custom[]" class="form-input" 
                       placeholder="Masukkan jenis produksi..." 
                       value="${customName}"
                       data-index="${jenisProduksiCounter}" ${requiredCustom}>
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
        const currentKabupaten = "{{ $industriPrimer->industri->kabupaten }}";
        
        fetch('https://www.emsifa.com/api-wilayah-indonesia/api/regencies/33.json')
            .then(response => response.json())
            .then(data => {
                loadingInfo.textContent = 'Pilih kabupaten/kota di Jawa Tengah';
                data.sort((a, b) => a.name.localeCompare(b.name));
                data.forEach(kabupaten => {
                    const option = document.createElement('option');
                    option.value = kabupaten.name;
                    option.textContent = kabupaten.name;
                    if (kabupaten.name === currentKabupaten) {
                        option.selected = true;
                    }
                    kabupatenSelect.appendChild(option);
                });
            })
            .catch(() => {
                loadingInfo.textContent = 'Gagal memuat data wilayah';
                loadingInfo.style.color = '#dc2626';
            });

        // Load existing jenis produksi
        if (existingJenisProduksi && existingJenisProduksi.length > 0) {
            existingJenisProduksi.forEach(jp => {
                addJenisProduksi(jp.id, jp.pivot.kapasitas_izin, jp.pivot.nama_custom || '');
            });
        } else {
            addJenisProduksi();
        }
    });

    function confirmDeleteDokumen() {
        if (confirm('Apakah Anda yakin ingin menghapus dokumen ini?')) {
            document.getElementById('hapusDokumenFlag').value = '1';
            const currentFileDiv = document.querySelector('.current-file');
            if (currentFileDiv) {
                currentFileDiv.innerHTML = '<div style="color: #dc2626; font-style: italic;">Dokumen akan dihapus saat Anda klik Update Data</div>';
            }
        }
    }

    // Event listener untuk menampilkan preview file baru yang dipilih
    document.getElementById('dokumenInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validasi file
            if (file.type !== 'application/pdf') {
                alert('File harus berformat PDF!');
                e.target.value = '';
                return;
            }
            
            if (file.size > 5 * 1024 * 1024) { // 5MB
                alert('Ukuran file maksimal 5 MB!');
                e.target.value = '';
                return;
            }

            // Sembunyikan file lama jika ada
            const currentFileDiv = document.querySelector('.current-file');
            if (currentFileDiv) {
                currentFileDiv.style.display = 'none';
            }

            // Reset flag hapus dokumen
            document.getElementById('hapusDokumenFlag').value = '0';

            // Tampilkan preview file baru
            const uploadWrapper = document.querySelector('.file-upload-wrapper');
            const existingPreview = document.getElementById('newFilePreview');
            if (existingPreview) {
                existingPreview.remove();
            }

            const previewDiv = document.createElement('div');
            previewDiv.id = 'newFilePreview';
            previewDiv.style.cssText = 'margin-top: 12px; padding: 12px; background: #dcfce7; border: 1px solid #86efac; border-radius: 6px; display: flex; justify-content: space-between; align-items: center;';
            previewDiv.innerHTML = `
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-file-pdf" style="color: #dc2626; font-size: 20px;"></i>
                    <div>
                        <div style="font-weight: 600; color: #166534;">${file.name}</div>
                        <div style="font-size: 12px; color: #15803d;">${(file.size / 1024).toFixed(2)} KB - File baru akan diupload</div>
                    </div>
                </div>
                <button type="button" onclick="cancelNewFile()" style="background: #fee2e2; color: #dc2626; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 500;">
                    ✕ Batal
                </button>
            `;
            uploadWrapper.parentNode.insertBefore(previewDiv, uploadWrapper.nextSibling);
        }
    });

    // Fungsi untuk membatalkan file baru yang dipilih
    window.cancelNewFile = function() {
        const fileInput = document.getElementById('dokumenInput');
        fileInput.value = '';
        
        const preview = document.getElementById('newFilePreview');
        if (preview) {
            preview.remove();
        }

        // Tampilkan kembali file lama jika ada
        const currentFileDiv = document.querySelector('.current-file');
        if (currentFileDiv) {
            currentFileDiv.style.display = 'flex';
        }
    }

</script>
@endpush
@endsection
