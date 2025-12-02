@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h3 class="mb-4 text-primary">Daftar Nilai Siswa</h3>

    {{-- Notifikasi sukses --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ================================================ --}}
    {{-- 🔍 BLOK FILTER --}}
    {{-- ================================================ --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light fw-bold">
            <i class="fas fa-filter me-1"></i> Filter Data Nilai
        </div>

        <div class="card-body">
            <form action="{{ route('nilai.index') }}" method="GET" class="row g-3 align-items-end">

                {{-- Filter Kelas --}}
                <div class="col-md-4">
                    <label for="filter_kelas" class="form-label">Filter Berdasarkan Kelas</label>
                    <select name="kelas" id="filter_kelas" class="form-select">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($semuaKelas as $k)
                            <option value="{{ $k->id_kelas }}" 
                                {{ request('kelas') == $k->id_kelas ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Mapel --}}
                <div class="col-md-4">
                    <label for="filter_mapel" class="form-label">Filter Berdasarkan Mapel</label>
                    <select name="mapel" id="filter_mapel" class="form-select">
                        <option value="">-- Semua Mata Pelajaran --</option>
                        @foreach($semuaMapel as $m)
                            <option value="{{ $m->id_mapel }}" 
                                {{ request('mapel') == $m->id_mapel ? 'selected' : '' }}>
                                {{ $m->nama_mapel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tombol --}}
                <div class="col-md-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('nilai.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i> Reset
                    </a>
                </div>

            </form>
        </div>
    </div>

    {{-- Status Filter --}}
    <div class="d-flex justify-content-between mb-3">
        <div>
            @if(request('kelas') || request('mapel'))
                <span class="badge bg-warning text-dark">
                    Filter Aktif:
                    @if(request('kelas'))
                        Kelas {{ $semuaKelas->find(request('kelas'))->nama_kelas ?? 'N/A' }}
                    @endif
                    @if(request('mapel'))
                        | Mapel {{ $semuaMapel->find(request('mapel'))->nama_mapel ?? 'N/A' }}
                    @endif
                </span>
            @endif
        </div>

        <a href="{{ route('nilai.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Tambah Nilai Baru
        </a>
    </div>

    {{-- ================================================ --}}
    {{-- 📄 TABEL DATA NILAI --}}
    {{-- ================================================ --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Siswa (NIS)</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th class="text-center bg-info text-white">Nilai Akhir</th>
                            <th class="text-center">Catatan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($nilai as $n)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>

                                <td>
                                    <strong>{{ $n->siswa->nama ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $n->siswa->nis ?? '-' }}</small>
                                </td>

                                <td>{{ $n->mapel->nama_mapel ?? 'N/A' }}</td>

                                <td>{{ $n->kelas->nama_kelas ?? 'N/A' }}</td>

                                <td class="text-center fw-bold bg-info-subtle">
                                    {{ number_format($n->nilai_akhir, 2) }}
                                </td>

                                <td class="text-center">{{ $n->catatan }}</td>

                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <a href="{{ route('nilai.edit', $n->id_nilai) }}"
                                           class="btn btn-sm btn-warning me-2" title="Edit Data">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <a href="{{ route('nilai.show', $n->id_nilai) }}"
                                           class="btn btn-info btn-sm me-2" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <form action="{{ route('nilai.destroy', $n->id_nilai) }}"
                                              method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus data nilai ini?');">
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
