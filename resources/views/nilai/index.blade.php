@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Daftar Nilai Siswa</h3>
    @if (auth()->user()->role !== 'kepala_sekolah')
        <a href="{{ route('nilai.create') }}" class="btn btn-success mb-3">+ Tambah Nilai</a>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Mata Pelajaran</th>
                <th>Kelas</th>
                <th>Tugas</th>
                <th>UTS</th>
                <th>UAS</th>
                <th>Nilai Akhir</th>
                <th>Semester</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nilai as $n)
                <tr>
                    <td>{{ $n->siswa->nis }}</td>
                    <td>{{ $n->siswa->nama }}</td>
                    <td>{{ $n->mapel->nama_mapel }}</td>
                    <td>{{ $n->id_kelas }}</td>
                    <td>{{ $n->nilai_tugas }}</td>
                    <td>{{ $n->nilai_uts }}</td>
                    <td>{{ $n->nilai_uas }}</td>
                    <td>{{ number_format($n->nilai_akhir, 2) }}</td>
                    <td>{{ $n->semester }}</td>
                    <td>

                     @if (auth()->user()->role !== 'kepala_sekolah')
                        <a href="{{ route('nilai.edit', $n->id_nilai) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('nilai.destroy', $n->id_nilai) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin?')">Hapus</button>
                        </form>
                    @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
