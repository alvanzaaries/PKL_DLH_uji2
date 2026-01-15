@extends('laporan.layouts.dashboard')
@section('title', 'Pratinjau Laporan')
@section('page-title', 'Pratinjau Laporan')

@section('content')

<style>
    /* Preview Page Styling - Matching Dashboard */
    .preview-container {
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 4px;
        box-shadow: none;
    }

    .preview-header {
        padding: 1.5rem;
        background-color: white;
        border-bottom: 2px solid #F3F4F6;
    }

    .preview-title {
        font-family: 'Inter', sans-serif;
        font-weight: 700;
        font-size: 1.125rem;
        color: #111827;
        letter-spacing: -0.025em;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .preview-title-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        background: #F3F4F6;
        color: #0F2F24;
        border-radius: 4px;
        font-size: 0.875rem;
    }

    .preview-subtitle {
        font-size: 0.8rem;
        color: #6B7280;
        margin-top: 4px;
        margin-left: 40px;
    }

    .metadata-section {
        background: #F9FAFB;
        border-bottom: 1px solid #E5E7EB;
        padding: 1.5rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .metadata-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .metadata-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: #6B7280;
    }

    .metadata-value {
        font-size: 0.875rem;
        font-weight: 600;
        color: #111827;
    }

    .metadata-value.success {
        color: #059669;
    }

    .metadata-value.error {
        color: #DC2626;
    }

    .metadata-icon {
        margin-right: 6px;
    }

    .error-alert {
        background: #FEF2F2;
        border-left: 4px solid #DC2626;
        padding: 1rem 1.5rem;
        margin: 0 1.5rem 1.5rem 1.5rem;
    }

    .error-header {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: #991B1B;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .error-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .error-item {
        font-size: 0.8125rem;
        color: #7F1D1D;
        padding: 0.5rem 0;
        border-bottom: 1px solid #FEE2E2;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .error-item:last-child {
        border-bottom: none;
    }

    .error-bullet {
        color: #DC2626;
        font-size: 0.625rem;
        margin-top: 4px;
    }

    .preview-table-container {
        overflow-x: auto;
        border-top: 1px solid #E5E7EB;
    }

    .preview-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8125rem;
    }

    .preview-table thead {
        background: #F3F4F6;
    }

    .preview-table th {
        text-align: left;
        padding: 0.875rem 1rem;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 700;
        color: #6B7280;
        white-space: nowrap;
        border-bottom: 2px solid #E5E7EB;
    }

    .preview-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #E5E7EB;
        color: #374151;
        vertical-align: middle;
    }

    .preview-table td[contenteditable="true"] {
        cursor: text;
        position: relative;
        transition: background 0.15s;
    }

    .preview-table td[contenteditable="true"]:hover {
        background: #FFFBEB !important;
        outline: 1px solid #FCD34D;
    }

    .preview-table td[contenteditable="true"]:focus {
        background: #FEF3C7 !important;
        outline: 2px solid #D4AF37;
        outline-offset: -2px;
    }

    .preview-table tbody tr:hover {
        background: #F9FAFB;
    }

    .edit-indicator {
        position: absolute;
        top: 4px;
        right: 4px;
        font-size: 0.625rem;
        color: #D4AF37;
        opacity: 0;
        transition: opacity 0.15s;
    }

    .preview-table td[contenteditable="true"]:hover .edit-indicator {
        opacity: 1;
    }

    .edit-mode-notice {
        background: #FFFBEB;
        border: 1px solid #FCD34D;
        border-radius: 4px;
        padding: 1rem 1.5rem;
        margin: 1.5rem;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 0.8125rem;
        color: #92400E;
    }

    .edit-mode-icon {
        color: #D4AF37;
        font-size: 1.125rem;
    }

    .preview-table tbody tr:last-child td {
        border-bottom: none;
    }

    .table-info-banner {
        background: #EFF6FF;
        border-top: 1px solid #DBEAFE;
        padding: 0.875rem 1.5rem;
        font-size: 0.8125rem;
        color: #1E40AF;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .empty-table-state {
        padding: 4rem 1rem;
        text-align: center;
        background: #F9FAFB;
        color: #6B7280;
    }

    .action-footer {
        padding: 1.5rem;
        background: white;
        border-top: 1px solid #E5E7EB;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .btn-action {
        height: 38px;
        padding: 0 1.25rem;
        border-radius: 4px;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid transparent;
        text-decoration: none;
    }

    .btn-secondary {
        background: white;
        border-color: #D1D5DB;
        color: #374151;
    }

    .btn-secondary:hover {
        background: #F3F4F6;
        border-color: #9CA3AF;
    }

    .btn-primary {
        background: #0F2F24;
        color: white;
    }

    .btn-primary:hover {
        background: #183F32;
    }

    .btn-disabled {
        background: #E5E7EB;
        color: #9CA3AF;
        cursor: not-allowed;
    }

    @media (max-width: 768px) {
        .metadata-section {
            grid-template-columns: 1fr;
        }
        .action-footer {
            flex-direction: column;
        }
        .btn-action {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="preview-container">
    <!-- Header -->
    <div class="preview-header">
        <h2 class="preview-title">
            <span class="preview-title-icon">
                <i class="fas fa-eye"></i>
            </span>
            PRATINJAU DATA LAPORAN
        </h2>
        <p class="preview-subtitle">
            Verifikasi kebenaran data sebelum disimpan ke database
        </p>
    </div>

    <!-- Metadata Info -->
    <div class="metadata-section">
        <div class="metadata-item">
            <div class="metadata-label">Jenis Laporan</div>
            <div class="metadata-value">{{ $metadata['jenis_laporan'] }}</div>
        </div>
        <div class="metadata-item">
            <div class="metadata-label">Periode</div>
            <div class="metadata-value">
                {{ ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][$metadata['bulan']] }} 
                {{ $metadata['tahun'] }}
            </div>
        </div>
        <div class="metadata-item">
            <div class="metadata-label">Total Data</div>
            <div class="metadata-value success">{{ $data['total'] ?? 0 }} Baris</div>
        </div>
        <div class="metadata-item">
            <div class="metadata-label">Status Validasi</div>
            @if(empty($data['errors']))
                <div class="metadata-value success">
                    <i class="fas fa-check-circle metadata-icon"></i>Semua Valid
                </div>
            @else
                <div class="metadata-value error">
                    <i class="fas fa-exclamation-circle metadata-icon"></i>{{ count($data['errors']) }} Error
                </div>
            @endif
        </div>
    </div>

    <!-- Error Messages -->
    @if(!empty($data['errors']))
        <div class="error-alert">
            <div class="error-header">
                <i class="fas fa-exclamation-triangle"></i>
                Ditemukan Error Validasi
            </div>
            <ul class="error-list">
                @foreach($data['errors'] as $error)
                    <li class="error-item">
                        <span class="error-bullet">●</span>
                        <span>{{ $error }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Edit Mode Notice -->
    <div class="edit-mode-notice">
        <i class="fas fa-edit edit-mode-icon"></i>
        <div>
            <strong>Mode Edit Aktif:</strong> Klik pada data di tabel untuk mengedit langsung. Perubahan akan disimpan saat Anda menekan tombol "Konfirmasi & Simpan".
        </div>
    </div>

    <!-- Preview Table -->
    <div class="preview-table-container">
        <table class="preview-table" id="previewTable">
            <thead>
                <tr>
                    @foreach($data['headers'] ?? [] as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($data['rows'] ?? [] as $rowIndex => $row)
                    @php
                        // Filter: skip row yang kosong atau tidak lengkap
                        $isRowEmpty = true;
                        $headerCount = count($data['headers'] ?? []);
                        
                        foreach($row as $cell) {
                            if ($cell !== null && $cell !== '' && trim($cell) !== '') {
                                $isRowEmpty = false;
                                break;
                            }
                        }
                        
                        // Skip row jika semuanya kosong
                        if ($isRowEmpty) {
                            continue;
                        }
                    @endphp
                    <tr data-row-index="{{ $rowIndex }}">
                        @foreach($row as $cellIndex => $cell)
                            @if($cellIndex < $headerCount)
                                <td contenteditable="true" data-cell-index="{{ $cellIndex }}">{{ $cell ?? '' }}</td>
                            @endif
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="100">
                            <div class="empty-table-state">
                                <div style="color: #D1D5DB; margin-bottom: 1rem;">
                                    <i class="far fa-folder-open fa-3x"></i>
                                </div>
                                <h3 style="font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Tidak Ada Data</h3>
                                <p style="font-size: 0.875rem;">Data preview tidak tersedia atau file kosong</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if(($data['total'] ?? 0) > count($data['rows'] ?? []))
            <div class="table-info-banner">
                <i class="fas fa-info-circle"></i>
                <span>
                    Menampilkan {{ count($data['rows'] ?? []) }} dari <strong>{{ $data['total'] ?? 0 }}</strong> total baris data untuk pratinjau
                </span>
            </div>
        @endif
    </div>

    <!-- Action Buttons -->
    <form action="{{ route('laporan.store') }}" method="POST" id="previewForm">
        @csrf
        
        <!-- Hidden inputs untuk metadata -->
        <input type="hidden" name="industri_id" value="{{ $metadata['industri_id'] }}">
        <input type="hidden" name="bulan" value="{{ $metadata['bulan'] }}">
        <input type="hidden" name="tahun" value="{{ $metadata['tahun'] }}">
        <input type="hidden" name="jenis_laporan" value="{{ $metadata['jenis_laporan'] }}">
        <input type="hidden" name="confirmed_preview" value="1">
        
        <!-- Hidden input untuk menyimpan edited data -->
        <input type="hidden" name="edited_data" id="editedDataInput">

        <div class="action-footer">
            <a href="{{ route('industri.laporan', $metadata['industri_id']) }}" class="btn-action btn-secondary">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
            
            @if(empty($data['errors']))
                <button type="submit" class="btn-action btn-primary">
                    <i class="fas fa-save"></i>
                    <span>Konfirmasi & Simpan</span>
                </button>
            @else
                <button type="button" disabled class="btn-action btn-disabled">
                    <i class="fas fa-ban"></i>
                    <span>Perbaiki Error Dahulu</span>
                </button>
            @endif
        </div>
    </form>

</div>

<script>
    // Collect edited data before form submission
    document.getElementById('previewForm').addEventListener('submit', function(e) {
        const table = document.getElementById('previewTable');
        const rows = table.querySelectorAll('tbody tr');
        const editedData = [];
        
        rows.forEach(row => {
            const rowData = [];
            const cells = row.querySelectorAll('td[contenteditable]');
            
            cells.forEach(cell => {
                rowData.push(cell.textContent.trim());
            });
            
            if (rowData.length > 0) {
                editedData.push(rowData);
            }
        });
        
        // Save edited data to hidden input as JSON
        document.getElementById('editedDataInput').value = JSON.stringify(editedData);
    });

    // Prevent line breaks in contenteditable cells
    document.querySelectorAll('td[contenteditable]').forEach(cell => {
        cell.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                // Move to next cell
                const nextCell = this.nextElementSibling;
                if (nextCell && nextCell.hasAttribute('contenteditable')) {
                    nextCell.focus();
                } else {
                    const nextRow = this.parentElement.nextElementSibling;
                    if (nextRow) {
                        const firstCell = nextRow.querySelector('td[contenteditable]');
                        if (firstCell) firstCell.focus();
                    }
                }
            }
        });
        
        // Prevent paste with formatting
        cell.addEventListener('paste', function(e) {
            e.preventDefault();
            const text = (e.clipboardData || window.clipboardData).getData('text/plain');
            document.execCommand('insertText', false, text);
        });
    });
</script>

@endsection
