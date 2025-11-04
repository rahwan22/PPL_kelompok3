@extends('layouts.app')

@section('content')

<div class="container mt-4">
<div class="card shadow-sm">
<div class="card-header bg-warning text-white">
<h3 class="mb-0">Edit Nilai Siswa: {{ $nilai->siswa->nama ?? 'N/A' }}</h3>
</div>
<div class="card-body">
<!-- Form diarahkan ke nilai.update dengan method PUT -->
<form action="{{ route('nilai.update', $nilai->id) }}" method="POST">
@csrf
@method('PUT')

            <!-- Input Siswa (NIS) -->
            <div class="mb-3">
                <label for="nis" class="form-label">Siswa</label>
                <select name="nis" id="nis" class="form-control @error('nis') is-invalid @enderror" required>
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($siswa as $s)
                        <option value="{{ $s->nis }}" {{ old('nis', $nilai->nis) == $s->nis ? 'selected' : '' }}>
                            {{ $s->nama }} ({{ $s->nis }})
                        </option>
                    @endforeach
                </select>
                @error('nis') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Input Mata Pelajaran -->
            <div class="mb-3">
                <label for="id_mapel" class="form-label">Mata Pelajaran</label>
                <select name="id_mapel" id="id_mapel" class="form-control @error('id_mapel') is-invalid @enderror" required>
                    <option value="">-- Pilih Mapel --</option>
                    @foreach($mapel as $m)
                        <option value="{{ $m->id_mapel }}" {{ old('id_mapel', $nilai->id_mapel) == $m->id_mapel ? 'selected' : '' }}>
                            {{ $m->nama_mapel }}
                        </option>
                    @endforeach
                </select>
                @error('id_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Input Kelas -->
            <div class="mb-3">
                <label for="id_kelas" class="form-label">Kelas</label>
                <select name="id_kelas" id="id_kelas" class="form-control @error('id_kelas') is-invalid @enderror" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ old('id_kelas', $nilai->id_kelas) == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
                @error('id_kelas') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Nilai Tugas -->
            <div class="mb-3">
                <label for="nilai_tugas" class="form-label">Nilai Tugas</label>
                <input type="number" name="nilai_tugas" id="nilai_tugas" 
                       class="form-control @error('nilai_tugas') is-invalid @enderror" 
                       value="{{ old('nilai_tugas', $nilai->nilai_tugas) }}" min="0" max="100" required>
                @error('nilai_tugas') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Nilai UTS -->
            <div class="mb-3">
                <label for="nilai_uts" class="form-label">Nilai UTS</label>
                <input type="number" name="nilai_uts" id="nilai_uts" 
                       class="form-control @error('nilai_uts') is-invalid @enderror" 
                       value="{{ old('nilai_uts', $nilai->nilai_uts) }}" min="0" max="100" required>
                @error('nilai_uts') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Nilai UAS -->
            <div class="mb-3">
                <label for="nilai_uas" class="form-label">Nilai UAS</label>
                <input type="number" name="nilai_uas" id="nilai_uas" 
                       class="form-control @error('nilai_uas') is-invalid @enderror" 
                       value="{{ old('nilai_uas', $nilai->nilai_uas) }}" min="0" max="100" required>
                @error('nilai_uas') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Semester -->
            <div class="mb-3">
                <label for="semester" class="form-label">Semester</label>
                <input type="text" name="semester" id="semester" 
                       class="form-control @error('semester') is-invalid @enderror" 
                       value="{{ old('semester', $nilai->semester) }}" required>
                @error('semester') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Catatan -->
            <div class="mb-3">
                <label for="catatan" class="form-label">Catatan</label>
                <textarea name="catatan" id="catatan" class="form-control @error('catatan') is-invalid @enderror">
                    {{ old('catatan', $nilai->catatan) }}
                </textarea>
                @error('catatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="d-flex justify-content-end pt-3">
                <a href="{{ route('nilai.index') }}" class="btn btn-secondary me-2">
                    <i class="bi bi-arrow-left"></i> Batal
                </a>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-save"></i> Update Nilai
                </button>
            </div>
        </form>
    </div>
</div>


</div>
@endsection