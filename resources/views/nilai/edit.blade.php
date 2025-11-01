@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Nilai Siswa</h3>
    <form action="{{ route('nilai.update', $nilai->id_nilai) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Siswa</label>
            <input type="text" value="{{ $nilai->siswa->nama }}" class="form-control" disabled>
        </div>

        <div class="mb-3">
            <label>Mata Pelajaran</label>
            <input type="text" value="{{ $nilai->mapel->nama_mapel }}" class="form-control" disabled>
        </div>
        <div class="mb-3">
            <label>Kelas</label>
            <input type="text" value="{{ $nilai->nilai->id_kelas }}" class="form-control" disabled>
        </div>


        <div class="mb-3"><label>Nilai Tugas</label><input type="number" name="nilai_tugas" value="{{ $nilai->nilai_tugas }}" class="form-control" required></div>
        <div class="mb-3"><label>Nilai UTS</label><input type="number" name="nilai_uts" value="{{ $nilai->nilai_uts }}" class="form-control" required></div>
        <div class="mb-3"><label>Nilai UAS</label><input type="number" name="nilai_uas" value="{{ $nilai->nilai_uas }}" class="form-control" required></div>
        <div class="mb-3"><label>Catatan</label><textarea name="catatan" class="form-control">{{ $nilai->catatan }}</textarea></div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('nilai.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
