@extends('laporan.layouts.layout')
@section('title', 'Pratinjau Laporan')
@section('page-title', 'Pratinjau Laporan')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/laporan/custom.css') }}">

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

        <!-- Warning Message (for revalidation) -->
        <!-- Warning Message (for revalidation) -->
        @php $warningMsg = session('warning') ?? ($warning ?? null); @endphp
        @if($warningMsg)
            <div
                style="background: #FEF3C7; border-left: 4px solid #F59E0B; padding: 1rem 1.5rem; margin: 0 1.5rem 1.5rem 1.5rem;">
                <div
                    style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; color: #92400E; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-exclamation-circle"></i>
                    Perhatian
                </div>
                <p style="font-size: 0.8125rem; color: #78350F; margin: 0;">
                    {{ $warningMsg }}
                </p>
            </div>
        @endif

        <!-- Error Messages (top-level) -->
        @php $topList = $displayErrors ?? ($data['errors'] ?? []); @endphp
        @if(!empty($topList))
            <div class="error-alert">
                <div class="error-header">
                    <i class="fas fa-exclamation-triangle"></i>
                    Ditemukan Error Validasi
                </div>
                <ul class="error-list">
                    @foreach($topList as $error)
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
                <strong>Mode Edit Aktif:</strong> Klik pada data di tabel untuk mengedit langsung. Perubahan akan disimpan
                saat Anda menekan tombol "Konfirmasi & Simpan".
            </div>
        </div>

        <!-- Preview Table -->
        <div class="preview-table-container">
            @php
                // Mapping satuan berdasarkan nama header
                $unitMapping = [
                    'Volume' => 'm³',
                    'Jumlah Batang' => 'Batang',
                    'Jumlah Keping' => 'Kpg',
                    'Persediaan Awal' => 'm³',
                    'Penambahan' => 'm³',
                    'Penggunaan/Pengurangan' => 'm³',
                    'Persediaan Akhir' => 'm³',
                ];
            @endphp
            <table class="preview-table" id="previewTable">
                <thead>
                    <tr>
                        <th>
                            <div>Baris</div>
                            <div style="font-size: 0.6rem; font-weight: 500; color: #9CA3AF; margin-top: 2px;">-</div>
                        </th>
                        @foreach($data['headers'] ?? [] as $header)
                            <th>
                                <div>{{ $header }}</div>
                                @if(isset($unitMapping[$header]))
                                    <div style="font-size: 0.6rem; font-weight: 500; color: #059669; margin-top: 2px;">
                                        ({{ $unitMapping[$header] }})</div>
                                @else
                                    <div style="font-size: 0.6rem; font-weight: 500; color: #9CA3AF; margin-top: 2px;">-</div>
                                @endif
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                        $rowsToIterate = $paginatedRows ?? ($data['rows'] ?? []);
                        $rowOffset = isset($paginatedRows) ? (($paginatedRows->currentPage() - 1) * $paginatedRows->perPage()) : 0;
                        $displayCount = isset($paginatedRows) ? $paginatedRows->count() : count($data['rows'] ?? []);
                        $errorsByRow = $errorsByRow ?? [];
                    @endphp

                    @forelse($rowsToIterate as $rowIndex => $row)
                        @php
                            // Support both wrapped rows (['cells'=>..., 'source_row'=>...]) and plain arrays
                            if (is_array($row) && array_key_exists('cells', $row)) {
                                $cells = $row['cells'];
                                $sourceRow = $row['source_row'] ?? ($rowOffset + $rowIndex + 1);
                            } else {
                                $cells = $row;
                                $sourceRow = $rowOffset + $rowIndex + 1;
                            }

                            // Filter: skip row yang kosong atau tidak lengkap
                            $isRowEmpty = true;
                            $headerCount = count($data['headers'] ?? []);
                            foreach ($cells as $cell) {
                                if ($cell !== null && $cell !== '' && trim($cell) !== '') {
                                    $isRowEmpty = false;
                                    break;
                                }
                            }
                            if ($isRowEmpty) {
                                continue;
                            }

                            $rowHasErrors = isset($errorsByRow[$sourceRow]);
                        @endphp
                        <tr data-row-index="{{ $rowOffset + $rowIndex }}" @if($rowHasErrors)
                        style="background:#FEF2F2;border-left:4px solid #FCA5A5;" @endif>
                            <td style="font-weight:700;">{{ $rowIndex + 1 }}</td>
                            @foreach($cells as $cellIndex => $cell)
                                @if($cellIndex < $headerCount)
                                    <td contenteditable="true" data-cell-index="{{ $cellIndex }}">{{ $cell ?? '' }}</td>
                                @endif
                            @endforeach
                        </tr>

                        {{-- keep highlight but do not show inline messages here; top-level list contains messages --}}
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
            @if(($data['total'] ?? 0) > $displayCount)
                <div class="table-info-banner">
                    <i class="fas fa-info-circle"></i>
                    <span>
                        Menampilkan {{ $displayCount }} dari <strong>{{ $data['total'] ?? 0 }}</strong> total baris
                        data untuk pratinjau
                    </span>
                </div>
            @endif

            @if(isset($paginatedRows))
                <div class="px-6 py-4">
                    {{ $paginatedRows->appends(request()->query())->links() }}
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

            <!-- Flag: when set, server will only revalidate preview rows and not save -->
            <input type="hidden" name="revalidate_only" id="revalidateOnlyInput" value="0">

            <!-- Hidden input untuk menyimpan edited data -->
            <input type="hidden" name="edited_data" id="editedDataInput">
            @php
                $plainRows = [];
                foreach ($data['rows'] ?? [] as $r) {
                    if (is_array($r) && array_key_exists('cells', $r)) {
                        $plainRows[] = $r['cells'];
                    } else {
                        $plainRows[] = $r;
                    }
                }
            @endphp

            <div class="action-footer">
                <a href="{{ route('laporan.industri', $metadata['industri_id']) }}" class="btn-action btn-secondary"
                    id="btnKembali">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>


                <button type="submit" class="btn-action btn-primary">
                    <i class="fas fa-save"></i>
                    @if(empty($data['errors']))
                        <span>Konfirmasi & Simpan</span>
                    @else
                        <span>Perbaiki & Validasi Ulang</span>
                    @endif
                </button>
            </div>
        </form>

    </div>

    <script>
        // masterRows: full dataset as array of arrays
        var masterRows = @json($plainRows);

        // Check if this is a fresh upload - if so, clear localStorage first
        var isFreshUpload = @json($isFreshUpload ?? false);
        if (isFreshUpload) {
            try { localStorage.removeItem('preview_master_rows'); } catch (e) { }
        } else {
            // Only restore persisted edits if NOT a fresh upload
            try {
                var stored = localStorage.getItem('preview_master_rows');
                if (stored) {
                    var parsed = JSON.parse(stored);
                    if (Array.isArray(parsed)) masterRows = parsed;
                }
            } catch (e) { }
        }

        // whether the server indicated initial errors on this preview
        var initialHasErrors = @json(!empty($data['errors']));

        function persistMasterRows() {
            try { localStorage.setItem('preview_master_rows', JSON.stringify(masterRows)); } catch (e) { }
        }

        function renderCurrentPage() {
            var table = document.getElementById('previewTable');
            if (!table) return;
            var trs = table.querySelectorAll('tbody tr');
            trs.forEach(function (tr) {
                var cells = tr.querySelectorAll('td[contenteditable]');
                if (!cells || cells.length === 0) return;
                var idx = parseInt(tr.getAttribute('data-row-index'));
                if (!isNaN(idx) && masterRows[idx]) {
                    cells.forEach(function (c, ci) { c.textContent = masterRows[idx][ci] ?? ''; });
                }
            });
        }

        function attachCellListeners() {
            var table = document.getElementById('previewTable');
            if (!table) return;
            table.querySelectorAll('td[contenteditable]').forEach(function (cell) {
                cell.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') { e.preventDefault(); var next = this.nextElementSibling; if (next && next.hasAttribute('contenteditable')) next.focus(); }
                });
                cell.addEventListener('paste', function (e) { e.preventDefault(); var text = (e.clipboardData || window.clipboardData).getData('text/plain'); document.execCommand('insertText', false, text); });
                cell.addEventListener('input', function () {
                    var tr = this.parentElement; var idx = parseInt(tr.getAttribute('data-row-index')); if (isNaN(idx)) return; var cells = tr.querySelectorAll('td[contenteditable]'); masterRows[idx] = masterRows[idx] || []; cells.forEach(function (c, ci) { masterRows[idx][ci] = c.textContent.trim(); }); persistMasterRows();
                });
            });
        }

        // On submit, send full masterRows and set revalidate flag if initial preview had errors
        document.getElementById('previewForm').addEventListener('submit', function (e) {
            // copy visible page edits into masterRows
            var table = document.getElementById('previewTable');
            if (table) {
                var trs = table.querySelectorAll('tbody tr');
                trs.forEach(function (tr) {
                    var cells = tr.querySelectorAll('td[contenteditable]');
                    if (!cells || cells.length === 0) return;
                    var idx = parseInt(tr.getAttribute('data-row-index'));
                    if (isNaN(idx)) return;
                    masterRows[idx] = masterRows[idx] || [];
                    cells.forEach(function (c, ci) { masterRows[idx][ci] = c.textContent.trim(); });
                });
            }

            document.getElementById('editedDataInput').value = JSON.stringify(masterRows);
            document.getElementById('revalidateOnlyInput').value = initialHasErrors ? '1' : '0';
        });

        document.addEventListener('DOMContentLoaded', function () {
            renderCurrentPage();
            attachCellListeners();

            var revalidationOk = @json(session('revalidation_ok', false));
            if (revalidationOk) {
                try { localStorage.removeItem('preview_master_rows'); } catch (e) { }
                // update button text
                var submitBtn = document.querySelector('.action-footer .btn-primary');
                if (submitBtn) submitBtn.innerHTML = '<i class="fas fa-save"></i> <span>Konfirmasi &amp; Simpan</span>';
            }

            var saveOk = @json(session('save_ok', false));
            if (saveOk) {
                try { localStorage.removeItem('preview_master_rows'); } catch (e) { }
            }

            // Clear localStorage when user clicks "Kembali" button
            var btnKembali = document.getElementById('btnKembali');
            if (btnKembali) {
                btnKembali.addEventListener('click', function () {
                    try { localStorage.removeItem('preview_master_rows'); } catch (e) { }
                });
            }
        });
    </script>

@endsection