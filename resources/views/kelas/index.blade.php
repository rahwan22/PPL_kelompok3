@extends('layouts.app')

@section('content')
<div class="container mt-5">
    {{-- Header dengan gaya yang lebih modern --}}
    <h2 class="h3 font-weight-bold text-primary text-center mb-4">
        <i class="fas fa-school me-2"></i> Data Kelas
    </h2>

    {{-- Tombol Tambah Kelas (Hanya terlihat jika role BUKAN kepala_sekolah, yaitu Admin, Guru, dll.) --}}
    @if (auth()->user()->role !== 'kepala_sekolah')
        <a href="{{ route('kelas.create') }}" class="btn btn-success mb-4 rounded-pill shadow-sm">
            <i class="fas fa-plus me-1"></i> Tambah Kelas
        </a>
    @endif

    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Tabel Utama --}}
    <div class="card shadow-lg border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-primary text-white">
                        <tr>
                            <th scope="col" class="py-3">ID</th>
                            <th scope="col" class="py-3">Nama Kelas</th>
                            <th scope="col" class="py-3">Tahun Ajaran</th>
                            <th scope="col" class="py-3">Wali Kelas</th>
                            {{-- Tampilkan header Aksi hanya jika ada tombol aksi yang akan ditampilkan --}}
                            @if (auth()->user()->role !== 'kepala_sekolah')
                                <th scope="col" class="py-3 text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kelas as $k)
                            <tr>
                                <td>{{ $k->id_kelas }}</td>
                                <td>
                                    <strong>{{ $k->nama_kelas }}</strong>
                                </td>
                                <td>{{ $k->tahun_ajaran }}</td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $k->waliKelas->nama ?? 'Belum Ditentukan' }}
                                    </span>
                                </td>
                                
                                {{-- Kolom Aksi (Hanya untuk non-Kepala Sekolah) --}}
                                @if (auth()->user()->role !== 'kepala_sekolah')
                                    <td class="text-center">
                                        {{-- Tombol Edit --}}
                                        <a href="{{ route('kelas.edit', $k->id_kelas) }}" class="btn btn-warning btn-sm me-2" title="Edit Data">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('kelas.destroy', $k->id_kelas) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus Data" onclick="return confirm('Apakah Anda yakin ingin menghapus kelas {{ $k->nama_kelas }}? Aksi ini tidak dapat dibatalkan.');">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                {{-- Penyesuaian colspan agar tabel tidak pecah --}}
                                @php $colspan = (auth()->user()->role !== 'kepala_sekolah') ? 5 : 4; @endphp
                                <td colspan="{{ $colspan }}" class="text-center py-4 text-muted">
                                    <i class="fas fa-box-open me-2"></i> Belum ada data kelas yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
