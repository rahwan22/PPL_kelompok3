@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <!-- {{-- 1. Mengatur Tombol Tambah --}}
    @if (auth()->user()->role === 'admin')
        {{-- Tombol Tambah hanya muncul untuk role 'admin' --}}
        <a href="{{ route('mapel.create') }}" class="btn btn-primary mb-3">+ Tambah Mata Pelajaran</a>
    @endif -->
    @if (auth()->user()->role === 'kepala_sekolah ')
        <a href="{{ route('mapel.create') }}" class="btn btn-success mb-3">+ Tambah Mapel</a>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Kode Mapel</th>
                <th>Nama Mapel</th>
                {{-- Kolom Aksi hanya muncul untuk role 'admin' --}}
                @if (auth()->user()->role === 'admin')
                    <th>Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($mapel as $m)
                <tr>
                    <td>{{ $m->id_mapel }}</td>
                    <td>{{ $m->kode_mapel }}</td>
                    <td>{{ $m->nama_mapel }}</td>
                    
                    {{-- 2. Mengatur Tombol Edit & Hapus --}}
                    @if (auth()->user()->role === 'admin')
                        <td>
                            <a href="{{ route('mapel.edit', $m->id_mapel) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('mapel.destroy', $m->id_mapel) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">Hapus</button>
                            </form>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection