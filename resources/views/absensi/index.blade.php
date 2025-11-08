@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="h3 font-weight-bold text-success mb-4">
        <i class="fas fa-clipboard-check me-2"></i> Daftar Rekap Absensi Siswa
    </h2>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Filter dan Tombol Tambah --}}
    <div class="d-flex justify-content-between mb-3">
        {{-- Tombol Tambah Manual kini muncul jika role adalah 'admin' ATAU 'guru' --}}
        @if (auth()->user()->role == 'admin' || auth()->user()->role == 'guru')
            <a href="{{ route('absensi.create') }}" class="btn btn-success rounded-pill shadow-sm">
                <i class="fas fa-plus me-1"></i> Tambah Absensi Manual
            </a>
            <a href="{{ route('absensi.scan') }}" class="btn btn-primary rounded-pill shadow-sm">
                <i class="fas fa-qrcode me-1"></i> Mulai Scan QR
            </a>
        @endif
        {{-- Area Filter (Dapat ditambahkan logika filter di sini) --}}
        
    </div>

    {{-- Tabel Absensi --}}
    <div class="card shadow-lg border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="bg-success text-white">
                        <tr>
                            <th scope="col" class="py-3">No</th>
                            <th scope="col" class="py-3">Tanggal</th>
                            <th scope="col" class="py-3">NIS</th>
                            <th scope="col" class="py-3">Nama Siswa</th>
                            <th scope="col" class="py-3">Status</th>
                            <th scope="col" class="py-3">Jam</th>
                            <th scope="col" class="py-3">Lokasi</th>
                            <th scope="col" class="py-3">Sumber</th>
                            <!-- <th scope="col" class="py-3">Diinput Oleh</th> -->
                            @if (auth()->user()->role == 'admin' || auth()->user()->role == 'guru')
                                <th scope="col" class="py-3 text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($absensi as $a)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{-- Memformat tanggal dari kolom 'tanggal' --}}
                                    {{ \Carbon\Carbon::parse($a->tanggal)->translatedFormat('d F Y') }}
                                </td>
                                <td>{{ $a->nis }}</td>
                                <td>{{ $a->siswa->nama ?? 'Siswa Tidak Ditemukan' }}</td>
                                <td>
                                    @php
                                        // Pastikan status yang di-match adalah huruf kecil sesuai yang disimpan di DB
                                        $statusLower = strtolower($a->status);
                                        $badgeClass = match($statusLower) {
                                            'hadir' => 'bg-primary',
                                            'terlambat' => 'bg-warning text-dark',
                                            'izin' => 'bg-info text-dark',
                                            'sakit' => 'bg-secondary',
                                            'alpa' => 'bg-danger',
                                            default => 'bg-dark',
                                        };
                                    @endphp
                                    {{-- Menampilkan Status dengan Huruf Kapital di Awal --}}
                                    <span class="badge {{ $badgeClass }}">{{ ucfirst($a->status) }}</span>
                                </td>
                                <td>
                                    {{-- Menampilkan Jam secara terpisah --}}
                                    <span class="badge bg-secondary">{{ $a->jam ?? '-' }}</span>
                                </td>
                                <td>
                                    {{-- Kolom LOKASI baru --}}
                                    <small class="text-muted">{{ $a->lokasi ?? 'Tidak Tercatat' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $a->sumber == 'scan' ? 'success' : 'dark' }} text-uppercase">
                                        {{ $a->sumber }}
                                    </span>
                                </td>
                                <!-- <td>
                                    {{-- Kolom DIINPUT OLEH baru (Asumsi relasi 'user' ada di model Absensi) --}}
                                    <small>{{ $a->user->name ?? ' ' }}</small> -->
                                <!-- </td> -->
                                @if (auth()->user()->role == 'admin' || auth()->user()->role == 'guru')
                                    <td class="text-center">
                                        {{-- Tombol Edit (Menggunakan $a->id_absensi) --}}
                                        <a href="{{ route('absensi.edit', $a->id_absensi) }}" class="btn btn-sm btn-outline-primary me-2" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- Tombol Hapus (Menggunakan $a->id_absensi) --}}
                                        <form action="{{ route('absensi.destroy', $a->id_absensi) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data absensi ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                {{-- Penyesuaian colspan untuk 10 kolom --}}
                                <td colspan="10" class="text-center py-4 text-muted">
                                    <i class="fas fa-box-open me-2"></i> Belum ada data absensi yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    {{-- Pagination (Jika Anda menggunakan paginate di Controller) --}}
    {{-- <div class="mt-3">
        {{ $absensi->links() }} 
    </div> --}}

</div>
@endsection
