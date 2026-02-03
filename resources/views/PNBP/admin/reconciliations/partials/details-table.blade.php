{{-- Header & Tabel Detail Rekonsiliasi (AJAX Partial) --}}
<div class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-white dark:bg-surface-dark">
    <h3 class="font-bold text-gray-800 dark:text-white">Detail Data Transaksi ({{ $details->total() }} baris)</h3>
    <div class="flex items-center space-x-2">
        <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded border dark:border-gray-600">
            Menampilkan {{ $details->count() }} data per halaman
        </span>
    </div>
</div>

<div class="p-4">
    {{-- TABEL NETRAL MULAI DARI SINI --}}
    <div class="overflow-x-auto border rounded-lg dark:border-gray-700">
        <table class="min-w-full text-xs text-left text-gray-900 dark:text-gray-200">
            {{-- Header Netral --}}
            <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-800 border-b dark:border-gray-700 text-gray-700 dark:text-gray-300">
                <tr>
                    <th class="px-3 py-3 whitespace-nowrap sticky left-0 bg-gray-100 dark:bg-gray-800 z-10 shadow-sm border-r dark:border-gray-700 min-w-[50px]">No</th>
                    <th class="px-3 py-3 whitespace-nowrap min-w-[200px]">Wilayah</th>

                    <th class="px-3 py-3 whitespace-nowrap min-w-[120px]">LHP No</th>
                    <th class="px-3 py-3 whitespace-nowrap min-w-[100px]">Tgl LHP</th>
                    <th class="px-3 py-3 whitespace-nowrap min-w-[150px]">Jenis HH</th>
                    <th class="px-3 py-3 whitespace-nowrap text-right">Volume</th>
                    <th class="px-3 py-3 whitespace-nowrap text-center w-[60px]">Sat</th>
                    <th class="px-3 py-3 whitespace-nowrap text-right">Nilai LHP</th>

                    <th class="px-3 py-3 whitespace-nowrap border-l dark:border-gray-700 min-w-[120px]">Billing No</th>
                    <th class="px-3 py-3 whitespace-nowrap min-w-[100px]">Tgl Billing</th>
                    <th class="px-3 py-3 whitespace-nowrap text-right">Nilai Billing</th>

                    <th class="px-3 py-3 whitespace-nowrap border-l dark:border-gray-700 min-w-[100px]">Tgl Setor</th>
                    <th class="px-3 py-3 whitespace-nowrap min-w-[120px]">Bank</th>
                    <th class="px-3 py-3 whitespace-nowrap min-w-[120px]">NTPN</th>
                    <th class="px-3 py-3 whitespace-nowrap min-w-[120px]">NTB</th>
                    <th class="px-3 py-3 whitespace-nowrap text-right">Nilai Setor</th>
                </tr>
            </thead>

            {{-- Body Netral --}}
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($details as $detail)
                    <tr class="bg-white dark:bg-surface-dark hover:bg-gray-50 dark:hover:bg-gray-700">
                        {{-- No & Wilayah --}}
                        <td class="px-3 py-2 sticky left-0 bg-white dark:bg-surface-dark font-medium text-center shadow-sm border-r dark:border-gray-700 text-gray-500 dark:text-gray-400">{{ ($details->firstItem() ?? 1) + $loop->index }}</td>
                        <td class="px-3 py-2 font-medium text-gray-900 dark:text-gray-200 whitespace-nowrap">{{ $detail->wilayah }}</td>

                        {{-- Data LHP --}}
                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $detail->lhp_no }}</td>
                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $detail->lhp_tanggal }}</td>
                        <td class="px-3 py-2 font-medium text-gray-800 dark:text-gray-200 whitespace-nowrap">{{ $detail->jenis_sdh }}</td>
                        <td class="px-3 py-2 text-right font-medium text-gray-800 dark:text-gray-200">{{ number_format($detail->volume, 2, '.', ',') }}</td>
                        <td class="px-3 py-2 text-center text-xs text-gray-500 dark:text-gray-400">{{ $detail->satuan }}</td>
                        <td class="px-3 py-2 text-right font-medium text-gray-800 dark:text-gray-200">{{ number_format($detail->lhp_nilai, 0, '.', ',') }}</td>

                        {{-- Data Billing --}}
                        <td class="px-3 py-2 border-l dark:border-gray-700 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $detail->billing_no }}</td>
                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $detail->billing_tanggal }}</td>
                        <td class="px-3 py-2 text-right font-medium text-gray-800 dark:text-gray-200">{{ number_format($detail->billing_nilai ?? 0, 0, '.', ',') }}</td>

                        {{-- Data Setor --}}
                        <td class="px-3 py-2 border-l dark:border-gray-700 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $detail->setor_tanggal }}</td>
                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $detail->setor_bank }}</td>
                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $detail->setor_ntpn }}</td>
                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $detail->setor_ntb }}</td>
                        <td class="px-3 py-2 text-right font-medium text-gray-800 dark:text-gray-200">{{ number_format($detail->setor_nilai ?? 0, 0, '.', ',') }}</td>
                    </tr>
                @empty
                    <tr><td class="p-6 text-center text-gray-500 dark:text-gray-400" colspan="15">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4" data-pagination>
        {{ $details->withQueryString()->links() }}
    </div>
</div>
