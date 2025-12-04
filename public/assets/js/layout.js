
        function confirmDelete(nama, id) {
            // first ask: apakah ingin hapus guru?
            if (!confirm('Yakin hapus data guru ' + nama + '? Tekan OK untuk lanjut.')) {
                return;
            }
            // second ask: apakah akun user juga ikut dihapus?
            var delUser = confirm('Apakah akun login (user) guru ini juga ingin dihapus? OK = Ya, Cancel = Tidak');
            document.getElementById('delete_user_' + id).value = delUser ? '1' : '0';
            document.getElementById('delete-form-' + id).submit();
        }

