<?php
session_start();
include 'koneksi.php';

// Jika sudah login, langsung arahkan ke dashboard
if(isset($_SESSION['status']) && $_SESSION['status'] == "login"){
    header("location:index.php");
    exit();
}

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    $cek = mysqli_num_rows($query);

    if ($cek > 0) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['username'] = $username;
        $_SESSION['nama'] = $data['nama_lengkap'];
        $_SESSION['role'] = $data['role'];
        $_SESSION['id_user'] = $data['id'];
        $_SESSION['status'] = "login";

        header("location:index.php");
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Masuk - Mentora</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* TEMA MENTORA */
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: #EAF3FF; /* Soft Blue Background */
            margin: 0; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            padding: 20px; 
            box-sizing: border-box;
        }
        
        .login-card {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(10, 31, 68, 0.1);
            width: 100%;
            max-width: 400px; /* Diperlebar sedikit dari 380px */
            text-align: center;
            border-top: 6px solid #0A1F44; 
        }

        .brand-title {
            font-size: 28px;
            font-weight: 800;
            color: #0A1F44;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }
        
        .brand-subtitle {
            color: #3C4A5A;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .form-group { margin-bottom: 20px; text-align: left; }
        label { display: block; font-weight: 700; color: #3C4A5A; margin-bottom: 8px; font-size: 14px; }
        
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 14px 15px; /* Padding diperbesar agar nyaman di HP */
            border: 1px solid #ddd;
            border-radius: 10px; /* Radius diperhalus */
            box-sizing: border-box;
            font-size: 15px; /* Font diperbesar */
            transition: 0.2s;
            background: #F8F9FA;
            font-family: inherit;
        }
        
        input:focus {
            border-color: #1F75FE;
            background: white;
            outline: none;
            box-shadow: 0 0 0 3px rgba(31, 117, 254, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 14px; /* Tombol lebih besar */
            background: #1F75FE; 
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(31, 117, 254, 0.3);
        }
        
        .btn-login:hover {
            background: #0A1F44;
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
            font-weight: 600;
        }

        .footer-link {
            margin-top: 25px;
            font-size: 14px;
            color: #666;
        }
        .footer-link a {
            color: #1F75FE;
            text-decoration: none;
            font-weight: 700;
        }
        .footer-link a:hover { text-decoration: underline; }

        /* --- KHUSUS MOBILE --- */
        @media (max-width: 480px) {
            body {
                padding: 15px; /* Kurangi padding body di HP */
            }
            .login-card {
                padding: 30px 25px; /* Sesuaikan padding kartu */
                box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            }
            .brand-title { font-size: 26px; }
            input[type="text"], input[type="password"] { font-size: 16px; } /* Font input lebih besar di HP */
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="brand-title">Mentora<span style="color:#1F75FE;">.</span></div>
        <div class="brand-subtitle">Masuk untuk melanjutkan pembelajaran</div>

        <?php if(isset($error)) { echo "<div class='error-msg'>⚠️ $error</div>"; } ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Masukkan username" required autocomplete="off">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Masukkan password" required>
            </div>
            
            <button type="submit" name="login" class="btn-login">Masuk Sekarang</button>
        </form>

        <div class="footer-link">
            Belum punya akun? <a href="daftar.php">Daftar di sini</a>
        </div>
    </div>

</body>
</html>