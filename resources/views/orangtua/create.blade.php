@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3>Tambah Orang Tua</h3>

    <form action="{{ route('orangtua.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control">
        </div>
        <div class="mb-3">
            <label>No. WhatsApp</label>
            <input type="text" name="no_wa" class="form-control">
        </div>
        <div class="mb-3">
            <label>Preferensi Notifikasi</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="preferensi_notif[]" value="email">
                <label class="form-check-label">Email</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="preferensi_notif[]" value="wa">
                <label class="form-check-label">WhatsApp</label>
            </div>
        </div>
        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('orangtua.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
