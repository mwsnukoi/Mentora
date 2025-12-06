<?php
session_start();
include 'koneksi.php';
$id_user = $_SESSION['id_user'];

// --- LOGIKA BUAT QUIZ (KHUSUS GURU) ---
if(isset($_POST['buat_quiz']) && $_SESSION['role'] == 'guru'){
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $kelas_id = $_POST['kelas_id'];
    $desk = mysqli_real_escape_string($conn, $_POST['deskripsi']);

    $cek_kelas = mysqli_query($conn, "SELECT id FROM kelas WHERE id='$kelas_id' AND guru_id='$id_user'");
    if(mysqli_num_rows($cek_kelas) > 0){
        mysqli_query($conn, "INSERT INTO quiz (kelas_id, judul, deskripsi) VALUES ('$kelas_id', '$judul', '$desk')");
        echo "<script>alert('Quiz berhasil dibuat! Silakan atur soalnya.'); window.location='quiz.php';</script>";
    } else {
        echo "<script>alert('Gagal! Anda tidak memiliki akses ke kelas ini.');</script>";
    }
}

// --- LOGIKA HAPUS QUIZ (KHUSUS GURU) ---
if(isset($_GET['hapus_quiz']) && $_SESSION['role'] == 'guru'){
    $id_quiz = $_GET['hapus_quiz'];
    $cek = mysqli_query($conn, "SELECT q.id FROM quiz q JOIN kelas k ON q.kelas_id = k.id WHERE q.id='$id_quiz' AND k.guru_id='$id_user'");
    if(mysqli_num_rows($cek) > 0){
        mysqli_query($conn, "DELETE FROM soal WHERE quiz_id='$id_quiz'");
        mysqli_query($conn, "DELETE FROM nilai_quiz WHERE quiz_id='$id_quiz'");
        mysqli_query($conn, "DELETE FROM quiz WHERE id='$id_quiz'");
        echo "<script>alert('Quiz berhasil dihapus.'); window.location='quiz.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus quiz.'); window.location='quiz.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quiz & Ujian - Mentora</title>
    <!-- META TAG WAJIB AGAR TIDAK ZOOM OUT DI HP -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #EAF3FF; margin: 0; }
        
        /* Container Utama */
        .container { 
            max-width: 1000px; 
            margin: 30px auto; 
            padding: 0 20px; 
            padding-bottom: 60px;
            box-sizing: border-box; 
        }
        
        /* TOMBOL KEMBALI */
        .btn-back {
            display: inline-flex; align-items: center; gap: 8px;
            text-decoration: none; color: #3C4A5A; font-weight: 700;
            font-size: 16px; margin-bottom: 20px; transition: 0.3s;
        }
        .btn-back span { font-size: 20px; transition: 0.3s; }
        .btn-back:hover { color: #1F75FE; }
        .btn-back:hover span { transform: translateX(-5px); }

        /* --- FORM GURU (PERBAIKAN DISINI) --- */
        .box-guru { 
            background: white; padding: 30px; border-radius: 16px; 
            border-left: 6px solid #1F75FE; 
            box-shadow: 0 4px 20px rgba(10,31,68,0.05); 
            margin-bottom: 40px; 
            width: 100%; 
            box-sizing: border-box; /* Mencegah overflow */
        }
        .box-guru h3 { margin-top: 0; color: #0A1F44; font-size: 20px; font-weight: 800; margin-bottom: 20px; }
        
        /* Menggunakan CSS GRID agar Responsif Otomatis */
        .form-grid { 
            display: grid; 
            /* Kolom otomatis menyesuaikan. Min 200px, Max 1 bagian penuh */
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); 
            gap: 15px; 
            width: 100%;
            align-items: center;
        }
        
        /* INPUT & SELECT */
        input, select { 
            width: 100%; /* Pastikan lebar penuh di dalam grid cell */
            padding: 12px; 
            border: 1px solid #ccc; 
            border-radius: 8px; 
            font-family: inherit; 
            font-size: 14px;
            box-sizing: border-box; /* Kunci agar padding tidak menambah lebar */
            background: white;
            height: 45px; /* Tinggi seragam */
        }
        input:focus, select:focus { border-color: #1F75FE; outline: none; }
        
        .btn-buat { 
            background: #1F75FE; color: white; border: none; 
            padding: 0 25px; border-radius: 8px; cursor: pointer; 
            font-weight: bold; font-size: 14px; transition: 0.2s;
            height: 45px; /* Tinggi disamakan dengan input */
            width: 100%; /* Tombol memenuhi kolom gridnya */
        }
        .btn-buat:hover { background: #0A1F44; }

        /* Grid Quiz (Daftar Kartu) */
        .section-title { 
            font-size: 24px; color: #0A1F44; font-weight: 800; 
            margin-bottom: 25px; border-left: 6px solid #0A1F44; 
            padding-left: 15px; 
        }

        .grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
            gap: 25px; 
        }
        
        .card { 
            background: white; border-radius: 16px; padding: 25px; 
            box-shadow: 0 4px 15px rgba(10,31,68,0.03); 
            border: 1px solid #fff; position: relative; 
            display: flex; flex-direction: column; justify-content: space-between;
            transition: 0.3s; box-sizing: border-box;
        }
        .card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(10,31,68,0.1); }
        
        .badge-kelas { background: #EAF3FF; color: #1F75FE; padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; text-transform: uppercase; width: fit-content; margin-bottom: 15px; }
        .card h3 { margin: 0 0 10px 0; color: #0A1F44; font-size: 18px; font-weight: 700; line-height: 1.4; }
        .card p { color: #555; font-size: 14px; line-height: 1.6; margin: 0 0 15px 0; }
        
        .card-footer { margin-top: 20px; padding-top: 15px; border-top: 1px solid #f0f0f0; display: flex; flex-direction: column; gap: 10px; }
        .btn-action { display: block; text-align: center; text-decoration: none; padding: 10px; border-radius: 4px; margin-top: 10px; font-weight: bold; font-size: 13px; transition: 0.2s; }
        .btn-soal { background: #fff; color: #0A1F44; border: 1px solid #0A1F44; } .btn-soal:hover { background: #0A1F44; color: white; }
        .btn-kerjakan { background: #1F75FE; color: white; border: 1px solid #1F75FE; } .btn-kerjakan:hover { background: #0A1F44; border-color: #0A1F44; }
        .btn-hapus { color: #dc3545; font-size: 12px; margin-top: 5px; text-decoration: none; text-align: center; display: block; } .btn-hapus:hover { text-decoration: underline; }
        
        .score-badge { position: absolute; top: 20px; right: 20px; background: #0A1F44; color: white; padding: 5px 12px; border-radius: 20px; font-weight: bold; font-size: 13px; }
        .status-selesai { text-align: center; color: #28a745; font-weight: bold; font-size: 14px; background: #e8f5e9; padding: 10px; border-radius: 8px; margin-top: 10px; }
        .empty-state { text-align: center; color: #888; padding: 50px; background: white; border-radius: 16px; grid-column: 1/-1; }

        /* --- RESPONSIF MOBILE --- */
        @media (max-width: 768px) {
            .container { padding: 15px; }
            
            .box-guru { padding: 20px; }
            
            /* Di HP, Grid form jadi 1 kolom penuh ke bawah */
            .form-grid { 
                grid-template-columns: 1fr; 
                gap: 15px;
            }
            
            .btn-buat { font-size: 16px; padding: 0; }
            
            .grid { grid-template-columns: 1fr; } /* Kartu jadi 1 kolom */
            
            .section-title { font-size: 20px; }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <a href="index.php" class="btn-back"><span>&larr;</span> Kembali ke Dashboard</a>

        <?php if($_SESSION['role'] == 'guru') { ?>
            <div class="box-guru">
                <h3>📝 Buat Quiz / Ujian Baru</h3>
                
                <!-- FORM MENGGUNAKAN GRID AGAR RAPI DI SEMUA LAYAR -->
                <form method="POST" class="form-grid">
                    <select name="kelas_id" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php
                        $q_kls = mysqli_query($conn, "SELECT * FROM kelas WHERE guru_id='$id_user'");
                        while($k = mysqli_fetch_assoc($q_kls)){
                            echo "<option value='{$k['id']}'>{$k['nama_kelas']}</option>";
                        }
                        ?>
                    </select>
                    
                    <input type="text" name="judul" placeholder="Judul Quiz" required>
                    <input type="text" name="deskripsi" placeholder="Deskripsi singkat...">
                    
                    <button type="submit" name="buat_quiz" class="btn-buat">+ Buat</button>
                </form>
            </div>
        <?php } ?>

        <h2 class="section-title">Daftar Quiz Tersedia</h2>
        
        <div class="grid">
            <?php
            if($_SESSION['role'] == 'guru'){
                $query = "SELECT q.*, k.nama_kelas FROM quiz q JOIN kelas k ON q.kelas_id = k.id WHERE k.guru_id='$id_user' ORDER BY q.id DESC";
            } else {
                $query = "SELECT q.*, k.nama_kelas FROM quiz q JOIN kelas k ON q.kelas_id = k.id JOIN kelas_siswa ks ON k.id = ks.kelas_id WHERE ks.siswa_id='$id_user' ORDER BY q.id DESC";
            }

            $exec = mysqli_query($conn, $query);
            if(mysqli_num_rows($exec) == 0) echo "<div class='empty-state'>Belum ada quiz atau ujian.</div>";

            while($row = mysqli_fetch_assoc($exec)){
                $nilai_show = "";
                if($_SESSION['role'] == 'siswa'){
                    $q_nilai = mysqli_query($conn, "SELECT skor FROM nilai_quiz WHERE quiz_id='{$row['id']}' AND siswa_id='$id_user'");
                    if(mysqli_num_rows($q_nilai) > 0){
                        $d_nilai = mysqli_fetch_assoc($q_nilai);
                        $nilai_show = "<div class='score-badge'>Nilai: {$d_nilai['skor']}</div>";
                    }
                }
            ?>
                <div class="card">
                    <?= $nilai_show; ?>
                    <span class="badge-kelas"><?= htmlspecialchars($row['nama_kelas']); ?></span>
                    
                    <div>
                        <h3><?= htmlspecialchars($row['judul']); ?></h3>
                        <p><?= htmlspecialchars($row['deskripsi']); ?></p>
                        <small style="color:#999;">Dibuat: <?= date('d M Y', strtotime($row['tanggal_buat'])); ?></small>
                    </div>
                    
                    <div class="card-footer">
                        <?php if($_SESSION['role'] == 'guru') { ?>
                            <a href="atur_soal.php?id=<?= $row['id']; ?>" class="btn-action btn-soal">⚙️ Atur Soal</a>
                            <a href="quiz.php?hapus_quiz=<?= $row['id']; ?>" class="btn-hapus" onclick="return confirm('PERINGATAN: Menghapus quiz ini akan menghapus semua soal dan nilai siswa. Yakin?');">🗑️ Hapus</a>
                        <?php } elseif($_SESSION['role'] == 'siswa') { 
                            if($nilai_show == ""){
                        ?>
                            <a href="kerjakan_quiz.php?id=<?= $row['id']; ?>" class="btn-action btn-kerjakan">✍️ Kerjakan Sekarang</a>
                        <?php } else { ?>
                            <div class="status-selesai">✅ Selesai Dikerjakan</div>
                        <?php } } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>