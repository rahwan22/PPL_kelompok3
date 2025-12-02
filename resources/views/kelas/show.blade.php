@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        

        @auth
            @if (Auth::user()->role === 'admin' || Auth::user()->role === 'kepala_sekolah')
                <a href="{{ route('kelas.index') }}" class="btn btn-secondary rounded-pill shadow-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Data Kelas
                </a>
            @else
                {{-- Spacer jika tombol di atas tidak ditampilkan --}}
                <div></div>
            @endif
        @endauth

        {{-- Header Detail --}}
        <h2 class="h3 font-weight-bold text-primary text-center m-0">
            <i class="fas fa-chalkboard-teacher me-2"></i> Detail Kelas
        </h2>
        
        <div class="d-flex">
    
            @auth
                @if (Auth::user()->role === 'admin')
                    <a href="{{ route('kelas.mapel.list', $kelas->id_kelas) }}" class="px-4 py-2 text-sm font-semibold rounded-lg text-gray-600 border border-gray-300 hover:bg-gray-50 transition duration-150 me-2" style="text-decoration: none;">
                        <i class="fas fa-book me-1"></i> Daftar Mata Pelajaran Kelas Ini
                    </a>
                @endif
            @endauth

            {{-- Tombol Mulai Scan QR (Hanya untuk Guru) --}}
            @auth
                @if (Auth::user()->role === 'guru')
          
                    <a href="{{ route('jadwal.guru.saya') }}" class="btn btn-primary rounded-pill shadow-sm">
                        <i class="fas fa-qrcode me-1"></i> Kembali Ke Daftar
                    </a>
                @endif
            @endauth
        
            @guest
                <div></div>
            @endguest
        </div>

    </div>

    {{-- Kartu Detail Kelas --}}
    <div class="card shadow-lg mb-5 border-0 rounded-3">
        <div class="card-header bg-primary text-white py-3 rounded-top-3">
            <h4 class="mb-0">{{ $kelas->nama_kelas }}</h4>
            
        </div>
        
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>ID Kelas:</strong> {{ $kelas->id_kelas }}</p>
                    <p><strong>Tahun Ajaran:</strong> <span class="badge bg-success">{{ $kelas->tahun_ajaran }}</span></p>
                </div>
                <div class="col-md-6">
                    <p>
                        <strong>Wali Kelas:</strong> 
                        <span class="badge bg-info text-dark">
                            {{ $kelas->waliKelas->nama ?? 'Belum Ditentukan' }}
                        </span>
                    </p>
                    <p><strong>Jumlah Siswa:</strong> <span class="badge bg-warning text-dark">{{ $kelas->siswa->count() }} Orang</span></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Siswa --}}
    <div class="card shadow-lg border-0 rounded-3 mb-5">
        <div class="card-header bg-secondary text-white py-3 rounded-top-3">
            <h4 class="mb-0">
                <i class="fas fa-users me-2"></i> Daftar Siswa
            </h4>
        
        </div>
        <div class="card-body p-0">
            @if ($kelas->siswa->count() > 0)
                <ul class="list-group list-group-flush">
                    <li class="list-group-item list-group-item-dark d-flex justify-content-between align-items-center fw-bold">
                        <div style="width: 50%;">Nama Siswa</div>
                        <div style="width: 25%;" class="text-center">NISN</div>
                        <div style="width: 25%;" class="text-center">Jenis Kelamin</div>
                        <!-- <div style="width: 25%;" class="text-center">Aksi</div> -->
                    </li>
                    @foreach ($kelas->siswa->sortBy('nama') as $siswa)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div style="width: 50%;">
                                <i class="fas fa-user-graduate me-2 text-primary"></i>
                                {{ $siswa->nama }} 
                            </div>
                            <div style="width: 25%;" class="text-center text-muted small">{{ $siswa->nis }}</div>
                            <div style="width: 25%;" class="text-center">
                                @if ($siswa->jenis_kelamin === 'L')
                                    <span class="badge bg-primary">Laki-laki</span>
                                @else
                                    <span class="badge bg-pink">Perempuan</span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                    
                </ul>
                
            @else
                <div class="alert alert-warning m-3 text-center">
                    <i class="fas fa-exclamation-triangle me-1"></i> Kelas ini belum memiliki siswa yang terdaftar.
                </div>
            @endif
        </div>
    </div>
    
    {{-- Tombol "Jadwal Mengajar Saya" dipindahkan ke bawah dan dibuat menonjol --}}
    @auth
        @if (Auth::user()->role === 'guru')
            <div class="d-flex justify-content-center mt-4 pb-5">
                <a href="{{ route('absensi.scan') }}" class="btn btn-lg btn-success rounded-pill shadow-lg w-75">
                    <i class="fas fa-calendar-alt me-2"></i> Scan QR Sekarang
                </a>
            </div>
        @endif
    @endauth

</div>

<style>
    /* Styling khusus untuk badge jenis kelamin jika diperlukan */
    .bg-pink {
        background-color: #ff69b4 !important; /* Warna pink cerah */
        color: white !important;
    }
</style>
@endsection