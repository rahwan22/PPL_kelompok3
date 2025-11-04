@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Tambah Guru</h3>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('guru.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input id="nama" name="nama" class="form-control" value="{{ old('nama') }}" required>
            @error('nama') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="nip" class="form-label">NIP </label>
            <input id="nip" name="nip" class="form-control" value="{{ old('nip') }}">
            @error('nip') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email (wajib, akan dipakai untuk login)</label>
            <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required>
            @error('email') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
            <select id="jenis_kelamin" name="jenis_kelamin" class="form-select" required>
                <option value="">-- Pilih --</option>
                <option value="L" {{ old('jenis_kelamin')=='L'?'selected':'' }}>L</option>
                <option value="P" {{ old('jenis_kelamin')=='P'?'selected':'' }}>P</option>
            </select>
            @error('jenis_kelamin') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat </label>
            <input id="alamat" name="alamat" class="form-control" value="{{ old('alamat') }}">
            @error('alamat') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label for="no_hp" class="form-label">No Hp</label>
            <input id="no_hp" name="no_hp" class="form-control" value="{{ old('no_hp') }}">
            @error('no_hp') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="mapel" class="form-label">Mapel </label>
            <input id="mapel" name="mapel" class="form-control" value="{{ old('mapel') }}">
            @error('mapel') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex justify-content-end">
            <a href="{{ route('guru.index') }}" class="btn btn-secondary me-2">Batal</a>
            <button class="btn btn-success">Simpan (akun dibuat dengan password default "password123")</button>
        </div>
    </form>
</div>
@endsection
