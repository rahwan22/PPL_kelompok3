


@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Tambah Kelas Baru</h3>

    <!-- Pembatasan Akses Form Hanya untuk Admin -->
    @if (Auth::check() && Auth::user()->role === 'admin')
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-person-plus-fill me-2"></i> Formulir Kelas
            </div>
            <div class="card-body">
                <!-- Form akan memanggil route kelas.store untuk menyimpan data -->
                <form action="{{ route('kelas.store') }}" method="POST">
                    @csrf

                    <!-- Input Nama Kelas -->
                    <div class="mb-3">
                        <label for="nama_kelas" class="form-label">Nama Kelas (Contoh: 4A)</label>
                        <input type="text" name="nama_kelas" id="nama_kelas" value="{{ old('nama_kelas') }}"
                               class="form-control @error('nama_kelas') is-invalid @enderror" 
                               placeholder="Masukkan nama kelas" required>
                        @error('nama_kelas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Input Tahun Ajaran -->
                    <div class="mb-3">
                        <label for="tahun_ajaran" class="form-label">Tahun Ajaran (Contoh: 2024/2025)</label>
                        <input type="text" name="tahun_ajaran" id="tahun_ajaran" value="{{ old('tahun_ajaran') }}"
                               class="form-control @error('tahun_ajaran') is-invalid @enderror" 
                               placeholder="Masukkan tahun ajaran" required>
                        @error('tahun_ajaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Select Wali Kelas -->
                    <div class="mb-3">
                        <label for="id_wali_kelas" class="form-label">Wali Kelas</label>
                        <select name="id_wali_kelas" id="id_wali_kelas"
                                class="form-select @error('id_wali_kelas') is-invalid @enderror">
                            <option value="">-- Pilih Wali Kelas (Opsional) --</option>
                            <!-- Loop data guru yang dikirim dari controller -->
                            @foreach ($guru as $g)
                                <option value="{{ $g->id_guru }}" {{ old('id_wali_kelas') == $g->id_guru ? 'selected' : '' }}>
                                    {{ $g->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_wali_kelas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tombol Aksi -->
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
        <!-- Jika bukan admin -->
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Akses Ditolak!</h4>
            <p>Anda tidak memiliki izin untuk mengakses halaman penambahan data kelas ini. Hanya Administrator yang diizinkan.</p>
            <hr>
            <a href="{{ route('kelas.index') }}" class="btn btn-danger">Kembali ke Daftar Kelas</a>
        </div>
    @endif
</div>
@endsection
