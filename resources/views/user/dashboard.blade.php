@extends('layouts.app')

@section('title', 'Upload Rekonsiliasi - SISUDAH')

@section('content')
<div class="w-full max-w-4xl mx-auto space-y-6">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h1 class="text-xl font-semibold text-gray-900">Halo, {{ auth()->user()->name }}.</h1>
            <p class="mt-1 text-sm text-gray-600">Silakan upload Kertas Kerja Rekonsiliasi Anda di bawah ini.</p>

            @if (session('success'))
                <div class="mt-4 rounded-md bg-green-50 p-3 border border-green-200 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-4 rounded-md bg-red-50 p-3 border border-red-200 text-sm text-red-700">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="uploadForm" action="{{ route('reconciliations.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tahun Periode</label>
                        <input type="number" name="year" value="{{ old('year', date('Y')) }}" class="mt-1 w-full border rounded-md px-3 py-2" required />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Triwulan</label>
                        <select name="quarter" class="mt-1 w-full border rounded-md px-3 py-2 bg-white" required>
                            <option value="1" {{ old('quarter') == 1 ? 'selected' : '' }}>Triwulan I (Jan - Mar)</option>
                            <option value="2" {{ old('quarter') == 2 ? 'selected' : '' }}>Triwulan II (Apr - Jun)</option>
                            <option value="3" {{ old('quarter') == 3 ? 'selected' : '' }}>Triwulan III (Jul - Sep)</option>
                            <option value="4" {{ old('quarter') == 4 ? 'selected' : '' }}>Triwulan IV (Okt - Des)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">File Excel (.xlsx, .xls, .csv)</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="mt-1 w-full border rounded-md px-3 py-2 bg-white" required />
                </div>

                <div class="pt-2">
                    <button id="uploadBtn" type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 rounded-md inline-flex items-center justify-center">
                        <svg id="uploadSpinner" class="hidden animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span id="uploadBtnText">Upload</span>
                    </button>
                    <p id="uploadHint" class="hidden mt-2 text-xs text-gray-500">Sedang mengupload… mohon tunggu.</p>
                </div>
            </form>

            <script>
                (function () {
                    const form = document.getElementById('uploadForm');
                    const btn = document.getElementById('uploadBtn');
                    const spinner = document.getElementById('uploadSpinner');
                    const text = document.getElementById('uploadBtnText');
                    const hint = document.getElementById('uploadHint');
                    if (!form || !btn || !spinner || !text || !hint) return;

                    form.addEventListener('submit', function () {
                        btn.disabled = true;
                        btn.classList.add('opacity-70', 'pointer-events-none');
                        spinner.classList.remove('hidden');
                        hint.classList.remove('hidden');
                        text.textContent = 'Mengupload…';
                        form.setAttribute('aria-busy', 'true');
                    });
                })();
            </script>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Riwayat Upload Saya</h2>
                <p class="mt-1 text-sm text-gray-600">Hanya menampilkan file yang Anda upload sendiri.</p>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="rounded-lg border border-gray-200 p-4">
                <div class="text-xs text-gray-500">Total Upload</div>
                <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $totals['total_upload'] ?? 0 }}</div>
            </div>
            <div class="rounded-lg border border-gray-200 p-4">
                <div class="text-xs text-gray-500">Total Baris Data</div>
                <div class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($totals['total_baris'] ?? 0, 0, ',', '.') }}</div>
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

    <form method="POST" action="{{ route('logout') }}" class="flex justify-center">
        @csrf
        <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">Logout</button>
    </form>
</div>
@endsection
