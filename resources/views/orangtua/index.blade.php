@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3>Data Orang Tua</h3>
    <a href="{{ route('orangtua.create') }}" class="btn btn-primary mb-3">+ Tambah Orang Tua</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Email</th>
                <th>No. WA</th>
                <th>Preferensi Notifikasi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orangtua as $o)
                <tr>
                    <td>{{ $o->id_orangtua }}</td>
                    <td>{{ $o->nama }}</td>
                    <td>{{ $o->email ?? '-' }}</td>
                    <td>{{ $o->no_wa ?? '-' }}</td>
                    <td>
                        @if($o->preferensi_notif)
                            {{ implode(', ', json_decode($o->preferensi_notif, true)) }}
                        @else
                            <em>Tidak ada</em>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('orangtua.edit', $o->id_orangtua) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('orangtua.destroy', $o->id_orangtua) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
