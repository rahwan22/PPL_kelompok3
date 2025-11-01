@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-success text-white">
            <h3 class="mb-0"><i class="fas fa-user-plus me-2"></i> Tambah Data Absensi Manual</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('absensi.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="nis" class="form-label">Siswa</label>
                    <select name="nis" id="nis" class="form-control" required>
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($siswa as $s)
                            {{-- Gunakan old('nis') untuk mempertahankan nilai yang dipilih jika validasi gagal --}}
                            <option value="{{ $s->nis }}" {{ old('nis') == $s->nis ? 'selected' : '' }}>
                                {{ $s->nama }} ({{ $s->nis }})
                            </option>
                        @endforeach
                    </select>
                    @error('nis') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    {{-- Default value disetel ke tanggal hari ini --}}
                    <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ old('tanggal', \Carbon\Carbon::now()->format('Y-m-d')) }}" required>
                    @error('tanggal') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="jam" class="form-label">Jam (Opsional)</label>
                    {{-- Default value disetel ke jam sekarang --}}
                    <input type="time" name="jam" id="jam" class="form-control" value="{{ old('jam', \Carbon\Carbon::now()->format('H:i')) }}">
                    @error('jam') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status Kehadiran</label>
                    <select name="status" id="status" class="form-control" required>
                        {{-- Menggunakan huruf kapital di sini untuk tampilan, controller akan mengkonversinya menjadi huruf kecil --}}
                        @php $statuses = ['hadir' => 'Hadir', 'terlambat' => 'Terlambat', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa']; @endphp
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ old('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                
                {{-- *** PERUBAHAN: Input Field Lokasi Baru *** --}}
                <div class="mb-3">
                    <label for="lokasi" class="form-label">Lokasi (Opsional)</label>
                    <input type="text" name="lokasi" id="lokasi" class="form-control" maxlength="100" placeholder="Contoh: Ruang Kelas X-A atau Koordinat GPS" value="{{ old('lokasi') }}">
                    <div class="form-text">Mencatat lokasi di mana absensi dicatat. Maksimal 100 karakter.</div>
                    @error('lokasi') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                {{-- Sumber harus diset 'manual' untuk form ini --}}
                <input type="hidden" name="sumber" value="manual">

                <div class="d-flex justify-content-end pt-3">
                    <a href="{{ route('absensi.index') }}" class="btn btn-secondary me-2">Kembali</a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Simpan Absensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
