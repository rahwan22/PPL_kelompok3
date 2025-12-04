
        // Pemicu cetak otomatis setelah halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Memberi jeda singkat agar semua konten (terutama gambar) dimuat sebelum mencetak
            setTimeout(() => {
                window.print();
            }, 800);
        });
    