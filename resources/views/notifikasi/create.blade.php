@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Kirim Notifikasi Baru</h3>
    <form action="{{ route('notifikasi.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nis">Siswa</label>
            <select name="nis" class="form-control" required>
                <option value="">-- Pilih Siswa --</option>
                @foreach($siswa as $s)
                    <option value="{{ $s->nis }}">{{ $s->nama }} ({{ $s->nis }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="id_orangtua">Orang Tua</label>
            <select name="id_orangtua" class="form-control" required>
                <option value="">-- Pilih Orang Tua --</option>
                @foreach($orangtua as $o)
                    <option value="{{ $o->id_orangtua }}">{{ $o->nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="pesan">Pesan</label>
            <textarea name="pesan" class="form-control" rows="3" required></textarea>
        </div>

        <button type="submit" class="btn btn-success">Kirim</button>
        <a href="{{ route('notifikasi.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
