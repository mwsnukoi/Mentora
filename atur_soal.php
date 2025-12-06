<?php
session_start();
include 'koneksi.php';

// Cek akses: Hanya Guru
if(!isset($_SESSION['status']) || $_SESSION['role'] != 'guru'){ 
    header("location:quiz.php"); 
    exit(); 
}

$id_quiz = $_GET['id'];
$id_user = $_SESSION['id_user'];

// Validasi: Pastikan quiz ini milik guru yang sedang login
$cek_quiz = mysqli_query($conn, "SELECT q.* FROM quiz q JOIN kelas k ON q.kelas_id = k.id WHERE q.id='$id_quiz' AND k.guru_id='$id_user'");
if(mysqli_num_rows($cek_quiz) == 0){
    echo "<script>alert('Akses ditolak!'); window.location='quiz.php';</script>";
    exit();
}
$d_quiz = mysqli_fetch_assoc($cek_quiz);

// --- LOGIKA TAMBAH SOAL ---
if(isset($_POST['simpan_soal'])){
    $pertanyaan = mysqli_real_escape_string($conn, $_POST['pertanyaan']);
    $a = mysqli_real_escape_string($conn, $_POST['a']);
    $b = mysqli_real_escape_string($conn, $_POST['b']);
    $c = mysqli_real_escape_string($conn, $_POST['c']);
    $d = mysqli_real_escape_string($conn, $_POST['d']);
    $kunci = $_POST['kunci'];

    $query = "INSERT INTO soal (quiz_id, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, kunci_jawaban) 
              VALUES ('$id_quiz', '$pertanyaan', '$a', '$b', '$c', '$d', '$kunci')";
    
    if(mysqli_query($conn, $query)){
        echo "<script>alert('Soal berhasil ditambahkan!');</script>";
    }
}

// --- LOGIKA HAPUS SOAL ---
if(isset($_GET['hapus'])){
    $id_soal = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM soal WHERE id='$id_soal'");
    header("location:atur_soal.php?id=$id_quiz");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Atur Soal - Mentora</title>
    <style>
        /* Tema Mentora */
        body { font-family: 'Segoe UI', sans-serif; background: #EAF3FF; margin: 0; }
        .container { max-width: 800px; margin: 30px auto; padding: 0 20px; padding-bottom: 60px; }
        
        /* TOMBOL KEMBALI */
        .btn-back {
            display: inline-flex; align-items: center; gap: 8px;
            text-decoration: none; color: #3C4A5A; font-weight: 700;
            font-size: 16px; margin-bottom: 20px; transition: 0.3s;
        }
        .btn-back span { font-size: 20px; transition: 0.3s; }
        .btn-back:hover { color: #1F75FE; }
        .btn-back:hover span { transform: translateX(-5px); }

        /* Form Input Soal */
        .box-input { 
            background: white; padding: 30px; border-radius: 16px; 
            box-shadow: 0 4px 20px rgba(10,31,68,0.05); 
            border-top: 5px solid #1F75FE; /* Aksen Biru */
            margin-bottom: 40px; 
        }
        .box-input h2 { margin-top: 0; color: #0A1F44; font-size: 22px; margin-bottom: 20px; font-weight: 800; }
        
        .form-group { margin-bottom: 15px; }
        label { font-weight: 700; color: #3C4A5A; display: block; margin-bottom: 8px; font-size: 14px; }
        
        textarea, input[type="text"], select { 
            width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; 
            box-sizing: border-box; font-family: inherit; font-size: 14px;
        }
        textarea:focus, input:focus, select:focus { border-color: #1F75FE; outline: none; }
        
        .grid-option { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        
        .btn-simpan { 
            background: #1F75FE; color: white; border: none; padding: 12px 30px; 
            border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 14px; 
            transition: 0.2s; margin-top: 10px;
        }
        .btn-simpan:hover { background: #0A1F44; }

        /* List Soal */
        .soal-header { 
            display: flex; justify-content: space-between; align-items: center; 
            margin-bottom: 20px; border-bottom: 2px solid #ccc; padding-bottom: 10px; 
        }
        .soal-header h3 { margin: 0; color: #0A1F44; font-size: 20px; font-weight: 800; }
        
        .item-soal { 
            background: white; padding: 25px; border-radius: 12px; 
            margin-bottom: 15px; border-left: 5px solid #0A1F44; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.03); position: relative; 
        }
        
        .soal-text { margin: 0 0 15px 0; font-weight: 600; color: #333; font-size: 16px; line-height: 1.5; }
        
        .opsi { 
            display: grid; grid-template-columns: 1fr 1fr; gap: 10px; 
            font-size: 14px; color: #555; 
        }
        .opsi span { background: #f9f9f9; padding: 8px; border-radius: 6px; border: 1px solid #eee; }
        
        .kunci-badge { 
            background: #e8f5e9; color: #2e7d32; padding: 5px 10px; 
            border-radius: 6px; font-weight: bold; font-size: 12px; 
            display: inline-block; margin-top: 15px; border: 1px solid #c8e6c9;
        }
        
        .btn-hapus { 
            position: absolute; top: 20px; right: 20px; 
            color: #dc3545; text-decoration: none; font-weight: bold; font-size: 13px; 
            background: #fff0f0; padding: 6px 12px; border-radius: 6px; border: 1px solid #ffcdd2;
            transition: 0.2s;
        }
        .btn-hapus:hover { background: #dc3545; color: white; border-color: #dc3545; }
        
        .empty-state { text-align: center; padding: 40px; color: #888; font-style: italic; }
    </style>
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="container">
        <!-- TOMBOL KEMBALI -->
        <a href="quiz.php" class="btn-back"><span>&larr;</span> Kembali ke Daftar Quiz</a>

        <!-- FORM INPUT SOAL -->
        <div class="box-input">
            <h2>⚙️ Tambah Soal: <?= htmlspecialchars($d_quiz['judul']); ?></h2>
            <form method="POST">
                <div class="form-group">
                    <label>Pertanyaan</label>
                    <textarea name="pertanyaan" required rows="3" placeholder="Tulis pertanyaan di sini..."></textarea>
                </div>
                
                <div class="grid-option">
                    <div class="form-group"><label>Opsi A</label><input type="text" name="a" required></div>
                    <div class="form-group"><label>Opsi B</label><input type="text" name="b" required></div>
                    <div class="form-group"><label>Opsi C</label><input type="text" name="c" required></div>
                    <div class="form-group"><label>Opsi D</label><input type="text" name="d" required></div>
                </div>
                
                <div class="form-group">
                    <label>Kunci Jawaban Benar</label>
                    <select name="kunci" required>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                    </select>
                </div>

                <button type="submit" name="simpan_soal" class="btn-simpan">Simpan Soal</button>
            </form>
        </div>

        <!-- DAFTAR BANK SOAL -->
        <div class="soal-header">
            <h3>Bank Soal (<?= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM soal WHERE quiz_id='$id_quiz'")); ?>)</h3>
        </div>

        <?php
        $q_soal = mysqli_query($conn, "SELECT * FROM soal WHERE quiz_id='$id_quiz' ORDER BY id ASC");
        
        if(mysqli_num_rows($q_soal) == 0) {
            echo "<div class='empty-state'>Belum ada soal yang ditambahkan.</div>";
        }
        
        $no = 1;
        while($s = mysqli_fetch_assoc($q_soal)){
        ?>
            <div class="item-soal">
                <a href="atur_soal.php?id=<?= $id_quiz; ?>&hapus=<?= $s['id']; ?>" class="btn-hapus" onclick="return confirm('Yakin ingin menghapus soal ini?');">Hapus</a>
                
                <div class="soal-text"><?= $no++; ?>. <?= nl2br(htmlspecialchars($s['pertanyaan'])); ?></div>
                
                <div class="opsi">
                    <span>A. <?= htmlspecialchars($s['opsi_a']); ?></span>
                    <span>B. <?= htmlspecialchars($s['opsi_b']); ?></span>
                    <span>C. <?= htmlspecialchars($s['opsi_c']); ?></span>
                    <span>D. <?= htmlspecialchars($s['opsi_d']); ?></span>
                </div>
                
                <div class="kunci-badge">Kunci Jawaban: <?= $s['kunci_jawaban']; ?></div>
            </div>
        <?php } ?>
    </div>

</body>
</html>