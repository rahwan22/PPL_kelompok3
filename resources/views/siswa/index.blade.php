@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <center><h3 class="mb-3">Data Siswa</h3></center>
    @if (auth()->user()->role !== 'kepala_sekolah')
        <a href="{{ route('siswa.create') }}" class="btn btn-success mb-3">+ Tambah Siswa</a>
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>NIS</th>
                <th>Nama</th>
                <th>Jenis Kelamin</th>
                <th>Kelas</th>
                <th>Orang Tua</th>
                <th>Status</th>
                @if (auth()->user()->role === 'admin')
                <th>Aksi</th>
                 @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($siswa as $s)
                <tr>
                    <td>{{ $s->nis }}</td>
                    <td>{{ $s->nama }}</td>
                    <td>{{ $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    <td>{{ $s->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ $s->orangtua->nama ?? '-' }}</td>
                    <td>{{ $s->aktif ? 'Aktif' : 'Nonaktif' }}</td>
                    
                    @if (auth()->user()->role === 'admin')
                    <td>
                        <a href="{{ route('admin.siswa.generateQR', $s->nis) }}" class="btn btn-sm btn-primary">
                            Generate QR
                        </a>

                        @if($s->qr_code)
                            <a href="{{ route('admin.siswa.downloadQR', $s->nis) }}" class="btn btn-sm btn-success">
                                Download QR
                            </a>
                        @endif

                        <a href="{{ route('siswa.edit', $s->nis) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('siswa.destroy', $s->nis) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data?')">Hapus</button>
                        </form>
                    </td>
                        @endif
                    
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
