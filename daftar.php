<?php
include 'koneksi.php';

if (isset($_POST['register'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = $_POST['role']; 

    // Cek username kembar
    $cek_user = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    
    if (mysqli_num_rows($cek_user) > 0) {
        $error = "Username sudah terpakai, silakan pilih yang lain.";
    } else {
        $query = "INSERT INTO users (username, password, nama_lengkap, role) VALUES ('$username', '$password', '$nama', '$role')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Pendaftaran Berhasil! Silakan Login.'); window.location='login.php';</script>";
        } else {
            $error = "Terjadi kesalahan sistem.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Akun - Mentora</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    
    <style>
        /* --- Tema Global Mentora --- */
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: #EAF3FF; 
            margin: 0; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            padding: 20px; 
            box-sizing: border-box; /* Agar padding tidak menambah lebar total */
        }
        
        .register-card {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(10, 31, 68, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            border-top: 6px solid #1F75FE; 
        }

        .brand-title {
            font-size: 26px;
            font-weight: 800;
            color: #0A1F44;
            margin-bottom: 5px;
        }
        .brand-subtitle {
            color: #3C4A5A;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .form-group { margin-bottom: 18px; text-align: left; }
        
        label { 
            display: block; 
            font-weight: 600; 
            color: #333; 
            margin-bottom: 8px; 
            font-size: 14px; 
        }
        
        input[type="text"], input[type="password"], select {
            width: 100%;
            padding: 12px;
            height: 48px; /* Tinggi input diperjelas */
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 15px;
            background: #F8F9FA;
            font-family: inherit;
            transition: all 0.3s;
        }

        input:focus, select:focus {
            border-color: #1F75FE;
            background: white;
            outline: none;
            box-shadow: 0 0 0 3px rgba(31, 117, 254, 0.1); /* Efek glow saat diklik */
        }

        .btn-register {
            width: 100%;
            padding: 14px;
            height: 50px;
            background: #0A1F44; 
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 15px;
        }
        .btn-register:hover {
            background: #1F75FE;
            transform: translateY(-2px);
        }

        .error-msg {
            background: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            border: 1px solid #ffcdd2;
            text-align: left;
        }

        .footer-link {
            margin-top: 25px;
            font-size: 14px;
            color: #666;
        }
        .footer-link a {
            color: #1F75FE;
            text-decoration: none;
            font-weight: bold;
        }
        .footer-link a:hover { text-decoration: underline; }

        /* =========================================
           SETTINGAN KHUSUS HP (MOBILE)
           ========================================= */
        @media screen and (max-width: 768px) {
            body {
                align-items: flex-start; /* Agar di HP tidak terlalu tengah jika keyboard muncul */
                padding: 15px; /* Mengurangi padding body */
            }

            .register-card {
                margin-top: 20px; /* Jarak dari atas */
                padding: 30px 20px; /* Padding dalam kartu */
                width: 100%; /* Penuhi lebar */
                box-shadow: 0 5px 20px rgba(0,0,0,0.05); /* Shadow lebih ringan */
            }

            .brand-title {
                font-size: 28px; /* Judul lebih besar */
            }

            .brand-subtitle {
                font-size: 15px; /* Subjudul sedikit lebih besar */
            }

            /* Inputan dibuat lebih besar & ramah jari */
            input[type="text"], 
            input[type="password"], 
            select {
                font-size: 16px; /* Mencegah zoom otomatis di iPhone */
                height: 55px;    /* Area sentuh lebih luas */
                padding: 0 15px;
            }

            label {
                font-size: 15px; /* Label lebih terbaca */
                margin-bottom: 10px;
            }

            /* Tombol lebih gagah */
            .btn-register {
                height: 60px; /* Tombol tinggi */
                font-size: 18px; /* Teks tombol besar */
                border-radius: 10px;
            }

            .footer-link {
                font-size: 15px;
                padding: 10px; /* Area klik link footer diperluas */
            }
        }
    </style>
</head>
<body>

    <div class="register-card">
        <div class="brand-title">Buat Akun Baru</div>
        <div class="brand-subtitle">Bergabunglah dengan komunitas Mentora</div>

        <?php if(isset($error)) { echo "<div class='error-msg'>$error</div>"; } ?>

        <form method="POST">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" placeholder="Contoh: Budi Santoso" required>
            </div>
            
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Buat username unik" required>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Buat password" required>
            </div>
            
            <div class="form-group">
                <label>Daftar Sebagai</label>
                <select name="role" required>
                    <option value="siswa">Siswa / Mahasiswa</option>
                    <option value="guru">Guru / Pengajar</option>
                </select>
            </div>

            <button type="submit" name="register" class="btn-register">Daftar Sekarang</button>
        </form>

        <div class="footer-link">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </div>
    </div>

</body>
</html>