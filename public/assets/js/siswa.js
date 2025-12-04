
    // --- Data Siswa dari PHP (digunakan untuk modal semua siswa) ---
    // const allKelasData = @json($kelas);

    // --- Logika Modal Generik (tetap sama) ---
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
     * @param {boolean} isMassDownload - BARU: Jika true, modal untuk unduh massal.
     * @returns {boolean} Selalu false untuk mencegah submit default form.
     */
    function showGenericModal(event, title, contentHtml, isConfirm = false, isMassDownload = false) {
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
            // Jika untuk Tampilkan Data (hanya tombol Tutup) atau Unduh Massal
            let closeButton = `<button onclick="hideGenericModal()" class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300">Tutup</button>`;
            
            if (isMassDownload) {
                // Tambahkan tombol Unduh Massal jika diaktifkan
                modalFooter.innerHTML = `
                    <button onclick="hideGenericModal()" class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300">Batal</button>
                    <button id="modalMassDownload" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">Unduh ID Card Terpilih</button>
                `;
            } else {
                modalFooter.innerHTML = closeButton;
            }
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
    
    // --- Logika Toggle Accordion (tetap sama) ---
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
        
        // --- Logika Filtering Siswa di Modal (tetap sama) ---
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
                    // Mulai dari index 1 karena index 0 adalah Checkbox/No
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
        
        // --- Logika Tombol Tampilkan Semua Siswa (ditambahkan checkbox) ---
        const showAllSiswaBtn = document.getElementById('showAllSiswaBtn');
        showAllSiswaBtn.addEventListener('click', () => {
            let tableRows = '';
            let counter = 1;
            const authRole = "{{ auth()->user()->role ?? '' }}";

            allKelasData.forEach(kelas => {
                kelas.siswa.forEach(siswa => {
                    // ... (logika actionHtml tetap sama)
                    let actionHtml = `
                        <a href="{{ route('siswa.show', ':nis') }}" class="p-2 text-indigo-600 hover:text-indigo-900 rounded-full bg-indigo-50 hover:bg-indigo-100 transition" title="Detail Siswa">
                            <i class="fas fa-eye text-sm"></i>
                        </a>
                    `.replace(/:nis/g, siswa.nis);
                    
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

        // --- Logika Tombol Unduh Massal ID Card (BARU) ---
        const downloadMassalBtn = document.getElementById('downloadMassalBtn');
        if (downloadMassalBtn) {
            downloadMassalBtn.addEventListener('click', () => {
                let tableRows = '';
                let counter = 1;
                const totalSiswa = allKelasData.reduce((sum, k) => sum + k.siswa.length, 0);

                allKelasData.forEach(kelas => {
                    kelas.siswa.forEach(siswa => {
                        // Hanya Siswa yang sudah punya QR Code yang bisa diunduh ID Card-nya
                        const isDownloadable = siswa.qr_code;
                        const disabledClass = isDownloadable ? '' : 'bg-gray-100 text-gray-400 cursor-not-allowed';
                        const disabledAttr = isDownloadable ? '' : 'disabled';

                        tableRows += `
                            <tr class="${disabledClass}">
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                    ${isDownloadable ? `<input type="checkbox" name="nis_list[]" value="${siswa.nis}" class="nis-checkbox form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out">` : '<i class="fas fa-ban text-red-500" title="QR Code belum digenerate"></i>'}
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">${counter++}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">${kelas.nama_kelas}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">${siswa.nis}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">${siswa.nama}</td>
                            </tr>
                        `;
                    });
                });

                const massDownloadTableHtml = `
                    <p class="text-sm text-gray-700 mb-4">Pilih siswa yang ID Card-nya ingin diunduh secara massal. Minimal ID Card per halaman adalah 6.</p>
                    <div class="mb-4 flex justify-between items-center">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" id="selectAllNis" class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out">
                            <span class="text-sm font-medium text-gray-700">Pilih Semua (${totalSiswa})</span>
                        </label>
                        <p class="text-sm font-semibold text-indigo-600" id="selectedCount">0 siswa terpilih</p>
                    </div>
                    <div class="mb-4">
                        <input type="text" id="siswaSearchInputMassal" onkeyup="filterSiswaTableMassal()" placeholder="Cari berdasarkan NIS, Nama, atau Kelas..." 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="overflow-x-auto max-h-96">
                        <form id="massDownloadForm" method="GET" action="{{ route('admin.siswa.downloadIdCardMassal') }}" target="_blank">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 sticky top-0 z-10">
                                    <tr>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">Pilih</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">No</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                    </tr>
                                </thead>
                                <tbody id="allSiswaTableBodyMassal" class="bg-white divide-y divide-gray-200">
                                    ${tableRows}
                                </tbody>
                            </table>
                        </form>
                    </div>
                `;
                
                // Perluas modal
                modalContent.classList.remove('max-w-sm');
                modalContent.classList.add('max-w-4xl');

                // Tampilkan modal Unduh Massal (isMassDownload = true)
                showGenericModal(null, 'Unduh Massal ID Card Siswa', massDownloadTableHtml, false, true);
                
                // Pasang logika Checkbox
                const selectAll = document.getElementById('selectAllNis');
                const checkboxes = document.querySelectorAll('#allSiswaTableBodyMassal .nis-checkbox');
                const selectedCountText = document.getElementById('selectedCount');
                const modalMassDownloadBtn = document.getElementById('modalMassDownload');

                function updateSelectedCount() {
                    const checkedCount = document.querySelectorAll('#allSiswaTableBodyMassal .nis-checkbox:checked').length;
                    selectedCountText.textContent = `${checkedCount} siswa terpilih`;
                    modalMassDownloadBtn.disabled = checkedCount === 0;
                    modalMassDownloadBtn.classList.toggle('opacity-50', checkedCount === 0);
                }

                selectAll.addEventListener('change', (e) => {
                    checkboxes.forEach(cb => {
                        // Hanya pilih yang tidak disabled (sudah ada QR Code)
                        if (!cb.disabled) {
                            cb.checked = e.target.checked;
                        }
                    });
                    updateSelectedCount();
                });

                checkboxes.forEach(cb => {
                    cb.addEventListener('change', updateSelectedCount);
                });
                
                // Logika Filter Massal
                window.filterSiswaTableMassal = function() {
                    const input = document.getElementById('siswaSearchInputMassal');
                    const filter = input.value.toUpperCase();
                    const tableBody = document.getElementById('allSiswaTableBodyMassal');
                    const tr = tableBody.getElementsByTagName('tr');
                    
                    for (let i = 0; i < tr.length; i++) {
                        const tds = tr[i].getElementsByTagName('td');
                        let found = false;

                        if (tds.length >= 5) {
                            // Kolom Kelas (2), NIS (3), Nama Siswa (4)
                            for (let j = 2; j <= 4; j++) {
                                const cellText = tds[j].textContent || tds[j].innerText;
                                if (cellText.toUpperCase().indexOf(filter) > -1) {
                                    found = true;
                                    break;
                                }
                            }
                        }

                        if (found) {
                            tr[i].style.display = '';
                        } else {
                            tr[i].style.display = 'none';
                        }
                    }
                };
                
                // Aksi tombol Unduh
                modalMassDownloadBtn.addEventListener('click', () => {
                    // Pastikan semua checkbox yang terpilih ada di form
                    const form = document.getElementById('massDownloadForm');
                    // Hapus input NIS sebelumnya
                    form.querySelectorAll('input[name="nis_list[]"]').forEach(input => input.remove());

                    document.querySelectorAll('#allSiswaTableBodyMassal .nis-checkbox:checked').forEach(checkbox => {
                        // Clone checkbox yang terpilih dan masukkan ke dalam form
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'nis_list[]';
                        hiddenInput.value = checkbox.value;
                        form.appendChild(hiddenInput);
                    });
                    
                    hideGenericModal();
                    form.submit(); // Submit form untuk mengarahkan ke Controller
                });
                
                updateSelectedCount(); // Inisialisasi hitungan
            });
        }


        // Perbaiki pemanggilan form hapus agar menggunakan modal generik baru (tetap sama)
        document.querySelectorAll('form[onsubmit*="showConfirmModal"]').forEach(form => {
            // Ini memastikan form Hapus di loop kelas tetap menggunakan modal generik baru
            const titleMatch = form.getAttribute('onsubmit').match(/'([^']*)'/g);
            if (titleMatch && titleMatch.length >= 2) {
                 const title = titleMatch[0].replace(/'/g, '');
                 const message = titleMatch[1].replace(/'/g, '');
                 form.setAttribute('onsubmit', `return showGenericModal(event, '${title}', '<p class="text-sm text-gray-700 mb-6">${message}</p>', true)`);
            }
        });
    });
