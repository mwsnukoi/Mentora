<?php
session_start();
include 'koneksi.php';

// Cek login
if(!isset($_SESSION['status']) || $_SESSION['status'] == '') {
    header("location:index.php");
    exit();
}

$id_user = $_SESSION['id_user'];

// --- PROSES UPDATE PROFIL ---
if(isset($_POST['simpan_profil'])){
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $hp = mysqli_real_escape_string($conn, $_POST['hp']);
    
    // Update Foto jika ada
    if(!empty($_FILES['foto']['name'])){
        $foto_name = time()."_".$_FILES['foto']['name'];
        move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/".$foto_name);
        mysqli_query($conn, "UPDATE users SET foto='$foto_name' WHERE id='$id_user'");
    }

    // Update Data Diri (Username TIDAK diupdate)
    $query_update = "UPDATE users SET nama_lengkap='$nama', email='$email', no_hp='$hp'";
    
    // Update Password jika diisi
    if(!empty($_POST['password'])){
        $pass = mysqli_real_escape_string($conn, $_POST['password']);
        $query_update .= ", password='$pass'";
    }
    
    $query_update .= " WHERE id='$id_user'";
    
    if(mysqli_query($conn, $query_update)){
        // Update session nama agar di navbar langsung berubah
        $_SESSION['nama'] = $nama; 
        echo "<script>alert('Profil berhasil diperbarui!'); window.location='profil.php';</script>";
    } else {
        echo "<script>alert('Gagal update data.');</script>";
    }
}

// Ambil data user terbaru
$q = mysqli_query($conn, "SELECT * FROM users WHERE id='$id_user'");
$d = mysqli_fetch_assoc($q);

