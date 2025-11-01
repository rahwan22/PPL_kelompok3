@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3>Edit Guru</h3>

    <form action="{{ route('guru.update', $guru->id_guru) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>NIP</label>
            <input type="text" name="nip" class="form-control" value="{{ $guru->nip }}">
        </div>
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" value="{{ $guru->nama }}" required>
        </div>
        <div class="mb-3">
            <label>Jenis Kelamin</label>
            <select name="jenis_kelamin" class="form-select" required>
                <option value="L" {{ $guru->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ $guru->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control">{{ $guru->alamat }}</textarea>
        </div>
        <div class="mb-3">
            <label>No HP</label>
            <input type="text" name="no_hp" class="form-control" value="{{ $guru->no_hp }}">
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="text" name="email" class="form-control" value="{{ $guru->email }}">
        </div>
        
        <div class="mb-3">
            <label>Mata Pelajaran</label>
            <input type="text" name="mapel" class="form-control" value="{{ $guru->mapel }}">
        </div>
        <button class="btn btn-success">Perbarui</button>
        <a href="{{ route('guru.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
