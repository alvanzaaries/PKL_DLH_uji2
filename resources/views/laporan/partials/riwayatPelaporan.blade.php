<div class="bg-white border border-gray-200 shadow-sm rounded-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-base font-bold text-gray-800">
            <i class="fas fa-history text-gray-500 mr-2"></i>
            Riwayat Pelaporan
        </h3>
    </div>

    @if ($laporans->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama
                            Perusahaan</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Jenis
                            Dokumen</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Periode
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tgl Upload
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($laporans as $laporan)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ($laporans->currentPage() - 1) * $laporans->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                {{ $laporan->industri->nama ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                {{ $laporan->jenis_laporan }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ \Carbon\Carbon::parse($laporan->tanggal)->translatedFormat('F Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $laporan->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center gap-2">
                                    @php
                                        // Map human-readable jenis_laporan to route 'jenis' slugs used by detail view
                                        $jenisSlug = null;
                                        switch ($laporan->jenis_laporan) {
                                            case 'Laporan Penerimaan Kayu Bulat':
                                                $jenisSlug = 'penerimaan_kayu_bulat';
                                                break;
                                            case 'Laporan Mutasi Kayu Bulat (LMKB)':
                                                $jenisSlug = 'mutasi_kayu_bulat';
                                                break;
                                            case 'Laporan Penerimaan Kayu Olahan':
                                                $jenisSlug = 'penerimaan_kayu_olahan';
                                                break;
                                            case 'Laporan Mutasi Kayu Olahan (LMKO)':
                                                $jenisSlug = 'mutasi_kayu_olahan';
                                                break;
                                            case 'Laporan Penjualan Kayu Olahan':
                                                $jenisSlug = 'penjualan_kayu_olahan';
                                                break;
                                        }
                                        $bulan = \Carbon\Carbon::parse($laporan->tanggal)->month;
                                        $tahun = \Carbon\Carbon::parse($laporan->tanggal)->year;
                                    @endphp

                                    @if($jenisSlug)
                                        <a href="{{ route('laporan.detail', ['industri' => $laporan->industri_id, 'id' => $laporan->id]) }}"
                                            class="text-[#1B5E20] hover:text-[#2E7D32] font-medium" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif

                                    <button type="button"
                                        onclick="confirmDelete('{{ $laporan->id }}', '{{ $laporan->industri_id }}', '{{ $laporan->jenis_laporan }}', '{{ \Carbon\Carbon::parse($laporan->tanggal)->translatedFormat('F Y') }}')"
                                        class="text-red-600 hover:text-red-800 font-medium" title="Hapus Laporan">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $laporans->links() }}
        </div>
    @else
        <div class="p-10 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3 text-gray-400">
                <i class="fas fa-folder-open text-xl"></i>
            </div>
            <h3 class="text-sm font-bold text-gray-900">Data Tidak Ditemukan</h3>
            <p class="text-xs text-gray-500 mt-1">Perusahaan ini belum memiliki riwayat pelaporan dokumen.</p>
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Konfirmasi Hapus</h3>
            <p class="text-sm text-gray-600 text-center mb-4">
                Apakah Anda yakin ingin menghapus laporan:<br>
                <strong id="deleteInfo" class="text-gray-900"></strong>
            </p>
            <p class="text-xs text-red-600 text-center mb-6">
                <i class="fas fa-warning mr-1"></i>
                Tindakan ini tidak dapat dibatalkan. Semua data terkait laporan ini akan dihapus permanen.
            </p>
            <div class="flex gap-3 justify-center">
                <button type="button" onclick="closeDeleteModal()" 
                        class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                    Batal
                </button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash-alt mr-1"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(laporanId, industriId, jenisLaporan, periode) {
        const modal = document.getElementById('deleteModal');
        const deleteInfo = document.getElementById('deleteInfo');
        const deleteForm = document.getElementById('deleteForm');
        
        deleteInfo.textContent = jenisLaporan + ' - ' + periode;
        deleteForm.action = '/laporan/' + industriId + '/delete/' + laporanId;
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    
    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    
    // Close modal when clicking outside
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
</script>