@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white shadow-xl rounded-xl mt-8">
    <div class="flex justify-between items-center border-b pb-4 mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Detail Data Guru</h1>
        <a href="{{ route('guru.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition duration-150">&larr; Kembali</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- Kolom Kiri: Foto --}}
        <div class="md:col-span-1 flex justify-center items-start">
            <div class="w-full max-w-xs p-3 bg-gray-50 border border-gray-200 rounded-xl shadow-lg">
                @if ($guru->foto)
                    <img 
                        src="{{ asset('storage/' . $guru->foto) }}" 
                        alt="Foto Profil Guru {{ $guru->nama }}" 
                        class="w-full h-auto object-cover rounded-lg border-4 border-indigo-200 shadow-md">
                @else
                    <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500 text-6xl border-4 border-indigo-200 shadow-md">
                        @if($guru->jenis_kelamin == 'L')
                            <span title="Laki-laki">👨</span>
                        @else
                            <span title="Perempuan">👩</span>
                        @endif
                    </div>
                @endif
                <p class="text-center mt-2 text-gray-600 text-sm font-medium">{{ $guru->nama }}</p>
            </div>
        </div>

        {{-- Kolom Kanan: Detail Data --}}
        <div class="md:col-span-2 space-y-4">
            <h2 class="text-2xl font-bold text-indigo-700">{{ $guru->nama }}</h2>
            
            <div class="space-y-3 text-gray-700">
                
                <!-- NIP -->
                <div class="flex items-center">
                    <span class="font-medium w-36">NIP</span>
                    <span class="ml-2">: {{ $guru->nip ?? '-' }}</span>
                </div>

                <!-- Email -->
                <div class="flex items-center">
                    <span class="font-medium w-36">Email</span>
                    <span class="ml-2 text-indigo-600">: {{ $guru->email }}</span>
                </div>

                <!-- Jenis Kelamin -->
                <div class="flex items-center">
                    <span class="font-medium w-36">Jenis Kelamin</span>
                    {{-- Pastikan logika Jenis Kelamin benar: L = Laki-laki, P = Perempuan --}}
                    <span class="ml-2">: 
                        {{ $guru->jenis_kelamin == 'L' ? 'Laki-laki' : ($guru->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}
                    </span>
                </div>
                
                <!-- Tanggal Lahir (Saya tambahkan jika ada kolom ini di tabel guru) -->
                @if (isset($guru->tanggal_lahir))
                <div class="flex items-center">
                    <span class="font-medium w-36">Tanggal Lahir</span>
                    <span class="ml-2">: 
                        {{ $guru->tanggal_lahir ? \Carbon\Carbon::parse($guru->tanggal_lahir)->isoFormat('D MMMM YYYY') : '-' }}
                    </span>
                </div>
                @endif

                
                
               
                <!-- No HP -->
                <div class="flex items-center">
                    <span class="font-medium w-36">No. HP</span>
                    <span class="ml-2">: {{ $guru->no_hp }}</span>
                </div>
                
                {{-- Alamat Lengkap (Dibuat menjadi blok terpisah seperti detail siswa) --}}
                <div class="pt-4 mt-2 border-t">
                    <p class="font-medium mb-1">Alamat Lengkap</p>
                    <p class="text-sm">{{ $guru->alamat }}</p>
                </div>
            </div>
        </div>
    </div>
    
    @if (auth()->user()->role === 'admin')
        <div class="mt-8 pt-4 border-t flex justify-end">
            <a href="{{ route('guru.edit', $guru->id_guru) }}" class="px-4 py-2 text-sm font-semibold rounded-lg text-white bg-yellow-500 hover:bg-yellow-600 shadow-md transition duration-150 ease-in-out">
                Edit Data
            </a>
        </div>
    @endif
</div>
@endsection