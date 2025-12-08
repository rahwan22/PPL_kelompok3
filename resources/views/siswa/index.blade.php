@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white shadow-xl rounded-lg p-6">

        <div class="text-center mb-6">
            <h3 class="text-3xl font-bold text-gray-800">Data Siswa Berdasarkan Kelas</h3>
        </div>
        
        <div class="flex flex-col sm:flex-row justify-between items-center mb-4 space-y-3 sm:space-y-0">
            {{-- Tombol Tambah Siswa --}}
            @if (auth()->user()->role !== 'kepala_sekolah')
                <a href="{{ route('siswa.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150">
                    <i class="fas fa-plus mr-2"></i> Tambah Siswa
                </a>
            @else
                <div></div> {{-- Spacer for alignment --}}
            @endif

            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 w-full sm:w-auto">
                {{-- Tombol BARU: Unduh Massal ID Card --}}
                @if (auth()->user()->role === 'admin' && count($kelas) > 0)
                    <button id="downloadMassalBtn" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-green-700 bg-green-100 hover:bg-green-200 transition duration-150">
                        <i class="fas fa-id-card mr-2"></i> Unduh Semua ID Card
                    </button>
                @endif
                
                {{-- Tombol Tampilkan Semua Siswa --}}
                <button id="showAllSiswaBtn" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-indigo-700 bg-indigo-100 hover:bg-indigo-200 transition duration-150">
                    <i class="fas fa-list-alt mr-2"></i> Tampilkan Semua Siswa ({{ $kelas->sum(fn($k) => $k->siswa->count()) }})
                </button>
            </div>
        </div>
        
        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @endif

        {{-- Loop Utama: Per Kelas (sisanya sama) --}}
        @forelse($kelas as $k)
            {{-- ... Kode sebelumnya untuk Accordion Kelas ... --}}
            <div class="mb-4 border border-gray-200 rounded-lg shadow-md overflow-hidden" data-kelas-id="{{ $k->id_kelas }}">
                
                {{-- HEADER KELAS (Clickable) --}}
                <div 
                    class="kelas-header bg-indigo-50 p-4 border-b border-indigo-200 cursor-pointer hover:bg-indigo-100 transition duration-150 flex justify-between items-center" 
                    data-kelas-id="{{ $k->id_kelas }}"
                >
                    <h4 class="text-xl font-semibold text-indigo-800">Kelas: {{ $k->nama_kelas }} ({{ $k->siswa->count() }} Siswa)</h4>
                    <i class="fas fa-chevron-down toggle-icon text-indigo-600 transition-transform duration-300"></i>
                </div>
                
                {{-- TABEL SISWA (Initially hidden) --}}
                <div id="siswa-list-{{ $k->id_kelas }}" class="siswa-container hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">No</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                {{-- Loop Kedua: Siswa di Dalam Kelas --}}
                                @forelse($k->siswa as $s)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $s->nis }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $s->nama }}</td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                {{-- Tombol Detail --}}
                                                <a href="{{ route('siswa.show', $s->nis) }}" class="p-2 text-indigo-600 hover:text-indigo-900 rounded-full bg-indigo-50 hover:bg-indigo-100 transition" title="Detail Siswa">
                                                    <i class="fas fa-eye text-sm"></i>
                                                </a>
                                                
                                                @if (auth()->user()->role === 'admin')
                                                    {{-- Tombol Edit --}}
                                                    <a href="{{ route('siswa.edit', $s->nis) }}" class="p-2 text-yellow-600 hover:text-yellow-900 rounded-full bg-yellow-50 hover:bg-yellow-100 transition" title="Edit Data">
                                                        <i class="fas fa-edit text-sm"></i>
                                                    </a>
                                                    
                                                    {{-- Tombol Hapus (Menggunakan form dengan custom modal/alert karena native alert dilarang) --}}
                                                    <form action="{{ route('siswa.destroy', $s->nis) }}" method="POST" class="inline" onsubmit="return showGenericModal(event, 'Hapus Data', 'Yakin hapus data siswa {{ $s->nama }}? Data tidak dapat dikembalikan.', true)">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-2 text-red-600 hover:text-red-900 rounded-full bg-red-50 hover:bg-red-100 transition" title="Hapus Data">
                                                            <i class="fas fa-trash-alt text-sm"></i>
                                                        </button>
                                                    </form>

                                                    {{-- Tambahan Admin (Lihat Nilai, Generate/Download QR) --}}
                                                    <a href="{{ route('admin.nilai.show_by_siswa', $s->nis) }}" class="px-3 py-1 text-xs font-semibold rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition">
                                                        Nilai
                                                    </a>

                                                    @if(!$s->qr_code)
                                                        <a href="{{ route('admin.siswa.generateQR', $s->nis) }}" class="px-3 py-1 text-xs font-semibold rounded-md text-green-700 bg-green-100 hover:bg-green-200 transition">
                                                            Generate QR
                                                        </a>
                                                    @else
                                                        <a href="{{ route('admin.siswa.downloadQR', $s->nis) }}" class="px-3 py-1 text-xs font-semibold rounded-md text-green-700 bg-green-100 hover:bg-green-200 transition">
                                                            Download QR
                                                        </a>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Tidak ada siswa di kelas ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center p-6 border rounded-lg bg-gray-50 text-gray-600">
                Data Kelas dan Siswa belum tersedia.
            </div>
        @endforelse

        {{-- Modal Generik Kustom (tetap sama) --}}
        <div id="genericModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 overflow-y-auto">
            <div id="genericModalContent" class="bg-white rounded-lg shadow-xl m-auto mt-20 max-w-4xl p-6 transform transition-all">
                <div class="flex justify-between items-center border-b pb-3 mb-4">
                    <h3 id="genericModalTitle" class="text-xl font-bold text-gray-900">Konfirmasi</h3>
                    <button onclick="hideGenericModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                {{-- Area Konten Modal --}}
                <div id="genericModalBody">
                    {{-- Pesan/Tabel akan di-inject di sini --}}
                </div>

                {{-- Area Footer Modal (untuk tombol konfirmasi atau close) --}}
                <div id="genericModalFooter" class="flex justify-end space-x-3 pt-4 border-t mt-4">
                    {{-- Tombol akan di-inject di sini --}}
                </div>
            </div>
        </div>

    </div>
</div>
<script>
    // Definisikan rute yang dibutuhkan oleh JavaScript
    const DOWNLOAD_MASSAL_URL = "{{ route('admin.downloadIdCardMassal') }}";
</script>

<script src="{{ asset('assets/js/siswa.js') }}" defer></script>
<script>
    const allKelasData = @json($kelas);
</script>
@endsection