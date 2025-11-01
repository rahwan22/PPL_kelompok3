@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3>Edit Mata Pelajaran</h3>

    <form action="{{ route('mapel.update', $mapel->id_mapel) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Kode Mapel</label>
            <input type="text" name="kode_mapel" class="form-control" value="{{ $mapel->kode_mapel }}" required>
        </div>
        <div class="mb-3">
            <label>Nama Mapel</label>
            <input type="text" name="nama_mapel" class="form-control" value="{{ $mapel->nama_mapel }}" required>
        </div>
        
        <button class="btn btn-success">Update</button>
        <a href="{{ route('mapel.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
