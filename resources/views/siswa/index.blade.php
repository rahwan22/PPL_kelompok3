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

            {{-- Tombol Tampilkan Semua Siswa (BARU) --}}
            <button id="showAllSiswaBtn" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-indigo-700 bg-indigo-100 hover:bg-indigo-200 transition duration-150">
                <i class="fas fa-list-alt mr-2"></i> Tampilkan Semua Siswa ({{ $kelas->sum(fn($k) => $k->siswa->count()) }})
            </button>
        </div>
        
        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @endif

        {{-- Loop Utama: Per Kelas --}}
        @forelse($kelas as $k)
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

        {{-- Modal Generik Kustom (Digunakan untuk Konfirmasi Hapus DAN Tampilkan Semua Siswa) --}}
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
    // --- Data Siswa dari PHP (digunakan untuk modal semua siswa) ---
    // Pastikan Controller Anda me-load $kelas dengan relasi 'siswa'
    const allKelasData = @json($kelas);

    // --- Logika Modal Generik (Menggantikan confirmModal lama) ---
    const modal = document.getElementById('genericModal');
    const modalTitle = document.getElementById('genericModalTitle');
    const modalBody = document.getElementById('genericModalBody');
    const modalFooter = document.getElementById('genericModalFooter');
    const modalContent = document.getElementById('genericModalContent');
    let currentForm = null;

    /**
     * Menampilkan modal generik untuk konfirmasi atau menampilkan data.
     * @param {Event} event - Event dari form submit (jika ada).
     * @param {string} title - Judul Modal.
     * @param {string} contentHtml - HTML content yang dimasukkan ke body modal.
     * @param {boolean} isConfirm - Jika true, tampilkan tombol konfirmasi Hapus. Jika false, tampilkan tombol Tutup.
     * @returns {boolean} Selalu false untuk mencegah submit default form.
     */
    function showGenericModal(event, title, contentHtml, isConfirm = false) {
        if (event) {
            event.preventDefault(); // Mencegah form submit default
            currentForm = event.target;
        } else {
            currentForm = null;
        }
        
        // Reset lebar modal default
        modalContent.classList.remove('max-w-4xl');
        modalContent.classList.add('max-w-sm'); 

        modalTitle.textContent = title;
        modalBody.innerHTML = contentHtml;
        modalFooter.innerHTML = ''; // Kosongkan footer
        
        if (isConfirm) {
            // Jika untuk konfirmasi Hapus
            modalFooter.innerHTML = `
                <button id="modalCancel" onclick="hideGenericModal()" class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300">Batal</button>
                <button id="modalConfirm" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">Ya, Hapus</button>
            `;
            document.getElementById('modalConfirm').onclick = function() {
                hideGenericModal();
                if (currentForm) {
                    currentForm.submit(); // Lanjutkan submit form
                }
            };
        } else {
            // Jika untuk Tampilkan Data (hanya tombol Tutup)
            // Tambahkan tombol Tutup di footer
            modalFooter.innerHTML = `
                <button onclick="hideGenericModal()" class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300">Tutup</button>
            `;
        }

        modal.classList.remove('hidden');
        
        // Atur posisi modal di tengah layar (vertikal)
        setTimeout(() => {
            const viewportHeight = window.innerHeight;
            const contentHeight = modalContent.offsetHeight;
            // Gunakan Math.max untuk memastikan modal tidak terlalu dekat ke atas/bawah
            const topMargin = Math.max(20, (viewportHeight - contentHeight) / 2); 
            modalContent.style.marginTop = topMargin + 'px';
        }, 10);
        
        return false;
    }

    function hideGenericModal() {
        modal.classList.add('hidden');
        currentForm = null;
    }

    // Tutup modal jika klik di luar area modal
    window.onclick = function(event) {
        if (event.target == modal) {
            hideGenericModal();
        }
    };
    
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
        
        // --- Logika Filtering Siswa di Modal ---
        function filterSiswaTable() {
            const input = document.getElementById('siswaSearchInput');
            if (!input) return;

            const filter = input.value.toUpperCase();
            const tableBody = document.getElementById('allSiswaTableBody');
            if (!tableBody) return;

            const tr = tableBody.getElementsByTagName('tr');
            let visibleCount = 0;
            
            for (let i = 0; i < tr.length; i++) {
                // Kolom untuk filtering: Kelas (index 1), NIS (index 2), Nama Siswa (index 3)
                const tds = tr[i].getElementsByTagName('td');
                let found = false;

                if (tds.length > 0) {
                    // Cek di kolom Kelas, NIS, dan Nama Siswa
                    for (let j = 1; j <= 3; j++) {
                        const cellText = tds[j].textContent || tds[j].innerText;
                        if (cellText.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }

                if (found) {
                    tr[i].style.display = '';
                    visibleCount++;
                } else {
                    tr[i].style.display = 'none';
                }
            }
        }
        
        // --- Logika Tombol Tampilkan Semua Siswa ---
        const showAllSiswaBtn = document.getElementById('showAllSiswaBtn');
        showAllSiswaBtn.addEventListener('click', () => {
            let tableRows = '';
            let counter = 1;
            const authRole = "{{ auth()->user()->role ?? '' }}";

            allKelasData.forEach(kelas => {
                kelas.siswa.forEach(siswa => {
                    // Base action link (Detail)
                    let actionHtml = `
                        <a href="{{ route('siswa.show', ':nis') }}" class="p-2 text-indigo-600 hover:text-indigo-900 rounded-full bg-indigo-50 hover:bg-indigo-100 transition" title="Detail Siswa">
                            <i class="fas fa-eye text-sm"></i>
                        </a>
                    `.replace(/:nis/g, siswa.nis);
                    
                    // Tambahkan aksi khusus Admin
                    if (authRole === 'admin') {
                        actionHtml += `
                            <a href="{{ route('siswa.edit', ':nis') }}" class="p-2 text-yellow-600 hover:text-yellow-900 rounded-full bg-yellow-50 hover:bg-yellow-100 transition" title="Edit Data">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            
                            <form action="{{ route('siswa.destroy', ':nis') }}" method="POST" class="inline" onsubmit="return showGenericModal(event, 'Hapus Data', 'Yakin hapus data siswa ${siswa.nama}?', true)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:text-red-900 rounded-full bg-red-50 hover:bg-red-100 transition" title="Hapus Data">
                                    <i class="fas fa-trash-alt text-sm"></i>
                                </button>
                            </form>
                            
                            <a href="{{ route('admin.nilai.show_by_siswa', ':nis') }}" class="px-3 py-1 text-xs font-semibold rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition">
                                Nilai
                            </a>

                            ${siswa.qr_code 
                                ? `<a href="{{ route('admin.siswa.downloadQR', ':nis') }}" class="px-3 py-1 text-xs font-semibold rounded-md text-green-700 bg-green-100 hover:bg-green-200 transition">Download QR</a>`
                                : `<a href="{{ route('admin.siswa.generateQR', ':nis') }}" class="px-3 py-1 text-xs font-semibold rounded-md text-green-700 bg-green-100 hover:bg-green-200 transition">Generate QR</a>`
                            }
                        `.replace(/:nis/g, siswa.nis);
                    }

                    tableRows += `
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">${counter++}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">${kelas.nama_kelas}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">${siswa.nis}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">${siswa.nama}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex items-center justify-center space-x-2">${actionHtml}</div>
                            </td>
                        </tr>
                    `;
                });
            });

            // Buat HTML tabel, termasuk input pencarian
            const tableHtml = `
                <div class="mb-4">
                    <input type="text" id="siswaSearchInput" onkeyup="filterSiswaTable()" placeholder="Cari berdasarkan NIS, Nama, atau Kelas..." 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="overflow-x-auto max-h-96">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">No</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="allSiswaTableBody" class="bg-white divide-y divide-gray-200">
                            ${tableRows}
                        </tbody>
                    </table>
                </div>
            `;
            
            // Perluas modal untuk tabel yang lebih besar
            modalContent.classList.remove('max-w-sm');
            modalContent.classList.add('max-w-4xl');

            // Tampilkan modal dengan tabel siswa
            showGenericModal(null, 'Daftar Semua Siswa', tableHtml, false);
            
            // Pasang fungsi filter ke window agar bisa diakses dari onkeyup
            window.filterSiswaTable = filterSiswaTable;
        });
    });

    // Perbaiki pemanggilan form hapus agar menggunakan modal generik baru
    document.querySelectorAll('form[onsubmit*="showConfirmModal"]').forEach(form => {
        // Ini memastikan form Hapus di loop kelas tetap menggunakan modal generik baru
        const titleMatch = form.getAttribute('onsubmit').match(/'([^']*)'/g);
        if (titleMatch && titleMatch.length >= 2) {
             const title = titleMatch[0].replace(/'/g, '');
             const message = titleMatch[1].replace(/'/g, '');
             form.setAttribute('onsubmit', `return showGenericModal(event, '${title}', '<p class="text-sm text-gray-700 mb-6">${message}</p>', true)`);
        }
    });
</script>
@endsection