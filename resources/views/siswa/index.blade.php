@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white shadow-xl rounded-lg p-6">

        <div class="text-center mb-6">
            <h3 class="text-3xl font-bold text-gray-800">Data Siswa Berdasarkan Kelas</h3>
        </div>
        
        {{-- Tombol Tambah Siswa --}}
        @if (auth()->user()->role !== 'kepala_sekolah')
            <a href="{{ route('siswa.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150 mb-4">
                + Tambah Siswa
            </a>
        @endif
        
        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @endif

        {{-- Loop Utama: Per Kelas --}}
        @forelse($kelas as $k)
            <div class="mb-4 border border-gray-200 rounded-lg shadow-md overflow-hidden">
                
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
                                                    <form action="{{ route('siswa.destroy', $s->nis) }}" method="POST" class="inline" onsubmit="return showConfirmModal(event, 'Hapus Data', 'Yakin hapus data siswa {{ $s->nama }}? Data tidak dapat dikembalikan.')">
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

        {{-- Modal Konfirmasi Kustom (Menggantikan window.confirm) --}}
        <div id="confirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
            <div class="bg-white rounded-lg shadow-xl m-auto mt-20 max-w-sm p-6">
                <h3 id="confirmModalTitle" class="text-lg font-bold text-gray-900 mb-4">Konfirmasi</h3>
                <p id="confirmModalMessage" class="text-sm text-gray-700 mb-6">Apakah Anda yakin?</p>
                <div class="flex justify-end space-x-3">
                    <button id="confirmModalCancel" class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300">Batal</button>
                    <button id="confirmModalConfirm" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">Ya, Hapus</button>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // --- Logika Toggle Accordion ---
    document.addEventListener('DOMContentLoaded', () => {
        const headers = document.querySelectorAll('.kelas-header');

        headers.forEach(header => {
            header.addEventListener('click', () => {
                const kelasId = header.getAttribute('data-kelas-id');
                const siswaContainer = document.getElementById(`siswa-list-${kelasId}`);
                const icon = header.querySelector('.toggle-icon');

                if (siswaContainer.classList.contains('hidden')) {
                    // Tampilkan konten
                    siswaContainer.classList.remove('hidden');
                    icon.classList.add('rotate-180'); // Putar ikon ke atas
                } else {
                    // Sembunyikan konten
                    siswaContainer.classList.add('hidden');
                    icon.classList.remove('rotate-180'); // Kembalikan ikon ke bawah
                }
            });
        });
    });


    // --- Logika Modal Konfirmasi Kustom ---
    const modal = document.getElementById('confirmModal');
    const modalTitle = document.getElementById('confirmModalTitle');
    const modalMessage = document.getElementById('confirmModalMessage');
    const modalCancel = document.getElementById('confirmModalCancel');
    const modalConfirm = document.getElementById('confirmModalConfirm');
    let currentForm = null;

    function showConfirmModal(event, title, message) {
        event.preventDefault(); // Mencegah form submit default
        currentForm = event.target;
        
        modalTitle.textContent = title;
        modalMessage.textContent = message;
        modal.classList.remove('hidden');

        // Mengatur posisi modal di tengah layar
        const modalContent = modal.querySelector('div');
        // Pastikan modal Content ada sebelum mengakses offsetHeight
        if (modalContent) {
            modalContent.style.marginTop = (window.innerHeight - modalContent.offsetHeight) / 2 + 'px';
        }


        return false; // Selalu kembalikan false untuk mencegah submit
    }

    modalCancel.onclick = function() {
        modal.classList.add('hidden');
        currentForm = null;
    };

    modalConfirm.onclick = function() {
        modal.classList.add('hidden');
        if (currentForm) {
            currentForm.submit(); // Lanjutkan submit form
        }
    };

    // Tutup modal jika klik di luar area modal
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.classList.add('hidden');
            currentForm = null;
        }
    };
</script>
@endsection