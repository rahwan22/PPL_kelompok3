@extends('layouts.app')

@section('content')
<style>
    .kelas-card {
        border-radius: 12px;
        transition: 0.25s ease;
    }
    .kelas-card:hover {
        background: #f1f3f5;
        transform: translateX(6px);
    }
    .kelas-badge {
        background: linear-gradient(135deg, #4e54c8, #8f94fb);
        color: white;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
    }
    .icon-kelas {
        font-size: 22px;
        color: #4e54c8;
        margin-right: 10px;
    }
</style>

<div class="container mt-4">

    <h3 class="text-center mb-2 fw-bold">📘 Laporan Absensi Siswa SD</h3>
    <p class="text-center text-muted mb-4">Silakan pilih kelas untuk melihat daftar siswa</p>

    <div class="row justify-content-center">
        <div class="col-md-7">

            <div class="list-group shadow-sm">

                {{-- KELAS 1 --}}
                <a href="{{ route('laporan.guru.kelas1') }}" 
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center kelas-card">
                   
                    <div class="d-flex align-items-center">
                        <i class="bi bi-people-fill icon-kelas"></i>
                        <strong>Kelas 1</strong>
                    </div>

                    <span class="kelas-badge">32 Siswa</span>
                </a>

                {{-- KELAS 2 --}}
                <a href="/kepsek/laporanGuru/kelas/2" 
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center kelas-card">
                   
                    <div class="d-flex align-items-center">
                        <i class="bi bi-people-fill icon-kelas"></i>
                        <strong>Kelas 2</strong>
                    </div>

                    <span class="kelas-badge">30 Siswa</span>
                </a>

                {{-- KELAS 3 --}}
                <a href="/kepsek/laporanGuru/kelas/3" 
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center kelas-card">
                   
                    <div class="d-flex align-items-center">
                        <i class="bi bi-people-fill icon-kelas"></i>
                        <strong>Kelas 3</strong>
                    </div>

                    <span class="kelas-badge">29 Siswa</span>
                </a>

                {{-- KELAS 4 --}}
                <a href="/kepsek/laporanGuru/kelas/4" 
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center kelas-card">
                   
                    <div class="d-flex align-items-center">
                        <i class="bi bi-people-fill icon-kelas"></i>
                        <strong>Kelas 4</strong>
                    </div>

                    <span class="kelas-badge">34 Siswa</span>
                </a>

                {{-- KELAS 5 --}}
                <a href="/kepsek/laporanGuru/kelas/5" 
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center kelas-card">
                   
                    <div class="d-flex align-items-center">
                        <i class="bi bi-people-fill icon-kelas"></i>
                        <strong>Kelas 5</strong>
                    </div>

                    <span class="kelas-badge">31 Siswa</span>
                </a>

                {{-- KELAS 6 --}}
                <a href="/kepsek/laporanGuru/kelas/6" 
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center kelas-card">
                   
                    <div class="d-flex align-items-center">
                        <i class="bi bi-people-fill icon-kelas"></i>
                        <strong>Kelas 6</strong>
                    </div>

                    <span class="kelas-badge">33 Siswa</span>
                </a>

            </div>
        </div>
    </div>

</div>
@endsection