// Cek Foto
$img_src = (!empty($d['foto']) && file_exists("uploads/".$d['foto'])) ? "uploads/".$d['foto'] : "https://via.placeholder.com/150";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profil Saya - Mentora</title>
    <style>
        /* Skema Warna Mentora */
        body { font-family: 'Segoe UI', sans-serif; background: #EAF3FF; margin: 0; }
        
        .container { max-width: 500px; margin: 40px auto; padding: 0 20px; padding-bottom: 60px; }
        
        /* TOMBOL KEMBALI */
        .btn-back {
            display: inline-flex; align-items: center; gap: 8px;
            text-decoration: none; color: #3C4A5A; font-weight: 700;
            font-size: 14px; margin-bottom: 20px; transition: 0.3s;
        }
        .btn-back:hover { color: #1F75FE; transform: translateX(-5px); }

        /* Card Profil */
        .profile-card { 
            background: white; 
            border-radius: 20px; 
            padding: 40px 30px; 
            box-shadow: 0 10px 30px rgba(10, 31, 68, 0.08); 
            text-align: center;
            position: relative;
            border-top: 5px solid #0A1F44; /* Aksen Navy */
        }

        /* Header & Edit Button */
        .profile-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .page-title { color: #0A1F44; font-weight: 800; font-size: 20px; margin: 0; }
        
        /* Tombol Edit Utama (Pensil) */
        .btn-edit-mode {
            background: #EAF3FF; color: #1F75FE; border: none;
            width: 35px; height: 35px; border-radius: 50%;
            cursor: pointer; font-size: 16px; display: flex; align-items: center; justify-content: center;
            transition: 0.2s;
        }
        .btn-edit-mode:hover { background: #1F75FE; color: white; }

        /* Avatar Section */
        .avatar-wrapper { 
            position: relative; 
            width: 110px; 
            height: 110px; 
            margin: 0 auto 15px; 
        }
        .avatar-img { 
            width: 100%; 
            height: 100%; 
            border-radius: 50%; 
            object-fit: cover; 
            border: 4px solid #EAF3FF; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        /* Tombol Ganti Foto (Hidden by default) */
        .edit-foto-btn {
            position: absolute; bottom: 0; right: 0;
            background: #1F75FE; color: white;
            width: 32px; height: 32px; border-radius: 50%;
            display: none; /* Sembunyi dulu */
            align-items: center; justify-content: center;
            cursor: pointer; border: 2px solid white; font-size: 14px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        /* User Info */
        .user-name { font-size: 22px; font-weight: 800; color: #0A1F44; margin: 0; }
        .user-role { color: #3C4A5A; font-size: 13px; margin-top: 5px; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; margin-bottom: 30px;}

        /* Form Styles */
        .menu-list { text-align: left; }
        
        .input-group { margin-bottom: 15px; }
        .input-label { display: block; font-size: 12px; color: #888; margin-bottom: 5px; font-weight: 600; }
        
        .form-input { 
            width: 100%; padding: 12px; 
            border: 1px solid #eee; border-radius: 10px; 
            font-family: inherit; color: #333;
            box-sizing: border-box; background: #f9f9f9;
            transition: 0.3s;
        }
        /* Style saat Read Only */
        .form-input[readonly] { background: transparent; border: 1px solid transparent; padding-left: 0; font-weight: 600; color: #0A1F44; }
        /* Style saat Edit Mode */
        .form-input:not([readonly]) { background: #fff; border-color: #1F75FE; }
        
        /* Input Disabled (Username) */
        .form-input-disabled {
            background: #f1f3f5; color: #999; border: 1px solid #eee; padding-left: 12px !important; cursor: not-allowed;
        }

        /* Tombol Aksi (Normal Size) */
        .action-buttons {
            display: flex; justify-content: center; gap: 15px; margin-top: 30px;
        }

        .btn-normal {
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
            transition: 0.2s;
            min-width: 120px;
            text-align: center;
            border: none;
        }

        .btn-save { 
            background: #1F75FE; color: white; 
            display: none; /* Sembunyi Default */
            box-shadow: 0 4px 10px rgba(31, 117, 254, 0.3);
        }
        .btn-save:hover { background: #0A1F44; }

        .btn-logout { 
            background: #fff; color: #ff6b6b; 
            border: 1px solid #ff6b6b; 
        }
        .btn-logout:hover { background: #ff6b6b; color: white; }
        
        #file-input { display: none; }
        
        /* Section Title */
        .section-title {
            font-size: 14px; color: #1F75FE; font-weight: bold;
            margin-top: 20px; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;
        }
    </style>
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="container">
        <!-- TOMBOL KEMBALI -->
        <a href="index.php" class="btn-back"><span>&larr;</span> Kembali ke Dashboard</a>

        <div class="profile-card">
            
            <!-- Header Kecil dengan Tombol Edit -->
            <div class="profile-header">
                <div class="page-title">Profil Saya</div>
                <button type="button" class="btn-edit-mode" onclick="toggleEditMode()" title="Edit Data Diri">✏️</button>
            </div>

            <form method="POST" enctype="multipart/form-data" id="formProfil">
                
                <!-- 1. Foto Profil -->
                <div class="avatar-wrapper">
                    <img src="<?= $img_src; ?>" class="avatar-img" id="preview-img">
                    <!-- Tombol Kamera (Muncul saat Edit Mode) -->
                    <label for="file-input" class="edit-foto-btn" id="btn-ganti-foto" title="Ganti Foto">📷</label>
                    <input type="file" name="foto" id="file-input" onchange="previewFile()"> 
                </div>

                <!-- Judul Nama & Role -->
                <h1 class="user-name"><?= $d['nama_lengkap']; ?></h1>
                <div class="user-role"><?= strtoupper($d['role']); ?></div>

                <!-- 2. Form Input -->
                <div class="menu-list">
                    
                    <div class="section-title">Informasi Akun</div>

                    <div class="input-group">
                        <span class="input-label">Username (Tidak dapat diubah)</span>
                        <!-- Disabled agar tidak bisa diedit -->
                        <input type="text" value="<?= $d['username']; ?>" class="form-input form-input-disabled" disabled>
                    </div>

                    <div class="input-group">
                        <span class="input-label">Nama Lengkap</span>
                        <!-- Ini yang sekarang bisa diedit -->
                        <input type="text" name="nama_lengkap" value="<?= $d['nama_lengkap']; ?>" class="form-input editable" readonly>
                    </div>

                    <div class="input-group">
                        <span class="input-label">Email</span>
                        <input type="email" name="email" value="<?= $d['email']; ?>" class="form-input editable" readonly>
                    </div>

                    <div class="input-group">
                        <span class="input-label">No. Handphone</span>
                        <input type="text" name="hp" value="<?= $d['no_hp']; ?>" class="form-input editable" readonly>
                    </div>

                    <div class="section-title">Keamanan</div>

                    <div class="input-group">
                        <span class="input-label">Ganti Password (Opsional)</span>
                        <input type="password" name="password" class="form-input editable" placeholder="********" readonly>
                    </div>

                </div>


            </form>
        </div>
    </div>

    <script>
        // Preview foto saat upload
        function previewFile() {
            const preview = document.getElementById('preview-img');
            const file = document.getElementById('file-input').files[0];
            const reader = new FileReader();
            reader.addEventListener("load", function () { preview.src = reader.result; }, false);
            if (file) { reader.readAsDataURL(file); }
        }

        // Aktifkan mode edit saat pensil diklik
        function toggleEditMode() {
            var inputs = document.getElementsByClassName('editable');
            var btnSimpan = document.getElementById('btn-simpan');
            var btnFoto = document.getElementById('btn-ganti-foto');
            
            var isReadOnly = inputs[0].hasAttribute('readonly');

            if (isReadOnly) {
                // AKTIFKAN MODE EDIT
                for(var i=0; i<inputs.length; i++) {
                    inputs[i].removeAttribute('readonly');
                    if(inputs[i].type == 'password') inputs[i].placeholder = "Ketik password baru...";
                }
                btnSimpan.style.display = 'inline-block';
                btnFoto.style.display = 'flex';
                inputs[0].focus(); // Fokus ke Nama Lengkap
            } else {
                // BATALKAN EDIT
                if(confirm("Batalkan perubahan?")) {
                    location.reload();
                }
            }
        }
    </script>

</body>
</html>