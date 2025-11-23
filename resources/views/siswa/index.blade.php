@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <center><h3 class="mb-3">Data Siswa</h3></center>
    
    {{-- Tombol Tambah Siswa --}}
    @if (auth()->user()->role !== 'kepala_sekolah')
        <a href="{{ route('siswa.create') }}" class="btn btn-success mb-3">+ Tambah Siswa</a>
    @endif
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Tabel Utama --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr class="text-center">
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th scope="col" class="py-3 text-center">Aksi</th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach($siswa as $s)
                <tr>
                    {{-- Menggunakan $loop->iteration untuk No Urut --}}
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $s->nis }}</td>
                    <td>{{ $s->nama }}</td>
                    <td>{{ $s->kelas->nama_kelas ?? '-' }}</td>
                    
                    
                    <td class="text-center">
                        <div>
                            {{-- Tombol Detail --}}
                        <a href="{{ route('siswa.show', $s->nis) }}" class="btn btn-info btn-sm me-2" title="Detail siswa">
                            <i class="fas fa-eye"></i>
                        </a>
                        @if (auth()->user()->role === 'admin')
                        <a href="{{ route('admin.nilai.show_by_siswa', $s->nis) }}" class="btn btn-info btn-sm">
                            Lihat Nilai
                        </a>

                            <a href="{{ route('admin.siswa.generateQR', $s->nis) }}" class="btn btn-sm btn-primary">
                                Generate QR
                            </a>

                        @if($s->qr_code)
                            <a href="{{ route('admin.siswa.downloadQR', $s->nis) }}" class="btn btn-sm btn-success">
                                Download QR
                            </a>
                        @endif
                        
                        
                        {{-- Tombol Edit --}}
                        <a href="{{ route('siswa.edit', $s->nis) }}" class="btn btn-warning btn-sm me-2" title="Edit Data">
                            <i class="fas fa-edit"></i>
                        </a>
                        
                        {{-- Tombol Hapus (Dibuat d-inline dan menggunakan alert konfirmasi standar) --}}
                        <form action="{{ route('siswa.destroy', $s->nis) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data siswa {{ $s->nama }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                        @endif
                        </div>
                    </td>
                    
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination (jika data Siswa menggunakan pagination) --}}
    @if (method_exists($siswa, 'links'))
        <div class="mt-3">
            {{ $siswa->links() }}
        </div>
    @endif
    
</div>
@endsection