<?php
session_start();
include 'koneksi.php';

// Cek akses: Hanya Guru yang boleh masuk
if($_SESSION['role'] != 'guru') { header("location:index.php"); exit(); }

if(isset($_POST['buat'])){
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $guru_id = $_SESSION['id_user'];
    
    // Generate Kode Unik 5 Karakter (Huruf Besar & Angka)
    $kode = strtoupper(substr(md5(time()), 0, 5));

    // Simpan ke database
    $query = "INSERT INTO kelas (guru_id, nama_kelas, deskripsi, kode_kelas) VALUES ('$guru_id', '$nama', '$deskripsi', '$kode')";
    
    if(mysqli_query($conn, $query)){
        echo "<script>alert('Kelas berhasil dibuat! Kode Kelas: $kode'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal membuat kelas.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Buat Kelas Baru - Mentora</title>
    <!-- META TAG RESPONSIVE -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* Tema Mentora */
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: #EAF3FF; 
            margin: 0; 
            display: flex; justify-content: center; align-items: center; min-height: 100vh;
            padding: 20px; box-sizing: border-box;
        }
        
        .card-form {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(10, 31, 68, 0.1);
            width: 100%; max-width: 500px;
            border-top: 6px solid #1F75FE; /* Aksen Biru Modern */
        }

        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { margin: 0; color: #0A1F44; font-size: 24px; font-weight: 800; }
        .header p { color: #666; font-size: 14px; margin-top: 5px; }

        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 700; color: #3C4A5A; margin-bottom: 8px; font-size: 14px; }
        
        input[type="text"], textarea {
            width: 100%; padding: 14px 15px; /* Padding diperbesar */
            border: 1px solid #ddd; border-radius: 10px;
            box-sizing: border-box; font-family: inherit; font-size: 15px; /* Font diperbesar */
            background: #F8F9FA; transition: 0.3s;
        }
        input:focus, textarea:focus { border-color: #1F75FE; background: white; outline: none; }

        .btn-submit {
            width: 100%; padding: 14px; /* Tombol diperbesar */
            background: #1F75FE; color: white;
            border: none; border-radius: 10px;
            font-weight: bold; font-size: 16px; cursor: pointer;
            transition: 0.2s; box-shadow: 0 4px 15px rgba(31, 117, 254, 0.3);
            margin-top: 10px;
        }
        .btn-submit:hover { background: #0A1F44; transform: translateY(-2px); }

        .btn-back {
            display: block; text-align: center; margin-top: 25px;
            text-decoration: none; color: #666; font-weight: 600; font-size: 14px;
        }
        .btn-back:hover { color: #1F75FE; }

        /* --- KHUSUS MOBILE (HP) --- */
        @media (max-width: 600px) {
            body {
                align-items: flex-start; /* Agar tidak selalu di tengah vertikal jika keyboard muncul */
                padding-top: 40px;
            }
            .card-form {
                padding: 30px 20px; /* Padding samping dikurangi agar muat */
                border-radius: 12px;
            }
            .header h2 { font-size: 22px; }
            input[type="text"], textarea { font-size: 16px; padding: 15px; } /* Input lebih besar lagi */
            .btn-submit { padding: 16px; font-size: 17px; } /* Tombol lebih besar lagi */
        }
    </style>
</head>
<body>

    <div class="card-form">
        <div class="header">
            <h2>🚀 Buat Kelas Baru</h2>
            <p>Siapkan ruang belajar digital untuk siswa Anda.</p>
        </div>

        <form method="POST">
            <div class="form-group">
                <label>Nama Kelas / Mata Pelajaran</label>
                <input type="text" name="nama" placeholder="Contoh: Matematika X-IPA 1" required autocomplete="off">
            </div>

            <div class="form-group">
                <label>Deskripsi Singkat</label>
                <textarea name="deskripsi" rows="4" placeholder="Jelaskan secara singkat tentang kelas ini..." required></textarea>
            </div>

            <button type="submit" name="buat" class="btn-submit">Buat Kelas Sekarang</button>
        </form>

        <a href="index.php" class="btn-back">Batal & Kembali ke Dashboard</a>
    </div>

</body>
</html>