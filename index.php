<?php 
session_start();
include 'koneksi.php';

// Cek apakah user sudah login
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:login.php");
    exit();
}

// Simpan ID User ke dalam session
$u = $_SESSION['username'];
$q = mysqli_query($conn, "SELECT id FROM users WHERE username='$u'");
$d = mysqli_fetch_assoc($q);
$_SESSION['id_user'] = $d['id'];
$id_user = $d['id'];

// --- LOGIKA HAPUS KELAS (KHUSUS GURU) ---
if(isset($_GET['hapus_kelas']) && $_SESSION['role'] == 'guru'){
    $id_hapus = $_GET['hapus_kelas'];
    
    // Validasi kepemilikan kelas
    $cek_milik = mysqli_query($conn, "SELECT * FROM kelas WHERE id='$id_hapus' AND guru_id='$id_user'");
    
    if(mysqli_num_rows($cek_milik) > 0){
        // 1. Hapus file fisik Materi
        $q_file = mysqli_query($conn, "SELECT file_content FROM materi WHERE kelas_id='$id_hapus'");
        while($f = mysqli_fetch_assoc($q_file)){
            if(!empty($f['file_content']) && file_exists("uploads/".$f['file_content'])){
                unlink("uploads/".$f['file_content']);
            }
        }

        // 2. Hapus data di database (Materi, Siswa, Quiz, Soal, Nilai)
        mysqli_query($conn, "DELETE FROM materi WHERE kelas_id='$id_hapus'");
        mysqli_query($conn, "DELETE FROM kelas_siswa WHERE kelas_id='$id_hapus'");
        
        $q_quiz = mysqli_query($conn, "SELECT id FROM quiz WHERE kelas_id='$id_hapus'");
        while($qz = mysqli_fetch_assoc($q_quiz)){
            mysqli_query($conn, "DELETE FROM soal WHERE quiz_id='{$qz['id']}'");
            mysqli_query($conn, "DELETE FROM nilai_quiz WHERE quiz_id='{$qz['id']}'");
        }
        mysqli_query($conn, "DELETE FROM quiz WHERE kelas_id='$id_hapus'");
        mysqli_query($conn, "DELETE FROM kelas WHERE id='$id_hapus'");
        
        echo "<script>alert('Kelas berhasil dihapus.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal! Anda tidak berhak menghapus kelas ini.'); window.location='index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Mentora</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Tema Mentora */
        body { font-family: 'Segoe UI', sans-serif; background: #EAF3FF; margin: 0; } 
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; min-height: 60vh; }
        
        /* --- HERO BANNER --- */
        .hero-banner {
            background: #FFFFFF; border-radius: 20px; padding: 40px 60px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 15px 40px rgba(10, 31, 68, 0.05);
            margin-bottom: 60px; position: relative; overflow: hidden;
        }
        .hero-content { z-index: 1; max-width: 55%; }
        .hero-tag { background: #EAF3FF; color: #1F75FE; padding: 6px 15px; border-radius: 30px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; display: inline-block; margin-bottom: 15px; }
        .hero-title { font-size: 38px; font-weight: 800; color: #0A1F44; margin: 0 0 15px 0; line-height: 1.2; }
        .hero-desc { color: #3C4A5A; font-size: 16px; line-height: 1.7; margin-bottom: 30px; }
        .btn-hero { background: #1F75FE; color: white; padding: 14px 35px; border-radius: 10px; text-decoration: none; font-weight: bold; box-shadow: 0 4px 15px rgba(31, 117, 254, 0.3); transition: 0.3s; display: inline-block; }
        .btn-hero:hover { transform: translateY(-3px); background: #0A1F44; }
        
        .hero-image img { 
            max-width: 380px; height: auto; 
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-15px); } }

        /* --- SECTION HEADER --- */
        .section-header { text-align: center; margin-bottom: 40px; }
        .section-header h2 { font-size: 28px; font-weight: 800; color: #0A1F44; margin: 0; }
        .section-header p { color: #3C4A5A; margin-top: 10px; font-size: 16px; }
        
        .btn-add-class { 
            background: #1F75FE; color: white; padding: 10px 25px; 
            text-decoration: none; border-radius: 30px; font-weight: bold; 
            display: inline-block; margin-top: 15px; transition: 0.3s; 
        }
        .btn-add-class:hover { background: #0A1F44; transform: translateY(-2px); }

        /* --- CARD STYLE (Grid Responsif) --- */
        .grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
            gap: 30px; 
        }
        
        .card { 
            background: white; border-radius: 16px; padding: 40px 30px; 
            text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.04); 
            transition: 0.3s; position: relative; border: 1px solid white;
            display: flex; flex-direction: column; align-items: center;
        }
        .card:hover { 
            transform: translateY(-10px); 
            box-shadow: 0 20px 40px rgba(31, 117, 254, 0.15); 
            border-color: #EAF3FF; 
        }

        .card-icon {
            width: 70px; height: 70px; background: #EAF3FF; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 25px; color: #1F75FE; font-size: 30px;
            box-shadow: 0 0 0 8px rgba(234, 243, 255, 0.5); transition: 0.3s;
        }
        .card:hover .card-icon { background: #1F75FE; color: white; box-shadow: 0 0 0 8px rgba(31, 117, 254, 0.1); }

        .card-title { font-size: 20px; font-weight: 700; color: #0A1F44; margin: 0 0 15px 0; }
        .card-desc { color: #3C4A5A; font-size: 14px; line-height: 1.6; margin-bottom: 25px; flex-grow: 1; }
        
        .card-meta { 
            font-size: 12px; color: #888; background: #f9f9f9; 
            padding: 5px 10px; border-radius: 20px; margin-bottom: 20px; 
            display: inline-block; width: fit-content; 
        }

        .btn-read-more { 
            background: #1F75FE; color: white; padding: 12px 30px; 
            border-radius: 8px; text-decoration: none; font-weight: bold; 
            font-size: 14px; transition: 0.2s; width: 100%; display: block; 
            box-sizing: border-box; 
        }
        .btn-read-more:hover { background: #0A1F44; }

        /* Tombol Hapus Kelas (Pojok Kanan Atas) */
        .btn-delete-corner { 
            position: absolute; top: 15px; right: 15px; 
            color: #ccc; font-size: 18px; text-decoration: none; 
            width: 30px; height: 30px; line-height: 30px; 
            border-radius: 50%; transition: 0.2s; 
        }
        .btn-delete-corner:hover { background: #ffebee; color: #dc3545; }

        /* --- ADMIN PANEL --- */
        .admin-header { 
            background: #0A1F44; padding: 15px 30px; color: white; 
            display: flex; justify-content: space-between; align-items: center; 
        }
        
        .admin-grid { 
            display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: 30px; margin-top: 40px; 
        }
        
        .admin-card { 
            background: white; padding: 40px; border-radius: 12px; 
            text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
            border-top: 5px solid #3C4A5A; transition: 0.3s; 
        }
        .admin-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        
        .admin-link { text-decoration: none; color: #0A1F44; font-weight: 800; font-size: 18px; display: block; margin-top: 10px; }
        .admin-link:hover { color: #1F75FE; }
        
        .empty-state { grid-column: 1/-1; text-align: center; padding: 60px; color: #888; }

        /* RESPONSIVE MOBILE */
        @media (max-width: 768px) {
            .hero-banner { flex-direction: column-reverse; text-align: center; padding: 30px; }
            .hero-content { max-width: 100%; }
            .hero-image img { max-width: 80%; margin-bottom: 20px; }
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- LOGIKA HEADER: Admin pakai Header Khusus, Siswa/Guru pakai Navbar -->
    <?php 
    if($_SESSION['role'] == 'admin') { 
        echo '<div class="admin-header">
                <h3 style="margin:0; font-weight:800; letter-spacing:1px;">Mentora Admin</h3>
                <a href="logout.php" style="color:#ff6b6b; text-decoration:none; font-weight:bold; border:1px solid #ff6b6b; padding:5px 15px; border-radius:6px;">Logout</a>
              </div>';
    } else { 
        include 'navbar.php'; 
    } 
    ?>

    <div class="container">
        
        <!-- TAMPILAN KHUSUS ADMIN -->
        <?php if($_SESSION['role'] == 'admin') { ?>
            
            <div class="section-header">
                <h2>Panel Administrator</h2>
                <p>Kelola sistem akademik Mentora dengan mudah.</p>
            </div>
            
            <div class="admin-grid">
                <div class="admin-card" style="border-top-color: #1F75FE;">
                    <span style="font-size:40px; display:block; margin-bottom:15px;">📅</span>
                    <a href="jadwal.php" class="admin-link">Kelola Jadwal &raquo;</a>
                    <p style="color:#3C4A5A; margin-top:10px; font-size:14px;">Atur jadwal pelajaran untuk seluruh kelas.</p>
                </div>
                <div class="admin-card" style="border-top-color: #0A1F44;">
                    <span style="font-size:40px; display:block; margin-bottom:15px;">📢</span>
                    <a href="pengumuman.php" class="admin-link">Kelola Pengumuman &raquo;</a>
                    <p style="color:#3C4A5A; margin-top:10px; font-size:14px;">Buat berita seputar kegiatan kampus.</p>
                </div>
            </div>

        <!-- TAMPILAN GURU & SISWA -->
        <?php } else { ?>
            
            <!-- HERO BANNER -->
            <div class="hero-banner">
                <div class="hero-content">
                    <span class="hero-tag">#MentoraLearning</span>
                    <h1 class="hero-title">Eksplorasi Ilmu Bersama Mentora</h1>
                    <p class="hero-desc">Platform pembelajaran modern yang didesain untuk membantu Anda mencapai potensi terbaik.</p>
                    
                    <?php if($_SESSION['role'] == 'guru') { ?>
                        <a href="buat_kelas.php" class="btn-hero">+ Buat Kelas Baru</a>
                    <?php } else { ?>
                        <a href="gabung_kelas.php" class="btn-hero">Gabung Kelas Sekarang</a>
                    <?php } ?>
                </div>
                <div class="hero-image">
                    <!-- Gambar Ilustrasi (Bisa diganti link-nya) -->
                    <img src="uploads/home.png" alt="Belajar Online">
                </div>
            </div>

            <!-- DAFTAR KELAS -->
            <div class="section-header">
                <h2>Kelas yang Tersedia</h2>
                <p>Pilih kelas di bawah untuk mulai belajar.</p>
                
                <?php if($_SESSION['role'] == 'guru') { ?>
                    <a href="buat_kelas.php" class="btn-add-class">+ Tambah Kelas</a>
                <?php } elseif($_SESSION['role'] == 'siswa') { ?>
                    <a href="gabung_kelas.php" class="btn-add-class">Gabung Kelas Lain</a>
                <?php } ?>
            </div>

            <div class="grid">
                <?php
                // Query mengambil kelas sesuai role
                if($_SESSION['role'] == 'guru'){
                    $query = mysqli_query($conn, "SELECT * FROM kelas WHERE guru_id='$id_user'");
                } else {
                    $query = mysqli_query($conn, "SELECT k.* FROM kelas k JOIN kelas_siswa ks ON k.id = ks.kelas_id WHERE ks.siswa_id='$id_user'");
                }

                if(mysqli_num_rows($query) == 0) {
                    echo "<div class='empty-state'><h3>Belum ada kelas.</h3><p>Silakan buat atau gabung kelas baru.</p></div>";
                }

                while($row = mysqli_fetch_assoc($query)){
                    // Ikon Random untuk variasi visual
                    $icons = ['📖', '💻', '📊', '🧪', '📐', '🎨'];
                    $random_icon = $icons[array_rand($icons)];
                ?>
                    <div class="card">
                        <!-- Tombol Hapus (Pojok Kanan Atas) - Hanya Guru -->
                        <?php if($_SESSION['role'] == 'guru'){ ?>
                            <a href="index.php?hapus_kelas=<?= $row['id']; ?>" class="btn-delete-corner" onclick="return confirm('Yakin hapus kelas ini selamanya?');" title="Hapus Kelas">✖</a>
                        <?php } ?>

                        <!-- Ikon Bulat -->
                        <div class="card-icon">
                            <?= $random_icon; ?>
                        </div>

                        <!-- Judul -->
                        <h3 class="card-title"><?= htmlspecialchars($row['nama_kelas']); ?></h3>

                        <!-- Deskripsi -->
                        <p class="card-desc">
                            <?= substr(htmlspecialchars($row['deskripsi']), 0, 80); ?>...
                        </p>

                        <!-- Info Jadwal (Optional) -->
                        <?php if(!empty($row['hari'])){ ?>
                            <div class="card-meta">
                                📅 <?= $row['hari']; ?> • <?= substr($row['jam_mulai'],0,5); ?>
                            </div>
                        <?php } ?>

                        <!-- Tombol Masuk -->
                        <a href="lihat_kelas.php?id=<?= $row['id']; ?>" class="btn-read-more">MASUK KELAS &rarr;</a>
                    </div>
                <?php } ?>
            </div>

        <?php } ?>
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>