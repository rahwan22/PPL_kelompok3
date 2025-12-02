@extends('layouts.app')

@section('content')
<div class="container mt-5">
    {{-- Header dengan gaya yang lebih modern --}}
    <h2 class="h3 font-weight-bold text-primary text-center mb-4">
        <i class="fas fa-school me-2"></i> Data Kelas
    </h2>

    {{-- Tombol Tambah Kelas (Hanya terlihat jika role ADALAH admin) --}}
    @if (auth()->user()->role === 'admin')
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
                            <th scope="col" class="py-3 text-center">Jumlah Siswa</th> 
                            <th scope="col" class="py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kelas as $k) 
                            <tr style="cursor: pointer;" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#siswa-{{ $k->id_kelas }}" 
                                aria-expanded="false" 
                                aria-controls="siswa-{{ $k->id_kelas }}"
                                class="kelas-row"
                            >
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
                                {{-- Kolom Jumlah Siswa --}}
                                <td class="text-center">
                                    <span class="badge bg-primary rounded-pill">
                                        {{ $k->siswa->count() }} 
                                    </span>
                                </td>
                                
                                {{-- Kolom Aksi (Hanya untuk Admin) --}}
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group" onclick="event.stopPropagation()">
                                        <a href="{{ route('kelas.show', $k->id_kelas) }}"  class="btn btn-info btn-sm me-2" title="Detail kelas">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if (auth()->user()->role === 'admin' )
                                        {{-- Tombol Edit --}}
                                        <a href="{{ route('kelas.edit', $k->id_kelas) }}" class="btn btn-primary btn-sm me-2" title="Edit Data" onclick="event.stopPropagation()">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('kelas.destroy', $k->id_kelas) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus Data" onclick="event.stopPropagation(); return confirm('Apakah Anda yakin ingin menghapus kelas {{ $k->nama_kelas }}? Aksi ini tidak dapat dibatalkan.');">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                {{-- PERBAIKAN: Colspan diatur menjadi 6 sesuai jumlah kolom header --}}
                                <td colspan="6" class="text-center py-4 text-muted">
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
