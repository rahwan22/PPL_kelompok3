@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Nilai Siswa: {{ $nilai->siswa->nama ?? 'N/A' }}</h3>

    <!-- Menggunakan id_nilai untuk rute update -->
    <form action="{{ route('nilai.update', $nilai->id_nilai) }}" method="POST">
        @csrf
        @method('PUT') <!-- Menggunakan metode PUT/PATCH untuk update -->

        <div class="mb-3">
            <label>Siswa</label>
            <select name="nis" class="form-control" required>
                <option value="">-- Pilih Siswa --</option>
                @foreach($siswa as $s)
                    <option value="{{ $s->nis }}" {{ $nilai->nis == $s->nis ? 'selected' : '' }}>
                        {{ $s->nama }} ({{ $s->nis }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Mata Pelajaran</label>
            <select name="id_mapel" class="form-control" required>
                <option value="">-- Pilih Mapel --</option>
                @foreach($mapel as $m)
                    <option value="{{ $m->id_mapel }}" {{ $nilai->id_mapel == $m->id_mapel ? 'selected' : '' }}>
                        {{ $m->nama_mapel }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="mb-3">
            <label>Kelas</label>
            <select name="id_kelas" class="form-control" required>
                <option value="">-- Pilih Kelas --</option>
                @foreach($kelas as $k)
                    <!-- Menggunakan $k->id_kelas dan $nilai->id_kelas untuk seleksi -->
                    <option value="{{ $k->id_kelas }}" {{ $nilai->id_kelas == $k->id_kelas ? 'selected' : '' }}>
                        {{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Nilai Tugas</label>
            <input type="number" name="nilai_tugas" class="form-control" value="{{ old('nilai_tugas', $nilai->nilai_tugas) }}" required>
        </div>
        <div class="mb-3">
            <label>Nilai UTS</label>
            <input type="number" name="nilai_uts" class="form-control" value="{{ old('nilai_uts', $nilai->nilai_uts) }}" required>
        </div>
        <div class="mb-3">
            <label>Nilai UAS</label>
            <input type="number" name="nilai_uas" class="form-control" value="{{ old('nilai_uas', $nilai->nilai_uas) }}" required>
        </div>
        <div class="mb-3">
            <label>Semester</label>
            <input type="text" name="semester" class="form-control" value="{{ old('semester', $nilai->semester) }}" required>
        </div>
        <div class="mb-3">
            <label>Catatan</label>
            <textarea name="catatan" class="form-control">{{ old('catatan', $nilai->catatan) }}</textarea>
        </div>

        <button type="submit" class="btn btn-warning">Perbarui Nilai</button>
        <a href="{{ route('nilai.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection