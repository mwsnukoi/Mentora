<?php
session_start();

// Hapus semua data sesi
session_unset();
session_destroy();

// Arahkan kembali ke halaman login
header("location:login.php");
exit();
?>
```

### 2. Cek Folder `uploads`
Pastikan di dalam folder proyek Anda (`htdocs/elearning/` atau `htdocs/mentora/`) sudah terdapat folder bernama **`uploads`**.

Jika belum ada:
1.  Buat folder baru.
2.  Beri nama **`uploads`** (huruf kecil semua).
3.  Folder ini penting untuk menyimpan Foto Profil, Materi (PDF/Video), dan Foto Berita.

### 3. Integrasi Footer ke Halaman Lain (Opsional tapi Bagus)
Tadi kita baru menambahkan Footer di `index.php`. Agar tampilan konsisten, Anda bisa menambahkan kode footer di file-file utama lainnya.

Caranya mudah, cukup tambahkan satu baris ini:
```php
<?php include 'footer.php'; ?>