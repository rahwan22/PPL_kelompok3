document.addEventListener('DOMContentLoaded', function () {
    const headers = document.querySelectorAll('.alokasi-header');

    headers.forEach(header => {
        header.addEventListener('click', function () {
            // Dapatkan ID elemen detail yang ditargetkan dari data-target
            const targetId = this.getAttribute('data-target');
            const detail = document.getElementById(targetId);
            const icon = this.querySelector('.alokasi-icon');

            // Toggle kelas 'hidden' untuk visibility dan 'active' untuk transisi
            detail.classList.toggle('hidden');
            
            // Menggunakan setTimeout agar transisi max-height berfungsi setelah 'hidden' dihapus
            setTimeout(() => {
                detail.classList.toggle('active');
            }, 10); 
            
            // Toggle rotasi ikon
            icon.classList.toggle('rotate');
        });
    });
});
