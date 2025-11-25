@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Tambah Kelas Baru</h3>

    {{-- Form hanya ditampilkan jika user adalah Admin --}}
    @if (auth()->user()->role === 'admin')
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-person-plus-fill me-2"></i> Formulir Kelas
            </div>
            <div class="card-body">
                {{-- Form akan memanggil route kelas.store --}}
                <form action="{{ route('kelas.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="nama_kelas" class="form-label">Nama Kelas (Contoh: 4A)</label>
                        <input type="text" name="nama_kelas" id="nama_kelas" value="{{ old('nama_kelas') }}"
                               class="form-control @error('nama_kelas') is-invalid @enderror" 
                               placeholder="Masukkan nama kelas" required>
                        @error('nama_kelas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="tahun_ajaran" class="form-label">Tahun Ajaran (Contoh: 2024/2025)</label>
                        <input type="text" name="tahun_ajaran" id="tahun_ajaran" value="{{ old('tahun_ajaran') }}"
                               class="form-control @error('tahun_ajaran') is-invalid @enderror" 
                               placeholder="Masukkan tahun ajaran" required>
                        @error('tahun_ajaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="id_kelas_wali" class="form-label">Wali Kelas (Opsional)</label>
                        <select name="id_kelas_wali" id="id_kelas_wali"
                                class="form-select @error('id_kelas_wali') is-invalid @enderror">
                            <option value="">-- Pilih Wali Kelas --</option>
                            
                            {{-- Loop data guru yang tersedia ($availableWalikelas) --}}
                            @foreach ($availableWalikelas as $guru) 
                                <option value="{{ $guru->id_guru }}"
                                    {{-- Old value check --}}
                                    {{ old('id_kelas_wali') == $guru->id_guru ? 'selected' : '' }}
                                >
                                    {{ $guru->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_kelas_wali')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end pt-3">
                        <a href="{{ route('kelas.index') }}" class="btn btn-secondary me-2">
                            <i class="bi bi-arrow-left"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Kelas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Akses Ditolak!</h4>
            <p>Anda tidak memiliki izin untuk mengakses halaman penambahan data kelas ini. Hanya Administrator yang diizinkan.</p>
            <hr>
            <a href="{{ route('kelas.index') }}" class="btn btn-danger">Kembali ke Daftar Kelas</a>
        </div>
    @endif
</div>
@endsection