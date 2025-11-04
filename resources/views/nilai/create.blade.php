@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Tambah Nilai Siswa</h3>
    <form action="{{ route('nilai.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Siswa</label>
            <select name="nis" class="form-control" required>
                <option value="">-- Pilih Siswa --</option>
                @foreach($siswa as $s)
                    <option value="{{ $s->nis }}">{{ $s->nama }} ({{ $s->nis }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Mata Pelajaran</label>
            <select name="id_mapel" class="form-control" required>
                <option value="">-- Pilih Mapel --</option>
                @foreach($mapel as $m)
                    <option value="{{ $m->id_mapel }}">{{ $m->nama_mapel }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Kelas</label>
            <select name="id_kelas" class="form-control" required>
                <option value="">-- Pilih Kelas --</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3"><label>Nilai Tugas</label><input type="number" name="nilai_tugas" class="form-control" required></div>
        <div class="mb-3"><label>Nilai UTS</label><input type="number" name="nilai_uts" class="form-control" required></div>
        <div class="mb-3"><label>Nilai UAS</label><input type="number" name="nilai_uas" class="form-control" required></div>
        <div class="mb-3"><label>Semester</label><input type="text" name="semester" class="form-control" required></div>
        <div class="mb-3"><label>Catatan</label><textarea name="catatan" class="form-control"></textarea></div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('nilai.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
