@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white shadow-xl rounded-xl mt-10">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Tambah Data Siswa</h1>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-400 rounded-lg">
            <p class="font-bold">Terjadi Kesalahan Validasi:</p>
            <ul class="list-disc ml-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('siswa.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <h3 class="md:col-span-2 text-xl font-semibold text-indigo-700 mt-4 mb-2 border-b pb-2">Data Siswa</h3>
            
            {{-- NIS --}}
            <div>
                <label for="nis" class="block text-sm font-medium text-gray-700 mb-1">NIS</label>
                <input type="text" name="nis" id="nis" value="{{ old('nis') }}" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('nis') border-red-500 @enderror">
                @error('nis')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nama Siswa --}}
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Siswa</label>
                <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('nama') border-red-500 @enderror">
                @error('nama')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Jenis Kelamin --}}
             <div class="mb-4">
                <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                <select name="jenis_kelamin" id="jenis_kelamin" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="L" 
                        {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>
                        Laki-laki
                    </option>
                    <option value="P" 
                        {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>
                        Perempuan
                    </option>
                </select>
                @error('jenis_kelamin')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tanggal Lahir --}}
            <div>
                <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir (Opsional)</label>
                <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_lahir') border-red-500 @enderror">
                @error('tanggal_lahir')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Alamat --}}
            <div class="md:col-span-2">
                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="alamat" id="alamat" rows="2" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('alamat') border-red-500 @enderror">{{ old('alamat') }}</textarea>
                @error('alamat')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>


            {{-- Kelas --}}
            <div class="md:col-span-2">
                <label for="id_kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                <select name="id_kelas" id="id_kelas" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('id_kelas') border-red-500 @enderror">
                    @foreach($kelas as $k)
                    <option value="{{ $k->id_kelas }}" {{ old('id_kelas') == $k->id_kelas ? 'selected' : '' }}>
                        {{ $k->nama_kelas }} ({{ $k->tahun_ajaran }})
                    </option>
                    @endforeach
                </select>
                @error('id_kelas')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Foto -->
            <div class="md:col-span-2">
                <label for="foto" class="block text-sm font-medium text-gray-700 mb-1">Foto Profil (Maks 2MB)</label>
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

        <!-- Bagian Data Orang Tua -->
        <h3 class="text-xl font-semibold text-indigo-700 mt-8 mb-4 border-b pb-2">Data Orang Tua</h3>
        
        <div class="p-4 mb-6 bg-yellow-50 border-l-4 border-yellow-500 text-yellow-800 rounded-lg">
            <p class="text-sm font-medium">Anda dapat **membuat data Orang Tua baru** atau **menghubungkan ke Orang Tua yang sudah terdaftar**.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- Nama Orang Tua BARU (FIXED: Ganti nama input) --}}
            <div>
                <label for="orangtua" class="block text-sm font-medium text-gray-700 mb-1">Nama Orang Tua Baru (Opsional)</label>
                <input type="text" name="orangtua" id="orangtua" value="{{ old('orangtua') }}" placeholder="Kosongkan jika memilih Orang Tua yang sudah ada"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500 @error('orangtua') border-red-500 @enderror">
                @error('orangtua')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Nomor WA Orang Tua BARU (FIXED: Ganti nama input & label/error ref) --}}
            <div>
                <label for="no_wa" class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp Baru (Contoh: 62812xxxx)</label>
                <input type="text" name="no_wa" id="no_wa" value="{{ old('no_wa') }}" placeholder="Tanpa '+', Wajib diisi jika Nama Orang Tua di atas diisi."
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500 @error('no_wa') border-red-500 @enderror">
                @error('no_wa')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Dropdown Opsi: Pilih Orang Tua Yang Sudah Ada --}}
            <!-- <div class="md:col-span-2 mt-4">
                <p class="text-sm font-semibold text-gray-700 mb-1">Atau Pilih Orang Tua Yang Sudah Ada:</p>
                <select name="id_orangtua" id="id_orangtua" 
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('id_orangtua') border-red-500 @enderror">
                    <option value="">-- Pilih Orang Tua (Jika Sudah Terdaftar) --</option>
                    @foreach($orangtua as $o)
                    <option value="{{ $o->id_orangtua }}" {{ old('id_orangtua') == $o->id_orangtua ? 'selected' : '' }}>
                        {{ $o->nama }} (WA: {{ $o->no_wa ?? '-' }})
                    </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-indigo-600">Pilih opsi ini HANYA JIKA Anda tidak mengisi Nama dan Nomor WA Orang Tua Baru di atas.</p>
                @error('id_orangtua')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div> -->

        </div>


        <div class="mt-8 pt-4 border-t flex justify-end">
            <a href="{{ route('siswa.index') }}" class="px-6 py-2 text-sm font-semibold rounded-lg text-gray-600 border border-gray-300 hover:bg-gray-50 mr-3">Batal</a>
            <button type="submit" class="px-6 py-2 text-sm font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-md transition duration-150 ease-in-out">
                Simpan Siswa
            </button>
        </div>
    </form>
</div>
@endsection