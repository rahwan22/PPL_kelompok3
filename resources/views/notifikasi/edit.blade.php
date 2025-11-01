@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Notifikasi</h3>
    <form action="{{ route('notifikasi.update', $notifikasi->id_notifikasi) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Siswa</label>
            <input type="text" class="form-control" value="{{ $notifikasi->siswa->nama }}" disabled>
        </div>

        <div class="mb-3">
            <label>Orang Tua</label>
            <input type="text" class="form-control" value="{{ $notifikasi->orangtua->nama }}" disabled>
        </div>

        <div class="mb-3">
            <label>Pesan</label>
            <textarea name="pesan" class="form-control" rows="3" required>{{ $notifikasi->pesan }}</textarea>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="Belum Dibaca" {{ $notifikasi->status == 'Belum Dibaca' ? 'selected' : '' }}>Belum Dibaca</option>
                <option value="Dibaca" {{ $notifikasi->status == 'Dibaca' ? 'selected' : '' }}>Dibaca</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('notifikasi.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
