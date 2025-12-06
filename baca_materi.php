<?php
session_start();
include 'koneksi.php';

// Cek Login
if(!isset($_SESSION['status'])) { header("location:login.php"); exit(); }

$id_materi = $_GET['id'];
$id_user = $_SESSION['id_user'];

// Ambil data materi dari database
$q_materi = mysqli_query($conn, "SELECT * FROM materi WHERE id='$id_materi'");
$d = mysqli_fetch_assoc($q_materi);

// Ambil nama kelas untuk navigasi
$q_kelas = mysqli_query($conn, "SELECT nama_kelas FROM kelas WHERE id='{$d['kelas_id']}'");
$d_kelas = mysqli_fetch_assoc($q_kelas);

// Proses Kirim Komentar
if(isset($_POST['kirim_komentar'])){
    $isi = mysqli_real_escape_string($conn, $_POST['komentar']);
    if(!empty($isi)){
        mysqli_query($conn, "INSERT INTO komentar (materi_id, user_id, isi_komentar) VALUES ('$id_materi', '$id_user', '$isi')");
        header("Refresh:0"); // Refresh halaman agar komentar muncul
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($d['judul']); ?> - Mentora</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* TEMA UTAMA MENTORA */
        body { font-family: 'Segoe UI', sans-serif; background: #EAF3FF; margin: 0; color: #333; }
        .container { max-width: 1000px; margin: 30px auto; padding: 0 20px; display: flex; flex-direction: column; gap: 25px; min-height: 80vh; }
        
        /* --- 1. TOMBOL KEMBALI (BACK ARROW) --- */
        .btn-back {
            display: inline-flex; align-items: center; gap: 8px;
            text-decoration: none; color: #3C4A5A; font-weight: 700;
            font-size: 16px; margin-bottom: 5px; transition: 0.3s;
            width: fit-content;
        }
        .btn-back span { font-size: 20px; transition: 0.3s; }
        .btn-back:hover { color: #1F75FE; }
        .btn-back:hover span { transform: translateX(-5px); } 

        /* --- 2. HEADER MATERI --- */
        .materi-header { 
            background: white; padding: 35px; border-radius: 16px; 
            box-shadow: 0 4px 20px rgba(10,31,68,0.05); 
            border-left: 6px solid #0A1F44; /* Aksen Navy */
        }
        .materi-header h1 { margin: 0 0 15px 0; color: #0A1F44; font-size: 32px; font-weight: 800; line-height: 1.3; }
        
        .meta { 
            color: #777; font-size: 14px; display: flex; gap: 20px; flex-wrap: wrap;
            border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 15px;
        }
        .meta span { display: flex; align-items: center; gap: 5px; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; white-space: nowrap; }
        
        .materi-desc { color: #444; line-height: 1.7; font-size: 16px; }

        /* --- 3. VIEWER KONTEN (Otomatis sesuai tipe) --- */
        .content-viewer { 
            background: #000; /* Background gelap agar fokus */
            border-radius: 16px; overflow: hidden; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.2); 
            width: 100%;
            /* Aspect Ratio 16:9 untuk video responsive */
            aspect-ratio: 16/9; 
            display: flex; align-items: center; justify-content: center;
        }
        
        /* Tampilan Khusus Teks (Artikel) */
        .text-viewer {
            background: white; padding: 50px; border-radius: 16px;
            line-height: 1.9; font-size: 17px; color: #2c3e50;
            box-shadow: 0 4px 20px rgba(10,31,68,0.05);
            min-height: auto; display: block;
        }

        /* Tampilan Khusus File Download (PPT/DOC) */
        .file-card {
            background: white; padding: 50px; border-radius: 16px;
            text-align: center; border: 2px dashed #1F75FE;
            background-color: #f8fbff;
        }
        .file-text-icon { font-size: 24px; font-weight: bold; color: #1F75FE; margin-bottom: 20px; display: block; letter-spacing: 2px; }
        .file-icon { font-size: 60px; margin-bottom: 15px; display: block; }
        
        .btn-download {
            background: #1F75FE; color: white; padding: 14px 35px;
            border-radius: 30px; text-decoration: none; font-weight: bold;
            display: inline-block; transition: 0.3s; box-shadow: 0 4px 15px rgba(31, 117, 254, 0.3);
        }
        .btn-download:hover { background: #0A1F44; transform: translateY(-3px); }

        /* Player Video & PDF */
        iframe, video, embed { width: 100%; height: 100%; border: none; display: block; }
        
        /* Khusus PDF agar tetap tinggi */
        .pdf-viewer { height: 80vh; min-height: 500px; }

        /* --- 4. AREA DISKUSI --- */
        .discussion-area { margin-top: 30px; display: flex; gap: 30px; align-items: flex-start; }
        
        /* Form Komentar */
        .comment-form { flex: 1; background: white; padding: 25px; border-radius: 16px; box-shadow: 0 4px 20px rgba(10,31,68,0.05); position: sticky; top: 90px; }
        .comment-form h3 { margin-top: 0; color: #0A1F44; font-size: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px; font-weight: 700; }
        
        textarea { 
            width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 10px; 
            height: 100px; font-family: inherit; box-sizing: border-box; resize: vertical; font-size: 14px; 
        }
        textarea:focus { border-color: #1F75FE; outline: none; }
        
        .btn-kirim { 
            background: #0A1F44; color: white; border: none; padding: 12px 25px; 
            border-radius: 8px; cursor: pointer; font-weight: bold; margin-top: 15px; 
            float: right; transition: 0.2s; width: 100%;
        }
        .btn-kirim:hover { background: #1F75FE; }

        /* List Komentar */
        .comment-list { flex: 2; display: flex; flex-direction: column; gap: 20px; width: 100%; }
        
        .comment-item { 
            background: white; padding: 20px; border-radius: 16px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.03); 
            display: flex; gap: 20px; border-left: 4px solid transparent; 
            transition: 0.2s; 
        }
        
        .comment-avatar { 
            width: 45px; height: 45px; background: #EAF3FF; color: #1F75FE; 
            border-radius: 50%; display: flex; align-items: center; justify-content: center; 
            font-weight: bold; font-size: 20px; flex-shrink: 0; 
        }
        
        .comment-content { width: 100%; }
        .comment-content h4 { margin: 0; font-size: 15px; color: #0A1F44; font-weight: 700; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 5px;}
        .comment-content p { margin: 8px 0 0; font-size: 15px; color: #555; line-height: 1.5; word-break: break-word; }
        .comment-date { font-size: 11px; color: #999; font-weight: normal; }
        
        .badge-guru { background: #1F75FE; color: white; font-size: 10px; padding: 3px 8px; border-radius: 4px; margin-left: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .style-guru { border-left-color: #1F75FE; background: #f4f9ff; } 

        /* --- RESPONSIVE MOBILE (HP) --- */
        @media (max-width: 768px) {
            .container { padding: 15px; margin-top: 20px; }
            
            /* Header Lebih Compact */
            .materi-header { padding: 25px; }
            .materi-header h1 { font-size: 22px; }
            .meta { flex-direction: column; gap: 8px; align-items: flex-start; } /* Info turun ke bawah */
            
            /* Video & Content */
            .content-viewer { aspect-ratio: 16/9; height: auto; min-height: auto; }
            .pdf-viewer { height: 400px; } /* PDF tidak terlalu tinggi di HP */
            .text-viewer { padding: 25px; font-size: 15px; }
            .file-card { padding: 30px 20px; }

            /* Diskusi Stack ke Bawah */
            .discussion-area { flex-direction: column; gap: 30px; }
            .comment-form { position: static; order: 1; } /* Form pindah ke bawah list atau tetap di atas */
            .comment-list { order: 2; }
            
            .comment-item { padding: 15px; gap: 15px; }
            .comment-avatar { width: 35px; height: 35px; font-size: 16px; }
            .comment-content h4 { font-size: 14px; flex-direction: column; align-items: flex-start; }
            .badge-guru { margin-left: 0; margin-top: 2px; display: inline-block;}
        }
    </style>
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="container">
        
        <!-- 1. TOMBOL KEMBALI -->
        <a href="lihat_kelas.php?id=<?= $d['kelas_id']; ?>" class="btn-back">
            <span>&larr;</span> Kembali ke Kelas
        </a>

        <!-- 2. HEADER JUDUL MATERI -->
        <div class="materi-header">
            <h1><?= htmlspecialchars($d['judul']); ?></h1>
            <div class="meta">
                <span>📅 <?= date('d M Y', strtotime($d['tanggal'])); ?></span>
                <span>📂 Bab: <?= htmlspecialchars($d['bab']); ?></span>
                <span>👁️ Tipe: <b><?= strtoupper($d['tipe']); ?></b></span>
            </div>
            <?php if(!empty($d['deskripsi'])) { ?>
                <div class="materi-desc"><?= nl2br(htmlspecialchars($d['deskripsi'])); ?></div>
            <?php } ?>
        </div>

        <!-- 3. VIEWER KONTEN -->
        <?php 
        $file_path = "uploads/" . $d['file_content'];
        
        // A. PDF
        if($d['tipe'] == 'pdf'){ 
        ?>
            <div class="content-viewer pdf-viewer" style="background: #333;">
                <embed src="<?= $file_path; ?>" type="application/pdf" width="100%" height="100%" />
            </div>

        <?php 
        // B. Video Upload
        } elseif($d['tipe'] == 'video'){ 
        ?>
            <div class="content-viewer">
                <video controls width="100%" height="100%">
                    <source src="<?= $file_path; ?>" type="video/mp4">
                    Browser Anda tidak mendukung pemutar video.
                </video>
            </div>

        <?php 
        // C. YouTube
        } elseif($d['tipe'] == 'youtube'){ 
            $url = $d['file_content'];
            $url = str_replace(["watch?v=", "youtu.be/"], ["embed/", "youtube.com/embed/"], $url);
        ?>
            <div class="content-viewer">
                <iframe src="<?= $url; ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>

        <?php 
        // D. Teks
        } elseif($d['tipe'] == 'teks'){ 
        ?>
            <div class="text-viewer">
                <?= nl2br(htmlspecialchars($d['file_content'])); ?>
            </div>

        <?php 
        // E. File Download
        } else { 
            $ext = pathinfo($d['file_content'], PATHINFO_EXTENSION);
        ?>
            <div class="file-card">
                <div class="file-icon">📂</div>
                <h2 style="margin-top:0; color:#0A1F44;">Dokumen Pembelajaran</h2>
                <p style="color:#666; margin-bottom:30px; font-size:16px;">
                    File <b>.<?= strtoupper($ext); ?></b> tersedia untuk diunduh.
                </p>
                <a href="<?= $file_path; ?>" class="btn-download" download>⬇ Unduh File</a>
            </div>
        <?php } ?>


        <!-- 4. AREA DISKUSI -->
        <div class="discussion-area">
            
            <!-- Form Input -->
            <div class="comment-form">
                <h3>💬 Diskusi & Tanya Jawab</h3>
                <form method="POST">
                    <textarea name="komentar" placeholder="Tulis pertanyaan..." required></textarea>
                    <button type="submit" name="kirim_komentar" class="btn-kirim">Kirim Pertanyaan</button>
                </form>
            </div>

            <!-- Daftar Komentar -->
            <div class="comment-list">
                <?php
                $q_komen = mysqli_query($conn, "SELECT k.*, u.nama_lengkap, u.role, u.foto FROM komentar k JOIN users u ON k.user_id = u.id WHERE k.materi_id='$id_materi' ORDER BY k.id DESC");
                
                if(mysqli_num_rows($q_komen) == 0){
                    echo "<div style='text-align:center; color:#999; padding:30px; background:white; border-radius:16px;'>Belum ada diskusi.</div>";
                }

                while($k = mysqli_fetch_assoc($q_komen)){
                    $inisial = strtoupper(substr($k['nama_lengkap'], 0, 1));
                    $role_badge = ($k['role'] == 'guru') ? "<span class='badge-guru'>GURU</span>" : "";
                    $is_guru = ($k['role'] == 'guru') ? "style-guru" : ""; 
                ?>
                    <div class="comment-item <?= $is_guru; ?>">
                        <div class="comment-avatar">
                            <?php if(!empty($k['foto']) && file_exists("uploads/".$k['foto'])) { ?>
                                <img src="uploads/<?= $k['foto']; ?>" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
                            <?php } else { echo $inisial; } ?>
                        </div>
                        
                        <div class="comment-content">
                            <h4>
                                <span><?= htmlspecialchars($k['nama_lengkap']); ?> <?= $role_badge; ?></span>
                                <span class="comment-date"><?= date('d M Y, H:i', strtotime($k['tanggal'])); ?></span>
                            </h4>
                            <p><?= nl2br(htmlspecialchars($k['isi_komentar'])); ?></p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

    </div>

</body>
</html>