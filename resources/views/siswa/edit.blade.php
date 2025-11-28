@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 max-w-3xl">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Edit Siswa: {{ $siswa->nama }}</h1>

    {{-- Notifikasi Error Validasi --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    {{-- Form Edit Siswa --}}
    <form action="{{ route('siswa.update', $siswa->nis) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-xl shadow-lg">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Data Siswa -->
            <div class="col-span-full border-b pb-2 mb-4">
                <h2 class="text-xl font-semibold text-indigo-700">Data Siswa</h2>
            </div>
            
            <div class="col-span-full">
                <label for="nis_display" class="block text-sm font-medium text-gray-700">NIS (Tidak dapat diubah)</label>
                <input type="text" id="nis_display" value="{{ $siswa->nis }}" disabled
                       class="mt-1 block w-full p-3 border border-gray-300 bg-gray-100 rounded-lg shadow-sm">
            </div>

            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" name="nama" id="nama" value="{{ old('nama', $siswa->nama) }}" required
                       class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('nama') border-red-500 @enderror">
            </div>
            
            <div>
                <label for="id_kelas" class="block text-sm font-medium text-gray-700">Kelas</label>
                <select name="id_kelas" id="id_kelas" required
                        class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('id_kelas') border-red-500 @enderror">
                    <option value="">Pilih Kelas</option>
                    {{-- Asumsi variabel $kelas tersedia --}}
                    @foreach ($kelas as $kls)
                        <option value="{{ $kls->id_kelas }}" {{ old('id_kelas', $siswa->id_kelas) == $kls->id_kelas ? 'selected' : '' }}>
                            {{ $kls->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                <select name="jenis_kelamin" id="jenis_kelamin" required
                        class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('jenis_kelamin') border-red-500 @enderror">
                    <option value="">Pilih</option>
                    <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            
            <div>
                <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir', optional($siswa->tanggal_lahir)->format('Y-m-d')) }}" 
                       class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_lahir') border-red-500 @enderror">
            </div>

            <div class="col-span-full">
                <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                <textarea name="alamat" id="alamat" rows="3"
                          class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('alamat') border-red-500 @enderror">{{ old('alamat', $siswa->alamat) }}</textarea>
            </div>
            
            <!-- Foto Section -->
            <div class="col-span-full md:col-span-1">
                <label class="block text-sm font-medium text-gray-700">Foto Saat Ini</label>
                @if ($siswa->foto)
                    <img src="{{ asset('storage/' . $siswa->foto) }}" alt="Foto Siswa" class="mt-2 w-32 h-32 object-cover rounded-lg shadow-md">
                    <div class="mt-2 flex items-center">
                        <input type="checkbox" name="hapus_foto" id="hapus_foto" value="1" class="rounded text-red-600">
                        <label for="hapus_foto" class="ml-2 text-sm text-red-600">Hapus Foto Lama</label>
                    </div>
                @else
                    <p class="mt-2 text-sm text-gray-500">Belum ada foto.</p>
                @endif
            </div>

            <div class="col-span-full md:col-span-1">
                <label for="foto" class="block text-sm font-medium text-gray-700">Upload Foto Baru (Opsional)</label>
                <input type="file" name="foto" id="foto" 
                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 @error('foto') border-red-500 @enderror">
            </div>
            
            <!-- Status Aktif -->
            <div class="col-span-full">
                <label for="aktif" class="flex items-center space-x-2 text-sm font-medium text-gray-700">
                    <input type="checkbox" name="aktif" id="aktif" value="1" class="rounded text-indigo-600" {{ old('aktif', $siswa->aktif) ? 'checked' : '' }}>
                    <span>Siswa Aktif</span>
                </label>
            </div>

            <!-- Data Orang Tua -->
            <div class="col-span-full border-b pb-2 mb-4 mt-4">
                <h2 class="text-xl font-semibold text-indigo-700">Data Orang Tua</h2>
            </div>

            <div class="col-span-full">
                <label for="orangtua_mode" class="block text-sm font-medium text-gray-700 mb-2">Pilih Mode Orang Tua:</label>
                <div class="flex space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="orangtua_mode" value="exist" id="modeExist" 
                                class="form-radio text-indigo-600 h-4 w-4" checked>
                        <span class="ml-2 text-gray-700">Pilih dari yang Sudah Ada</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="orangtua_mode" value="new" id="modeNew" 
                                class="form-radio text-indigo-600 h-4 w-4">
                        <span class="ml-2 text-gray-700">Buat Baru / Ubah Sementara</span>
                    </label>
                </div>
            </div>

            <!-- Pilihan Orang Tua (Mode EXIST) -->
            <div id="orangtuaExist" class="col-span-full transition-all duration-300">
                <label for="id_orangtua_pilihan" class="block text-sm font-medium text-gray-700">Pilih Orang Tua</label>
                <select name="id_orangtua_pilihan" id="id_orangtua_pilihan"
                        class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('id_orangtua_pilihan') border-red-500 @enderror">
                    <option value="">-- Pilih Orang Tua/Wali --</option>
                    {{-- Asumsi variabel $orangtua tersedia --}}
                    @foreach ($orangtua as $ortu)
                        <option value="{{ $ortu->id_orangtua }}" 
                            {{ old('id_orangtua_pilihan', $siswa->id_orangtua) == $ortu->id_orangtua ? 'selected' : '' }}>
                            {{ $ortu->nama }} (WA: {{ $ortu->no_wa }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Data Orang Tua Baru (Mode NEW) -->
            <div id="orangtuaNew" class="col-span-full grid grid-cols-1 md:grid-cols-2 gap-6 transition-all duration-300 hidden">
                <div>
                    <label for="nama_ortu_baru" class="block text-sm font-medium text-gray-700">Nama Orang Tua Baru</label>
                    <input type="text" name="nama_ortu_baru" id="nama_ortu_baru" value="{{ old('nama_ortu_baru') }}"
                           class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('nama_ortu_baru') border-red-500 @enderror">
                </div>
                <div>
                    <label for="no_wa_ortu_baru" class="block text-sm font-medium text-gray-700">No. WhatsApp Baru</label>
                    <input type="text" name="no_wa_ortu_baru" id="no_wa_ortu_baru" value="{{ old('no_wa_ortu_baru') }}"
                           class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('no_wa_ortu_baru') border-red-500 @enderror">
                    @error('no_wa_ortu_baru')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
        </div>
        
        <div class="mt-8 pt-4 border-t flex justify-end">
            <a href="{{ route('siswa.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg shadow-lg transition duration-300 text-center">
                Kembali ke Daftar
            </a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300">
                Update Data Siswa
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modeExist = document.getElementById('modeExist');
        const modeNew = document.getElementById('modeNew');
        const orangtuaExistDiv = document.getElementById('orangtuaExist');
        const orangtuaNewDiv = document.getElementById('orangtuaNew');
        
        function toggleOrangTuaMode() {
            if (modeExist.checked) {
                orangtuaExistDiv.classList.remove('hidden');
                orangtuaNewDiv.classList.add('hidden');
            } else if (modeNew.checked) {
                orangtuaExistDiv.classList.add('hidden');
                orangtuaNewDiv.classList.remove('hidden');
            }
        }

        modeExist.addEventListener('change', toggleOrangTuaMode);
        modeNew.addEventListener('change', toggleOrangTuaMode);

        // Inisialisasi: Periksa jika ada data old input error, jika tidak, gunakan mode default (EXIST)
        @if (old('nama_ortu_baru') || old('no_wa_ortu_baru'))
            modeNew.checked = true;
        @elseif (old('orangtua_mode') === 'new')
            modeNew.checked = true;
        @else
            modeExist.checked = true;
        @endif
        toggleOrangTuaMode();
    });
</script>
@endsection