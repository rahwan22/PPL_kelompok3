@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3>Edit Data Siswa</h3>

    <form action="{{ route('siswa.update', $siswa->nis) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" value="{{ $siswa->nama }}" required>
        </div>
        <div class="mb-3">
            <label>Jenis Kelamin</label>
            <select name="jenis_kelamin" class="form-select" required>
                <option value="L" {{ $siswa->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ $siswa->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" class="form-control" value="{{ $siswa->tanggal_lahir }}">
        </div>
        <div class="mb-3">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control">{{ $siswa->alamat }}</textarea>
        </div>
        <div class="mb-3">
            <label>Kelas</label>
            <select name="id_kelas" class="form-select" required>
                @foreach($kelas as $k)
                    <option value="{{ $k->id_kelas }}" {{ $siswa->id_kelas == $k->id_kelas ? 'selected' : '' }}>
                        {{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>
        <!-- <div class="mb-3">
            <label>Orang Tua</label>
            <select name="id_orangtua" class="form-select">
                <option value="">-- Pilih --</option>
                @foreach($orangtua as $o)
                    <option value="{{ $o->id_orangtua }}" {{ $siswa->id_orangtua == $o->id_orangtua ? 'selected' : '' }}>
                        {{ $o->nama }}
                    </option>
                @endforeach
            </select>
        </div> -->
        <button class="btn btn-success">Perbarui</button>
        <a href="{{ route('siswa.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
