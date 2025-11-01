@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0"><i class="fas fa-edit me-2"></i> Edit Data Absensi Siswa: {{ $absensi->siswa->nama ?? 'Siswa' }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('absensi.update', $absensi->id_absensi) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Siswa tidak bisa diubah --}}
                <div class="mb-3">
                    <label for="nis" class="form-label">Siswa</label>
                    <input type="text" class="form-control" value="{{ $absensi->siswa->nama ?? 'Siswa Tidak Ditemukan' }} ({{ $absensi->nis }})" disabled>
                    <input type="hidden" name="nis" value="{{ $absensi->nis }}">
                </div>

                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control" 
                           value="{{ old('tanggal', \Carbon\Carbon::parse($absensi->tanggal)->format('Y-m-d')) }}" required>
                    @error('tanggal') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="jam" class="form-label">Jam</label>
                    <input type="time" name="jam" id="jam" class="form-control" 
                           value="{{ old('jam', $absensi->jam ? \Carbon\Carbon::parse($absensi->jam)->format('H:i') : '') }}">
                    @error('jam') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status Kehadiran</label>
                    <select name="status" id="status" class="form-control" required>
                        {{-- Nilai yang di-match harus huruf kecil --}}
                        @php 
                            $currentStatus = strtolower(old('status', $absensi->status));
                            $statuses = ['hadir' => 'Hadir', 'terlambat' => 'Terlambat', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa']; 
                        @endphp
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ $currentStatus == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                {{-- *** PERUBAHAN: Input Field Lokasi Baru *** --}}
                <div class="mb-3">
                    <label for="lokasi" class="form-label">Lokasi (Opsional)</label>
                    <input type="text" name="lokasi" id="lokasi" class="form-control" maxlength="100" 
                           placeholder="Contoh: Ruang Kelas X-A atau Koordinat GPS" 
                           value="{{ old('lokasi', $absensi->lokasi) }}">
                    <div class="form-text">Lokasi pencatatan absensi. Maksimal 100 karakter.</div>
                    @error('lokasi') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                
                <div class="mb-3">
                    <label for="sumber" class="form-label">Sumber</label>
                    <select name="sumber" id="sumber" class="form-control" required>
                        <option value="scan" {{ old('sumber', $absensi->sumber) == 'scan' ? 'selected' : '' }}>Scan</option>
                        <option value="manual" {{ old('sumber', $absensi->sumber) == 'manual' ? 'selected' : '' }}>Manual</option>
                    </select>
                    @error('sumber') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-end pt-3">
                    <a href="{{ route('absensi.index') }}" class="btn btn-secondary me-2">Kembali</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sync me-1"></i> Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
