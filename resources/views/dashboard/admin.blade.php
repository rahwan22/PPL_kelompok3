@extends('layouts.app')

@section('content')
<p><center>Selamat datang, Anda memiliki akses penuh ke semua data</center></p>

<div class="row mt-4">
    <div class="col-md-4">
        <div class="card p-3 text-white bg-success mb-3">
            <h5>Jumlah Guru</h5>
            <p>{{ \App\Models\Guru::count() }}</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 text-white bg-success mb-3">
            <h5>Jumlah Siswa</h5>
            <p>{{ \App\Models\Siswa::count() }}</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 text-white bg-success mb-3">
            <h5>Jumlah Kelas</h5>
            <p>{{ \App\Models\Kelas::count() }}</p>
        </div>
    </div>
</div>
@endsection
