@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Daftar Notifikasi</h3>
    <a href="{{ route('notifikasi.create') }}" class="btn btn-primary mb-3">+ Tambah Notifikasi</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Siswa</th>
                <th>Orang Tua</th>
                <th>Pesan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($notifikasi as $n)
                <tr>
                    <td>{{ $n->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $n->siswa->nama ?? '-' }}</td>
                    <td>{{ $n->orangtua->nama ?? '-' }}</td>
                    <td>{{ $n->pesan }}</td>
                    <td>{{ $n->aksi}}</td>
                    <td>
                        <span class="badge {{ $n->status == 'Dibaca' ? 'bg-success' : 'bg-warning' }}">
                            {{ $n->status }}
                        </span>
                    </td>   
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
