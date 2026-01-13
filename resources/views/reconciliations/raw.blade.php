@extends('layouts.admin')

@section('content')
<div class="p-4">
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Tampilan Raw Excel — {{ $reconciliation->original_filename }}</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('reconciliations.show', $reconciliation->id) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded text-sm">Kembali</a>
            <a href="{{ route('reconciliations.file', $reconciliation->id) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">Unduh File Asli</a>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <div class="mb-3 text-sm text-gray-600">Menampilkan versi <strong>raw</strong> (tidak diubah) dari file Excel. Untuk keterbacaan, tabel pertama di dalam file ditampilkan dengan styling yang rapi.</div>

        @php
            // Ambil tabel pertama dari hasil render PhpSpreadsheet jika ada
            $tableHtml = $rawHtml;
            if (preg_match('/<table[^>]*>.*?<\/table>/is', $rawHtml, $m)) {
                $tableHtml = $m[0];
            }

            // Tambahkan kelas utilitas pada tag <table> pertama untuk memudahkan styling
            $tableHtml = preg_replace('/<table(.*?)>/i', '<table$1 class="w-full text-xs">', $tableHtml, 1);
            // Minimal sanitasi: ganti background yang terlalu gelap dari PhpSpreadsheet
            $tableHtml = str_replace(['background:#ffffff', 'background:#000000'], ['', ''], $tableHtml);
        @endphp

        <style>
            .raw-table { overflow:auto; max-width:100%; }
            .raw-table table { border-collapse: collapse; width:100%; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, 'Roboto Mono', 'Courier New', monospace; }
            .raw-table th, .raw-table td { border: 1px solid #e5e7eb; padding: 6px 8px; vertical-align: top; white-space: nowrap; }
            .raw-table thead th { background: #f8fafc; position: sticky; top: 0; z-index: 20; }
            .raw-table tr:nth-child(even) td { background: #fbfbfc; }
            .raw-table td { max-width: 360px; overflow: hidden; text-overflow: ellipsis; }
            .raw-table .header-cell { font-weight: 600; }
            /* Make horizontal scroll smoother */
            .raw-table::-webkit-scrollbar { height: 10px; }
            .raw-table::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 8px; }
        </style>

        <div class="raw-table border rounded">
            {!! $tableHtml !!}
        </div>
    </div>
</div>
@endsection
