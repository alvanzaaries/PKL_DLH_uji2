@extends('laporan/layouts.layout')

@section('title', 'Download Template Laporan')

@section('page-title', 'Download Template Laporan')

@section('content')

<div class="content-card" style="padding:1.25rem;">
    <h2 style="margin:0 0 0.5rem 0;">Download Template Laporan</h2>
    <p style="margin:0 0 1rem 0; color:#6B7280;">Pilih template yang ingin diunduh. File disediakan pada folder <code>public/template/laporan</code>.</p>

    @if(count($files) > 0)
    <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:0.5rem;">
        @foreach($files as $file)
        <li>
            <a class="btn btn-export" href="{{ asset('template/laporan/'.$file) }}" download>
                <i class="fas fa-file-download"></i>
                <span style="margin-left:8px;">{{ $file }}</span>
            </a>
        </li>
        @endforeach
    </ul>
    @else
    <div style="padding:1rem; background:#F9FAFB; border:1px solid #E5E7EB; border-radius:4px;">
        Tidak ada template tersedia.
    </div>
    @endif

    <div style="margin-top:1rem;">
        <a href="{{ route('laporan.index') }}" class="btn btn-secondary">Kembali ke Monitoring</a>
    </div>
</div>

@endsection
