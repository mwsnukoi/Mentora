<?php
session_start();
include 'koneksi.php';

if(!isset($_GET['id'])) {
    header("location:pengumuman.php");
    exit();
}

$id_berita = $_GET['id'];
$q = mysqli_query($conn, "SELECT * FROM pengumuman WHERE id='$id_berita'");
$d = mysqli_fetch_assoc($q);

if(!$d) {
    echo "Berita tidak ditemukan.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($d['judul']); ?> - Mentora</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #EAF3FF; margin: 0; }
        .container { max-width: 900px; margin: 40px auto; padding: 0 20px; min-height: 80vh; }
        
        /* TOMBOL KEMBALI */
        .btn-back {
            display: inline-flex; align-items: center; gap: 8px;
            text-decoration: none; color: #3C4A5A; font-weight: 700;
            font-size: 16px; margin-bottom: 20px; transition: 0.3s;
        }
        .btn-back span { font-size: 20px; transition: 0.3s; }
        .btn-back:hover { color: #1F75FE; }
        .btn-back:hover span { transform: translateX(-5px); }

        /* Detail Berita Container */
        .detail-box {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(10, 31, 68, 0.05);
            overflow: hidden;
            padding: 40px;
        }

        .news-image-full {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .news-meta {
            color: #1F75FE;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 13px;
            margin-bottom: 10px;
            display: block;
        }

        .news-title {
            color: #0A1F44;
            font-size: 36px;
            font-weight: 800;
            margin: 0 0 20px 0;
            line-height: 1.3;
        }

        .news-content {
            color: #333;
            font-size: 18px;
            line-height: 1.8;
            text-align: justify;
        }
        
        .divider {
            height: 1px; background: #eee; margin: 30px 0;
        }
        
        .admin-info {
            display: flex; align-items: center; gap: 10px; color: #666; font-size: 14px;
        }
        .admin-avatar {
            width: 40px; height: 40px; background: #0A1F44; color: white; 
            border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;
        }

    </style>
</head>
<body>

    <?php 
    if($_SESSION['role'] != 'admin') {
        include 'navbar.php'; 
    } else {
        // Header simpel untuk admin
        echo '<div style="padding:15px 30px; background:#0A1F44; color:white;"><b>Mentora Admin</b></div>';
    }
    ?>

    <div class="container">
        <!-- 1. TOMBOL KEMBALI (PANAH) -->
        <a href="pengumuman.php" class="btn-back"><span>&larr;</span> Kembali ke Daftar Berita</a>

        <div class="detail-box">
            <span class="news-meta">📅 <?= date('d F Y, H:i', strtotime($d['tanggal'])); ?> WIB</span>
            <h1 class="news-title"><?= htmlspecialchars($d['judul']); ?></h1>

            <!-- FOTO BESAR -->
            <?php if(!empty($d['foto']) && file_exists("uploads/".$d['foto'])) { ?>
                <img src="uploads/<?= $d['foto']; ?>" class="news-image-full" alt="Foto Berita">
            <?php } ?>

            <div class="news-content">
                <?= nl2br(htmlspecialchars($d['isi'])); ?>
            </div>

            <div class="divider"></div>

            <div class="admin-info">
                <div class="admin-avatar">A</div>
                <div>
                    <b>Diposting oleh Administrator</b><br>
                    Pusat Informasi Mentora
                </div>
            </div>
        </div>
    </div>


</body>
</html>