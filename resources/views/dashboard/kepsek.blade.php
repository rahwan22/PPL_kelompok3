@extends('layouts.app')

@section('content')
<p><center>Selamat datang! Anda hanya dapat melihat laporan sekolah.</center></p>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card p-3 text-white bg-success mb-3">
            <h5>Jumlah Guru</h5>
            <p>{{ \App\Models\Guru::count() }}</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-3 text-white bg-success mb-3">
            <h5>Jumlah Siswa</h5>
            <p>{{ \App\Models\Siswa::count() }}</p>
        </div>
    </div>
</div>

<!-- <div class="mt-3">
    <a href="{{ route('laporan.guru') }}" class="btn btn-outline-primary">Laporan Guru</a>
    <a href="{{ route('laporan.siswa') }}" class="btn btn-outline-success">Laporan Siswa</a>
    <a href="{{ route('laporan.nilai') }}" class="btn btn-outline-warning">Laporan Nilai</a>
    <a href="{{ route('laporan.absensi') }}" class="btn btn-outline-info">Laporan Absensi</a>
</div> -->
@endsection
