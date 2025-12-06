<?php
session_start();
include 'koneksi.php';

// --- LOGIKA ADMIN: POSTING BERITA BARU ---
if(isset($_POST['posting']) && $_SESSION['role'] == 'admin'){
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi = mysqli_real_escape_string($conn, $_POST['isi']);
    $penulis = $_SESSION['id_user'];
    
    $foto_berita = "";
    if(!empty($_FILES['foto']['name'])){
        $nama_file = time() . "_" . $_FILES['foto']['name'];
        $tmp_file = $_FILES['foto']['tmp_name'];
        move_uploaded_file($tmp_file, "uploads/" . $nama_file);
        $foto_berita = $nama_file;
    }

    $query = "INSERT INTO pengumuman (judul, isi, tipe, foto, penulis_id) VALUES ('$judul', '$isi', 'umum', '$foto_berita', '$penulis')";
    
    if(mysqli_query($conn, $query)){
        echo "<script>alert('Berita berhasil dipublikasikan!'); window.location='pengumuman.php';</script>";
    }
}

// --- LOGIKA ADMIN: HAPUS BERITA ---
if(isset($_GET['hapus']) && $_SESSION['role'] == 'admin'){
    $id_hapus = $_GET['hapus'];
    
    $cek_foto = mysqli_query($conn, "SELECT foto FROM pengumuman WHERE id='$id_hapus'");
    $data_foto = mysqli_fetch_assoc($cek_foto);
    
    if(!empty($data_foto['foto']) && file_exists("uploads/".$data_foto['foto'])){
        unlink("uploads/".$data_foto['foto']);
    }

    mysqli_query($conn, "DELETE FROM pengumuman WHERE id='$id_hapus'");
    echo "<script>alert('Berita berhasil dihapus!'); window.location='pengumuman.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pengumuman - Mentora</title>
    <!-- META TAG RESPONSIVE -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <style>
        /* Tema Mentora */
        body { font-family: 'Segoe UI', sans-serif; background: #EAF3FF; margin: 0; }
        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; min-height: 60vh; }
        
        /* TOMBOL KEMBALI */
        .btn-back {
            display: inline-flex; align-items: center; gap: 8px;
            text-decoration: none; color: #3C4A5A; font-weight: 700;
            font-size: 16px; margin-bottom: 20px; transition: 0.3s;
        }
        .btn-back:hover { color: #1F75FE; transform: translateX(-5px); }

        /* Form Admin */
        .admin-box { 
            background: white; padding: 30px; border-radius: 16px; 
            box-shadow: 0 4px 15px rgba(10, 31, 68, 0.05); 
            margin-bottom: 50px; border-top: 5px solid #0A1F44; 
        }
        .admin-box h3 { margin-top: 0; color: #0A1F44; font-weight: 800; }
        
        .form-label { display: block; font-weight: 700; color: #3C4A5A; margin-bottom: 8px; font-size: 14px; }
        input[type="text"], textarea { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 8px; font-family: inherit; box-sizing: border-box; }
        input:focus, textarea:focus { border-color: #1F75FE; outline: none; }

        .btn-post { background: #1F75FE; color: white; border: none; padding: 12px 25px; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.2s; }
        .btn-post:hover { background: #0A1F44; }
        
        /* Grid & Card */
        .news-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px; }
        .news-card { 
            background: white; border-radius: 16px; overflow: hidden; 
            box-shadow: 0 4px 15px rgba(10, 31, 68, 0.05); border: 1px solid #fff; 
            position: relative; transition: 0.3s; display: flex; flex-direction: column; height: 100%;
            text-decoration: none; 
        }
        .news-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(10, 31, 68, 0.1); }
        
        .news-img { width: 100%; height: 200px; object-fit: cover; display: block; border-bottom: 1px solid #eee; }
        .no-img { height: 200px; background: #f4f7f6; display: flex; align-items: center; justify-content: center; color: #ccc; font-size: 16px; font-weight: bold; text-transform: uppercase; }

        .news-body { padding: 25px; display: flex; flex-direction: column; flex-grow: 1; }
        .news-meta { color: #1F75FE; font-size: 12px; margin-bottom: 10px; display: block; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .news-title { margin: 0 0 10px 0; font-size: 20px; color: #0A1F44; font-weight: 800; line-height: 1.4; }
        .news-preview { color: #555; font-size: 14px; line-height: 1.6; margin-bottom: 20px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .read-more { margin-top: auto; color: #1F75FE; font-weight: bold; font-size: 14px; display: flex; align-items: center; gap: 5px; }
        
        .btn-delete { 
            position: absolute; top: 15px; right: 15px; background: white; color: #dc3545; 
            padding: 6px 10px; border-radius: 6px; font-size: 12px; font-weight: bold; 
            border: 1px solid #dc3545; z-index: 10; transition: 0.2s;
        }
        .btn-delete:hover { background: #dc3545; color: white; }
        
        .empty-state { text-align: center; color: #3C4A5A; padding: 50px; background: white; border-radius: 12px; grid-column: 1 / -1; }

        /* --- STYLE HEADER ADMIN --- */
        .admin-header-bar {
            padding: 15px 30px; 
            background: #0A1F44; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            color: white; 
            box-shadow: 0 4px 10px rgba(10, 31, 68, 0.2);
        }
        .admin-title { margin: 0; font-weight: 700; letter-spacing: 1px; font-size: 18px; }
        .admin-btn-back {
            text-decoration: none; font-weight: bold; color: white; 
            border: 1px solid white; padding: 6px 15px; 
            border-radius: 6px; font-size: 13px; transition: 0.2s;
        }
        .admin-btn-back:hover { background: white; color: #0A1F44; }

        /* --- KHUSUS MOBILE (PERBESAR UI ADMIN) --- */
        @media (max-width: 768px) {
            .admin-header-bar { padding: 20px; }
            .admin-title { font-size: 20px; } /* Judul lebih besar */
            
            /* Tombol Kembali lebih besar di HP */
            .admin-btn-back {
                padding: 10px 20px; 
                font-size: 14px;
                background: rgba(255,255,255,0.1);
            }
            
            /* Form input lebih nyaman di HP */
            input[type="text"], textarea, .btn-post {
                font-size: 16px;
                padding: 14px;
            }
            
            .news-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- Header Admin/Navbar -->
    <?php 
    if($_SESSION['role'] == 'admin') {
        // Menggunakan class baru .admin-header-bar
        echo '<div class="admin-header-bar">
                <h3 class="admin-title">📢 Kelola Pengumuman</h3>
                <a href="index.php" class="admin-btn-back">&laquo; Kembali</a>
              </div>';
    } else {
        include 'navbar.php'; 
    }
    ?>

    <div class="container">
        <!-- TOMBOL KEMBALI (Khusus User Biasa) -->
        <?php if($_SESSION['role'] != 'admin') { ?>
            <a href="index.php" class="btn-back"><span>&larr;</span> Kembali ke Dashboard</a>
        <?php } ?>
        
        <!-- FORM ADMIN -->
        <?php if($_SESSION['role'] == 'admin') { ?>
            <div class="admin-box">
                <h3>Tulis Berita Baru</h3>
                <form method="POST" enctype="multipart/form-data">
                    <label class="form-label">Judul Berita:</label>
                    <input type="text" name="judul" required placeholder="Masukkan judul yang menarik...">

                    <label class="form-label">Isi Berita:</label>
                    <textarea name="isi" rows="6" required placeholder="Tulis isi berita lengkap..."></textarea>

                    <label class="form-label">Foto (Opsional):</label>
                    <input type="file" name="foto" accept="image/*"><br><br>

                    <button type="submit" name="posting" class="btn-post">Terbitkan Berita</button>
                </form>
            </div>
        <?php } ?>

        <h2 style="border-left: 6px solid #1F75FE; padding-left: 15px; margin-bottom: 30px; color:#0A1F44; font-size:24px;">Berita & Info Kampus</h2>

        <div class="news-grid">
            <?php
            $q_berita = mysqli_query($conn, "SELECT * FROM pengumuman WHERE tipe='umum' ORDER BY id DESC");
            
            if(mysqli_num_rows($q_berita) == 0) {
                echo "<div class='empty-state'>Belum ada berita yang dipublikasikan.</div>";
            }

            while($row = mysqli_fetch_assoc($q_berita)){
                $foto = (!empty($row['foto']) && file_exists("uploads/".$row['foto'])) ? "uploads/".$row['foto'] : "";
            ?>
                <!-- WRAPPER CARD -->
                <div style="position: relative;">
                    <?php if($_SESSION['role'] == 'admin') { ?>
                        <!-- Tombol Hapus -->
                        <a href="pengumuman.php?hapus=<?= $row['id']; ?>" class="btn-delete" onclick="return confirm('Yakin hapus berita ini?');">Hapus</a>
                    <?php } ?>

                    <a href="detail_pengumuman.php?id=<?= $row['id']; ?>" class="news-card">
                        <?php if($foto != "") { ?>
                            <img src="<?= $foto; ?>" class="news-img" alt="Foto Berita">
                        <?php } else { ?>
                            <div class="no-img">NO IMAGE</div>
                        <?php } ?>

                        <div class="news-body">
                            <span class="news-meta">
                                <?= date('d M Y', strtotime($row['tanggal'])); ?>
                            </span>
                            <h3 class="news-title"><?= htmlspecialchars(substr($row['judul'], 0, 50)); ?><?= (strlen($row['judul'])>50)?'...':''; ?></h3>
                            <div class="news-preview">
                                <!-- Ambil 100 karakter pertama untuk preview -->
                                <?= htmlspecialchars(substr(strip_tags($row['isi']), 0, 100)); ?>...
                            </div>
                            <div class="read-more">Baca Selengkapnya <span>&rarr;</span></div>
                        </div>
                    </a>
                </div>
            <?php } ?>
        </div>

    </div>
    
    
</body>
</html>