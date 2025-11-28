@extends('layouts.app')

@section('content')
<div class="container mt-4">
    {{-- Menggunakan $kela (Route Model Binding) untuk menampilkan nama kelas --}}
    <h3 class="mb-4">Edit Data Kelas: <span class="text-primary">{{ $kela->nama_kelas }}</span></h3>

    @if (auth()->user()->role === 'admin')
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <i class="bi bi-pencil-square me-2"></i> Perbarui Data Kelas
            </div>
            <div class="card-body">
                {{-- Form akan memanggil route kelas.update dengan method PUT/PATCH --}}
                <form action="{{ route('kelas.update', $kela->id_kelas) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Wajib untuk operasi UPDATE --}}

                    <div class="mb-3">
                        <label for="nama_kelas" class="form-label">Nama Kelas </label>
                        <input type="text" name="nama_kelas" id="nama_kelas" 
                               {{-- Menggunakan old() atau data saat ini dari $kela --}}
                               value="{{ old('nama_kelas', $kela->nama_kelas) }}"
                               class="form-control @error('nama_kelas') is-invalid @enderror" 
                               placeholder="Masukkan nama kelas" required>
                        @error('nama_kelas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="tahun_ajaran" class="form-label">Tahun Ajaran (Contoh: 2024/2025)</label>
                        <input type="text" name="tahun_ajaran" id="tahun_ajaran" 
                               {{-- Menggunakan old() atau data saat ini dari $kela --}}
                               value="{{ old('tahun_ajaran', $kela->tahun_ajaran) }}"
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
                            
                            {{-- Opsi Default: Memungkinkan untuk menghapus wali kelas --}}
                            <option value="">-- Hapus Wali Kelas --</option>
                            
                            {{-- Loop data guru yang tersedia ($availableWalikelas) --}}
                            @foreach ($availableWalikelas as $guru) 
                                <option value="{{ $guru->id_guru }}"
                                    {{-- Logic untuk membuat opsi 'selected':
                                         1. Cek old('id_kelas_wali') jika ada error validasi.
                                         2. Jika tidak ada error, cek apakah guru ini adalah wali kelas saat ini. --}}
                                    {{ (old('id_kelas_wali') == $guru->id_guru) ? 'selected' : '' }}
                                    {{ (old('id_kelas_wali') === null && $kela->waliKelas && $kela->waliKelas->id_guru == $guru->id_guru) ? 'selected' : '' }}
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
                        <button type="submit" class="btn btn-warning text-dark">
                            <i class="bi bi-arrow-clockwise"></i> Perbarui Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Akses Ditolak!</h4>
            <p>Anda tidak memiliki izin untuk mengakses halaman pengubahan data kelas ini. Hanya Administrator yang diizinkan.</p>
            <hr>
            <a href="{{ route('kelas.index') }}" class="btn btn-danger">Kembali ke Daftar Kelas</a>
        </div>
    @endif
</div>
@endsection