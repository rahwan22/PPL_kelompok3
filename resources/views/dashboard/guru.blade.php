@extends('layouts.app')

@section('content')
<p>Selamat datang,!</p>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card p-3 text-white bg-primary mb-3">
            <h5>Jumlah Siswa</h5>
            <p>{{ \App\Models\Siswa::count() }}</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-3 text-white bg-primary mb-3">
            <h5>Mata Pelajaran</h5>
            <p>{{ \App\Models\MataPelajaran::count() }}</p>
        </div>
    </div>
        <div class="col-md-6">
             <div class="card p-3 text-white bg-primary mb-3">
            <h5>Jumlah Kelas</h5>
            <p>{{ \App\Models\Kelas::count() }}</p>
        </div>
        </div>
</div>



<!-- <div class="mt-3">
    <a href="{{ route('absensi.scan') }}" class="btn btn-outline-success">Scan Absensi Siswa</a>
    <a href="{{ route('nilai.create') }}" class="btn btn-outline-primary">Input Nilai</a>
</div> -->
@endsection
