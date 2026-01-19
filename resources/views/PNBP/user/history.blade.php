@extends('PNBP.layouts.app')

@section('title', 'Riwayat Upload - Pelaporan PNBP')

@section('content')
<div class="w-full max-w-5xl mx-auto">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Riwayat Laporan</h1>
                <p class="mt-1 text-sm text-gray-600">Menampilkan laporan yang pernah diunggah.</p>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="rounded-lg border border-gray-200 p-4">
                <div class="text-xs text-gray-500">Total Laporan</div>
                <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $totals['total_upload'] ?? 0 }}</div>
            </div>
            <div class="rounded-lg border border-gray-200 p-4">
                <div class="text-xs text-gray-500">Total Nilai Setor</div>
                <div class="mt-1 text-2xl font-semibold text-gray-900">Rp {{ number_format($totals['total_setor_nilai'] ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Baris</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total LHP</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Billing</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Setor</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse (($reconciliations ?? []) as $item)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">{{ optional($item->created_at)->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">{{ $item->year }} • TW {{ $item->quarter }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $item->original_filename }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 text-right whitespace-nowrap">{{ number_format($item->details_count ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 text-right whitespace-nowrap">Rp {{ number_format((float) ($item->total_lhp_nilai ?? 0), 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 text-right whitespace-nowrap">Rp {{ number_format((float) ($item->total_billing_nilai ?? 0), 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 text-right whitespace-nowrap">Rp {{ number_format((float) ($item->total_setor_nilai ?? 0), 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada riwayat upload.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
