@extends('layouts.app')

@section('content')

<div class="container mt-4">
<h3 class="mb-4 text-primary">Daftar Nilai Siswa</h3>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('nilai.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-circle me-1"></i> Tambah Nilai Baru
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th scope="col" class="text-center">#</th>
                        <th scope="col">Siswa (NIS)</th>
                        <th scope="col">Mata Pelajaran</th>
                        <th scope="col">Kelas</th> 
                        <th scope="col" class="text-center bg-info text-white">Nilai Akhir</th>
                        <th scope="col" class="text-center">Catatan</th>
                        <th scope="col" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($nilai as $n)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $n->siswa->nama ?? 'N/A' }}</strong> <br>
                                <small class="text-muted">{{ $n->siswa->nis ?? '-' }}</small>
                            </td>
                            <td>{{ $n->mapel->nama_mapel ?? $n->id_mapel ?? 'N/A' }}</td>
                            <td>{{ $n->kelas->nama_kelas ?? $n->id_kelas ?? 'N/A' }}</td>
                            <td class="text-center fw-bold bg-info-subtle">{{ number_format($n->nilai_akhir, 2) }}</td>

                            <td class="text-center">{{ $n->catatan }}</td>
                            
                            <td class="text-center">
                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('nilai.edit', $n->id_nilai) }}" class="btn btn-sm btn-warning me-2" title="Edit Data">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <!-- Tombol Edit: ✅ PERBAIKAN: Menggunakan $n->id_nilai -->

                                    <a href="{{ route('nilai.show', $n->id_nilai) }}" class="btn btn-info btn-sm me-2" title="show Data">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <!-- Tombol Hapus: ✅ PERBAIKAN: Menggunakan $n->id_nilai -->
                                    <form action="{{ route('nilai.destroy', $n->id_nilai) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data nilai ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus Data">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-4 text-muted">
                                <i class="bi bi-info-circle me-1"></i> Belum ada data nilai yang tercatat.
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