


@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white shadow-2xl rounded-xl mt-10">
    {{-- Header dan Tombol Aksi --}}
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-3xl font-extrabold text-indigo-800">
            Detail Siswa: {{ $siswa->nama }}
        </h1>
        <div class="flex space-x-3">
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('siswa.edit', $siswa->nis) }}" class="px-4 py-2 text-sm font-semibold rounded-lg text-white bg-green-600 hover:bg-green-700 transition duration-150">
                    <i class="fas fa-edit mr-1"></i> Edit Data
                </a>
            @endif
            <a href="{{ route('siswa.index') }}" class="px-4 py-2 text-sm font-semibold rounded-lg text-gray-600 border border-gray-300 hover:bg-gray-100 transition duration-150">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Kolom Kiri: Foto Profil --}}
        <div class="lg:col-span-1 flex flex-col items-center">
            <div class="md:col-span-1 flex justify-center items-start">
            <div class="w-full max-w-xs p-3 bg-gray-50 border border-gray-200 rounded-xl shadow-lg">
                <img 
                    src="{{ $siswa->foto ? asset('storage/' . $siswa->foto) : 'https://placehold.co/300x400/C8A2C8/ffffff?text=No+Photo' }}" 
                    alt="Foto Profil Siswa {{ $siswa->nama }}" 
                    class="w-full h-auto object-cover rounded-lg border-4 border-indigo-200 shadow-md">
                <!-- <p class="text-center mt-2 text-gray-600 text-sm font-medium">Foto Siswa</p> -->
            </div>
        </div>


            <p class="text-sm font-semibold text-gray-500">Status:</p>
            @if ($siswa->aktif)
                <span class="inline-block px-3 py-1 text-xs font-semibold leading-none rounded-full text-green-800 bg-green-100">
                    AKTIF
                </span>
            @else
                <span class="inline-block px-3 py-1 text-xs font-semibold leading-none rounded-full text-red-800 bg-red-100">
                    TIDAK AKTIF
                </span>
            @endif
        </div>

        {{-- Kolom Kanan: Data Siswa dan Kelas --}}
        <div class="lg:col-span-2">
            <h2 class="text-2xl font-bold text-gray-700 border-b-2 border-indigo-100 mb-4 pb-2">Informasi Utama</h2>
            
            <dl class="space-y-3 text-gray-600">
                <div class="flex border-b border-gray-100 pb-2">
                    <dt class="w-1/3 font-medium text-gray-800">NIS</dt>
                    <dd class="w-2/3">{{ $siswa->nis }}</dd>
                </div>
                <div class="flex border-b border-gray-100 pb-2">
                    <dt class="w-1/3 font-medium text-gray-800">Nama Lengkap</dt>
                    <dd class="w-2/3 text-lg font-semibold text-indigo-700">{{ $siswa->nama }}</dd>
                </div>
                <div class="flex border-b border-gray-100 pb-2">
                    <dt class="w-1/3 font-medium text-gray-800">Jenis Kelamin</dt>
                    <dd class="w-2/3">{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</dd>
                </div>
                <div class="flex border-b border-gray-100 pb-2">
                    <dt class="w-1/3 font-medium text-gray-800">Tanggal Lahir</dt>
                    <dd class="w-2/3">{{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->locale('id')->isoFormat('D MMMM YYYY') : '-' }}</dd>
                </div>
                <div class="flex border-b border-gray-100 pb-2">
                    <dt class="w-1/3 font-medium text-gray-800">Alamat</dt>
                    <dd class="w-2/3">{{ $siswa->alamat ?? '-' }}</dd>
                </div>
            </dl>

            <h2 class="text-2xl font-bold text-gray-700 border-b-2 border-indigo-100 mt-8 mb-4 pb-2">Informasi Kelas</h2>
            <dl class="space-y-3 text-gray-600">
                <div class="flex border-b border-gray-100 pb-2">
                    <dt class="w-1/3 font-medium text-gray-800">Kelas Saat Ini</dt>
                    {{-- Asumsi model Siswa memiliki relasi 'kelas' --}}
                    <dd class="w-2/3 text-md font-semibold text-pink-600">
                        @if ($siswa->kelas)
                            {{ $siswa->kelas->nama_kelas }}
                        @else
                            <span class="text-red-500">Tidak Terdaftar</span>
                        @endif
                    </dd>
                </div>
                <div class="flex border-b border-gray-100 pb-2">
                    <dt class="w-1/3 font-medium text-gray-800">Tahun Ajaran</dt>
                    <dd class="w-2/3">{{ $siswa->kelas ? $siswa->kelas->tahun_ajaran : '-' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Detail Orang Tua (Bagian Baru) --}}
    <div class="mt-10 p-6 bg-indigo-50 border border-indigo-200 rounded-xl shadow-inner">
        <h2 class="text-2xl font-bold text-indigo-700 border-b-2 border-indigo-400 mb-4 pb-2">SEKOLAH DASAR NEGERI SWASTA</h2>
        
        <!-- @if ($siswa->orangtua)
            {{-- Asumsi model Siswa memiliki relasi 'orangtua' --}}
            <dl class="space-y-3 text-gray-700">
                <div class="flex border-b border-indigo-100 pb-2">
                    <dt class="w-1/3 font-medium text-indigo-900">Nama Orang Tua</dt>
                    <dd class="w-2/3 text-lg font-bold text-indigo-800">{{ $siswa->id_orangtua }}</dd>
                </div>
                <div class="flex border-b border-indigo-100 pb-2">
                    <dt class="w-1/3 font-medium text-indigo-900">Nomor WhatsApp</dt>
                    <dd class="w-2/3">{{ $siswa->id_orangtua ?? 'Tidak Tersedia' }}</dd>
                </div>
                {{-- Tambahkan detail Orang Tua lain sesuai kebutuhan (misalnya, pekerjaan) --}}
            </dl>
        @else
            <div class="text-center p-4 bg-yellow-100 border border-yellow-300 rounded-lg">
                <p class="text-yellow-800 font-semibold">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Siswa ini belum terhubung dengan data Orang Tua/Wali.
                </p>
                <a href="{{ route('siswa.edit', $siswa->nis) }}" class="mt-2 inline-block text-sm text-yellow-700 hover:text-yellow-900 underline">Hubungkan Sekarang</a>
            </div>
        @endif -->
    </div>
</div>
@endsection