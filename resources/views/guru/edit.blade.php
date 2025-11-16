@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-xl mt-10">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Edit Data Guru: {{ $guru->nama }}</h1>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-400 rounded-lg">
            <p class="font-bold">Terjadi Kesalahan Validasi:</p>
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-400 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Pastikan form menggunakan enctype="multipart/form-data" untuk upload foto -->
    <form action="{{ route('guru.update', $guru->id_guru) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Nama -->
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="nama" id="nama" value="{{ old('nama', $guru->nama) }}" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('nama') border-red-500 @enderror">
                @error('nama')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- NIP -->
            <div>
                <label for="nip" class="block text-sm font-medium text-gray-700 mb-1">NIP (Opsional)</label>
                <input type="text" name="nip" id="nip" value="{{ old('nip', $guru->nip) }}"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('nip') border-red-500 @enderror">
                @error('nip')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email (Digunakan untuk Login)</label>
                <input type="email" name="email" id="email" value="{{ old('email', $guru->email) }}" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jenis Kelamin -->
            <div>
                <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                <select name="jenis_kelamin" id="jenis_kelamin" required
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('jenis_kelamin') border-red-500 @enderror">
                    <option value="L" {{ old('jenis_kelamin', $guru->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('jenis_kelamin', $guru->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('jenis_kelamin')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- No HP -->
            <div>
                <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-1">Nomor HP</label>
                <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $guru->no_hp) }}" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('no_hp') border-red-500 @enderror">
                @error('no_hp')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mata Pelajaran -->
            <div>
                <label for="mapel" class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran yang Diampu (Opsional)</label>
                <input type="text" name="mapel" id="mapel" value="{{ old('mapel', $guru->mapel) }}"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('mapel') border-red-500 @enderror">
                @error('mapel')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Alamat -->
            <div class="md:col-span-2">
                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                <textarea name="alamat" id="alamat" rows="2" required
                          class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('alamat') border-red-500 @enderror">{{ old('alamat', $guru->alamat) }}</textarea>
                @error('alamat')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Foto -->
            <div class="md:col-span-2">
                <label for="foto" class="block text-sm font-medium text-gray-700 mb-1">Foto Profil (Kosongkan jika tidak ingin diubah)</label>
                
                @if ($guru->foto)
                    <div class="mb-2 flex items-center">
                         <!-- Tampilkan foto lama menggunakan Storage::url() -->
                        <img src="{{ Storage::url($guru->foto) }}" alt="Foto Lama" class="w-16 h-16 object-cover rounded-md border mr-4">
                        <span class="text-xs text-gray-500">Foto saat ini akan diganti jika Anda mengunggah file baru.</span>
                    </div>
                @endif
                
                <input type="file" name="foto" id="foto" 
                       class="mt-1 block w-full text-sm text-gray-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-full file:border-0
                              file:text-sm file:font-semibold
                              file:bg-indigo-50 file:text-indigo-700
                              hover:file:bg-indigo-100 @error('foto') border-red-500 @enderror">
                @error('foto')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            
        </div>

        <div class="mt-8 pt-4 border-t flex justify-end">
            <a href="{{ route('guru.index') }}" class="px-6 py-2 text-sm font-semibold rounded-lg text-gray-600 border border-gray-300 hover:bg-gray-50 mr-3">Batal / Kembali</a>
            <button type="submit" class="px-6 py-2 text-sm font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-md transition duration-150 ease-in-out">
                Perbarui Data
            </button>
        </div>
    </form>
</div>
@endsection