@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 max-w-5xl">
    <h1 class="text-3xl font-extrabold mb-8 text-indigo-800 border-b-4 border-indigo-200 pb-2">Data Siswa Lengkap</h1>

    <div class="bg-white p-8 rounded-3xl shadow-2xl border border-indigo-100">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Kolom Kiri: Visual & Aksi Cepat -->
            <div class="lg:col-span-1 flex flex-col items-center space-y-6">
                
                <!-- Foto Siswa -->
                <div class="w-56 h-56 rounded-full overflow-hidden border-8 border-indigo-500/50 shadow-xl transform transition duration-300 hover:scale-[1.02]">
                    @if ($siswa->foto)
                        <img src="{{ asset('storage/' . $siswa->foto) }}" alt="Foto Siswa" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-500 text-lg font-semibold">
                            Foto Tidak Tersedia
                        </div>
                    @endif
                </div>

                <h2 class="text-3xl font-extrabold text-gray-900 text-center">{{ $siswa->nama }}</h2>
                
                <!-- Status & NIS Badge -->
                <div class="flex flex-col items-center space-y-2">
                    <span class="text-base font-bold text-indigo-600">NIS: {{ $siswa->nis }}</span>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full 
                        {{ $siswa->aktif ? 'bg-green-100 text-green-800 ring-2 ring-green-300' : 'bg-red-100 text-red-800 ring-2 ring-red-300' }}">
                        Status: {{ $siswa->aktif ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                
                <!-- QR Code Section -->
                <div class="mt-4 text-center p-4 bg-indigo-50 rounded-xl w-full">
                    <h3 class="text-md font-semibold text-indigo-700 mb-2">QR Code ID</h3>
                    @if ($siswa->qr_code)
                        <img src="{{ asset('storage/qr/' . $siswa->qr_code) }}" alt="QR Code" class="mx-auto w-32 h-32 border-4 border-white p-1 rounded-lg shadow-md">
                        <a href="{{ route('admin.siswa.generateQR', $siswa->nis) }}" class="text-sm text-indigo-600 hover:text-indigo-800 mt-3 block font-medium transition duration-150 ease-in-out hover:underline">Generate QR Code</a>
                    
                    @endif
                </div>

            </div>

            <!-- Kolom Kanan: Detail Informasi (Gabungan) -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Informasi Dasar Siswa (Akademik & Pribadi) -->
                <div class="bg-indigo-50 p-6 rounded-xl border border-indigo-200">
                    <h3 class="text-xl font-bold text-indigo-700 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Informasi Dasar Siswa
                    </h3>
                    <div class="space-y-3 text-gray-700">
                        
                        <!-- Kelas -->
                        <div class="flex justify-between border-b border-indigo-100 pb-2">
                            <span class="font-medium">Kelas</span>
                            <span class="text-lg font-extrabold text-indigo-800">{{ $siswa->kelas->nama_kelas ?? 'N/A' }}</span>
                        </div>

                        <!-- Jenis Kelamin -->
                        <div class="flex justify-between border-b border-indigo-100 pb-2">
                            <span class="font-medium">Jenis Kelamin</span>
                            <span>{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="flex justify-between border-b border-indigo-100 pb-2">
                            <span class="font-medium">Tanggal Lahir</span>
                            <span>{{ optional($siswa->tanggal_lahir)->format('d F Y') ?? 'N/A' }}</span>
                        </div>

                        <!-- Alamat -->
                        <div class="flex flex-col pt-2">
                            <span class="font-medium mb-1 flex items-center text-indigo-700">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Alamat Lengkap
                            </span>
                            <p class="text-sm bg-white p-3 rounded-lg border border-indigo-200 shadow-inner">{{ $siswa->alamat ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Informasi Orang Tua -->
                <div class="mt-8 p-6 rounded-xl border border-green-300 bg-green-50 shadow-md">
                    <h3 class="text-xl font-bold mb-4 text-green-700 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20h-2m2 0h-2m0-9a4 4 0 100 8m0-8a4 4 0 110 8m-9-6a4 4 0 110 8m0-8a4 4 0 100 8m-9-6a4 4 0 100 8m0-8a4 4 0 110 8"></path></svg>
                        Informasi Orang Tua
                    </h3>
                    @if ($siswa->orangtua)
                        <div class="space-y-2 text-gray-800">
                            <div class="flex justify-between border-b border-green-200 pb-1">
                                <span class="font-semibold w-40">Nama Ortu:</span>
                                <span class="text-right font-bold">{{ $siswa->orangtua->nama }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-semibold w-40">No. WhatsApp:</span>
                                <span class="text-right text-green-600 font-mono text-lg hover:underline cursor-pointer transition duration-150 ease-in-out">
                                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $siswa->orangtua->no_wa) }}" target="_blank">{{ $siswa->orangtua->no_wa }}</a>
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="p-3 rounded-lg border border-red-300 text-red-700 bg-red-100">
                            <p class="font-medium text-sm">Data Orang Tua belum terhubung atau tidak ditemukan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Aksi di Bagian Bawah -->
        <div class="mt-10 pt-6 border-t border-gray-200 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
            {{-- Tombol Unduh Kartu Siswa --}}
            <a href="{{ route('siswa.cetak.id', $siswa->nis) }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-lg transition duration-300 flex items-center justify-center sm:justify-start hover:shadow-xl">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Unduh Kartu Siswa
            </a>
            
            <a href="{{ route('siswa.edit', $siswa->nis) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg shadow-lg transition duration-300 hover:shadow-xl text-center">
                Edit Siswa
            </a>
            <a href="{{ route('siswa.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg shadow-lg transition duration-300 text-center">
                Kembali ke Daftar
            </a>
        </div>
    </div>
</div>
@endsection