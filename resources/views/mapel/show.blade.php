


@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white shadow-xl rounded-xl mt-10">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-3xl font-extrabold text-gray-800">Detail Mata Pelajaran</h1>
        <a href="{{ route('mapel.index') }}" class="px-4 py-2 text-sm font-semibold rounded-lg text-gray-600 border border-gray-300 hover:bg-gray-50 transition duration-150">
            Kembali ke Daftar
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 border rounded-lg bg-gray-50">
        <div class="col-span-1">
            <p class="text-sm font-medium text-gray-500">Kode Mata Pelajaran</p>
            <p class="text-xl font-bold text-indigo-700 mt-1">{{ $mapel->kode_mapel }}</p>
        </div>
        <div class="col-span-1">
            <p class="text-sm font-medium text-gray-500">Nama Mata Pelajaran</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ $mapel->nama_mapel }}</p>
        </div>
        <div class="col-span-1">
            <p class="text-sm font-medium text-gray-500">Dibuat Pada</p>
            <p class="text-md text-gray-600 mt-1">{{ $mapel->created_at->format('d M Y, H:i') }}</p>
        </div>
    </div>
    
    {{-- Daftar Guru Pengajar --}}
    <div class="mt-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">Guru Pengajar Mata Pelajaran Ini</h2>
        
        @if ($mapel->guruMengajar->isEmpty())
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-800">
                Belum ada guru yang terdaftar mengajar mata pelajaran ini.
            </div>
        @else
            <ul class="space-y-3">
                @foreach ($mapel->guruMengajar as $guru)
                    <li class="flex items-center justify-between p-3 bg-white shadow rounded-lg border border-gray-100">
                        <div class="flex items-center space-x-3">
                            <span class="text-indigo-600 text-lg font-semibold">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </span>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $guru->nama }}</p>
                                <p class="text-sm text-gray-500">
                                    NIP: {{ $guru->nip ?? '-' }} 
                                    @if($guru->pivot->id_kelas)
                                        | Mengajar di Kelas ID: {{ $guru->pivot->id_kelas }} 
                                        {{-- Anda mungkin ingin mengambil nama kelas di sini menggunakan relasi Kelas --}}
                                    @endif
                                </p>
                            </div>
                        </div>
                        {{-- Mengarahkan ke kelas.show hanya jika id_kelas ada --}}
                        @if($guru->pivot->id_kelas)
                            <a href="{{ route('kelas.show', $guru->pivot->id_kelas) }}" class="text-sm text-blue-500 hover:underline">Lihat daftar siswa</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="mt-8 pt-4 border-t flex justify-end">
        <a href="{{ route('mapel.edit', $mapel->id_mapel) }}" class="px-6 py-2 text-sm font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-md transition duration-150 ease-in-out">
            Edit Mata Pelajaran
        </a>
    </div>
</div>
@endsection