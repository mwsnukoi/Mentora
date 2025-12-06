<?php
session_start();
include 'koneksi.php';

// Cek akses: Hanya Siswa yang boleh masuk
if($_SESSION['role'] != 'siswa') { header("location:index.php"); exit(); }

if(isset($_POST['gabung'])){
    $kode = mysqli_real_escape_string($conn, $_POST['kode']);
    $siswa_id = $_SESSION['id_user'];

    // Cek validitas kode kelas
    $cek = mysqli_query($conn, "SELECT id FROM kelas WHERE kode_kelas='$kode'");
    
    if(mysqli_num_rows($cek) > 0){
        $data = mysqli_fetch_assoc($cek);
        $kelas_id = $data['id'];

        // Cek apakah sudah pernah bergabung sebelumnya?
        $cek_join = mysqli_query($conn, "SELECT * FROM kelas_siswa WHERE kelas_id='$kelas_id' AND siswa_id='$siswa_id'");
        
        if(mysqli_num_rows($cek_join) == 0){
            mysqli_query($conn, "INSERT INTO kelas_siswa (kelas_id, siswa_id) VALUES ('$kelas_id', '$siswa_id')");
            echo "<script>alert('Berhasil bergabung ke kelas!'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('Anda sudah terdaftar di kelas ini.');</script>";
        }
    } else {
        echo "<script>alert('Kode kelas tidak ditemukan! Periksa kembali kode dari guru Anda.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gabung Kelas - Mentora</title>
    <!-- META TAG WAJIB UNTUK RESPONSIVE -->
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
            padding: 50px 40px; /* Padding diperbesar */
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(10, 31, 68, 0.1);
            width: 100%; max-width: 480px; /* Diperlebar sedikit */
            border-top: 6px solid #0A1F44; /* Aksen Navy */
            text-align: center;
        }

        .header { margin-bottom: 40px; }
        .header h2 { margin: 0; color: #0A1F44; font-size: 28px; font-weight: 800; }
        .header p { color: #3C4A5A; font-size: 16px; margin-top: 10px; line-height: 1.5; }

        /* Input Kode Besar & Jelas */
        .input-code {
            width: 100%; 
            padding: 20px; /* Area ketik besar */
            border: 2px solid #ddd; border-radius: 12px;
            box-sizing: border-box; 
            font-family: monospace; font-size: 28px; /* Font kode besar */
            background: #F8F9FA; text-align: center; letter-spacing: 5px;
            text-transform: uppercase; color: #0A1F44; font-weight: 800;
            transition: 0.3s; margin-bottom: 25px;
        }
        .input-code:focus { 
            border-color: #1F75FE; background: white; outline: none; 
            box-shadow: 0 0 0 4px rgba(31, 117, 254, 0.1);
        }
        .input-code::placeholder { 
            letter-spacing: 0; font-family: 'Segoe UI', sans-serif; font-size: 15px; font-weight: normal; color: #ccc; 
        }

        .btn-submit {
            width: 100%; padding: 16px; /* Tombol tebal */
            background: #1F75FE; color: white;
            border: none; border-radius: 12px;
            font-weight: bold; font-size: 18px; cursor: pointer;
            transition: 0.2s; box-shadow: 0 4px 15px rgba(31, 117, 254, 0.2);
        }
        .btn-submit:hover { background: #0A1F44; transform: translateY(-2px); }

        .btn-back {
            display: block; margin-top: 30px;
            text-decoration: none; color: #666; font-weight: 600; font-size: 15px;
        }
        .btn-back:hover { color: #1F75FE; }

        /* --- KHUSUS MOBILE (HP) --- */
        @media (max-width: 600px) {
            .card-form {
                padding: 40px 25px; /* Sesuaikan padding di layar kecil */
            }
            .header h2 { font-size: 24px; }
            .header p { font-size: 14px; }
            
            /* Pastikan input tetap besar di HP */
            .input-code { font-size: 24px; padding: 18px; }
            .btn-submit { font-size: 16px; padding: 15px; }
        }
    </style>
</head>
<body>

    <div class="card-form">
        <div class="header">
            <h2>🔐 Gabung Kelas</h2>
            <p>Masukkan 5 digit kode unik yang diberikan oleh guru Anda untuk mengakses materi.</p>
        </div>

        <form method="POST">
            <input type="text" name="kode" class="input-code" placeholder="Kode Kelas" required maxlength="10" autocomplete="off">
            <button type="submit" name="gabung" class="btn-submit">Gabung Sekarang</button>
        </form>

        <a href="index.php" class="btn-back">Batal & Kembali ke Dashboard</a>
    </div>

</body>
</html>