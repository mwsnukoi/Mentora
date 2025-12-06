<?php
session_start();
include 'koneksi.php';

// Cek Akses: Hanya Siswa
if(!isset($_SESSION['status']) || $_SESSION['role'] != 'siswa'){ 
    header("location:quiz.php"); 
    exit(); 
}

$id_quiz = $_GET['id'];
$id_siswa = $_SESSION['id_user'];

// Cek apakah siswa sudah pernah mengerjakan? (Cegah submit ulang)
$cek_sudah = mysqli_query($conn, "SELECT id FROM nilai_quiz WHERE quiz_id='$id_quiz' AND siswa_id='$id_siswa'");
if(mysqli_num_rows($cek_sudah) > 0){
    echo "<script>alert('Anda sudah mengerjakan ujian ini!'); window.location='quiz.php';</script>";
    exit();
}

// --- PROSES HITUNG NILAI (SAAT TOMBOL KIRIM DIKLIK) ---
if(isset($_POST['selesai'])){
    $jawaban_siswa = isset($_POST['jwb']) ? $_POST['jwb'] : []; 
    
    // Ambil semua kunci jawaban benar dari database
    $q_kunci = mysqli_query($conn, "SELECT id, kunci_jawaban FROM soal WHERE quiz_id='$id_quiz'");
    
    $total_soal = mysqli_num_rows($q_kunci);
    $benar = 0;

    // Bandingkan jawaban siswa dengan kunci
    while($row = mysqli_fetch_assoc($q_kunci)){
        $id_soal = $row['id'];
        $kunci = $row['kunci_jawaban'];
        
        // Cek apakah siswa menjawab soal ini dan jawabannya benar
        if(isset($jawaban_siswa[$id_soal]) && $jawaban_siswa[$id_soal] == $kunci){
            $benar++;
        }
    }

    // Hitung Skor (Skala 0-100)
    $skor = 0;
    if($total_soal > 0) {
        $skor = ($benar / $total_soal) * 100;
        $skor = number_format($skor, 0); // Bulatkan
    }

    // Simpan Nilai ke Database
    mysqli_query($conn, "INSERT INTO nilai_quiz (quiz_id, siswa_id, skor) VALUES ('$id_quiz', '$id_siswa', '$skor')");
    
    echo "<script>alert('Ujian Selesai! Skor Anda: $skor'); window.location='quiz.php';</script>";
    exit();
}

// Ambil Info Quiz
$q_quiz = mysqli_query($conn, "SELECT * FROM quiz WHERE id='$id_quiz'");
$d_quiz = mysqli_fetch_assoc($q_quiz);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ujian: <?= htmlspecialchars($d_quiz['judul']); ?></title>
    <style>
        /* Tema Mentora */
        body { font-family: 'Segoe UI', sans-serif; background: #EAF3FF; margin: 0; }
        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; padding-bottom: 100px; }
        
        /* Header Ujian (Navy Dark) */
        .quiz-header { 
            background: #0A1F44; color: white; 
            padding: 30px; border-radius: 16px; 
            text-align: center; margin-bottom: 30px; 
            box-shadow: 0 10px 30px rgba(10,31,68,0.2); 
        }
        .quiz-header h1 { margin: 0; font-size: 24px; font-weight: 800; letter-spacing: 0.5px; }
        .quiz-header p { margin: 10px 0 0; opacity: 0.8; font-size: 14px; }

        /* Card Soal */
        .soal-box { 
            background: white; 
            padding: 30px; 
            border-radius: 16px; 
            margin-bottom: 25px; 
            box-shadow: 0 4px 15px rgba(10,31,68,0.03); 
            border: 1px solid #fff;
        }
        
        .soal-number {
            font-size: 12px; font-weight: 800; color: #1F75FE; 
            text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;
        }
        
        .soal-text { 
            font-weight: 600; font-size: 18px; color: #0A1F44; 
            margin-bottom: 20px; line-height: 1.6; 
        }
        
        /* Pilihan Ganda Custom Style */
        .option-group { display: flex; flex-direction: column; gap: 12px; }
        
        .option-label { 
            display: flex; align-items: center; 
            padding: 15px 20px; 
            border: 2px solid #f0f2f5; 
            border-radius: 10px; 
            cursor: pointer; 
            transition: 0.2s; 
            font-size: 15px;
            color: #555;
        }
        
        /* Efek Hover & Checked */
        .option-label:hover { background: #f8fbff; border-color: #b3d7ff; }
        
        /* Menyembunyikan radio button asli dan styling label ketika dipilih */
        input[type="radio"] { 
            margin-right: 15px; 
            transform: scale(1.3); 
            accent-color: #1F75FE; 
        }
        
        /* Highlight Pilihan yang Dipilih */
        input[type="radio"]:checked + span { color: #0A1F44; font-weight: bold; }
        .option-label:has(input:checked) { border-color: #1F75FE; background-color: #f0f7ff; }

        /* Tombol Kirim Fixed Bottom */
        .footer-action { 
            position: fixed; bottom: 0; left: 0; width: 100%; 
            background: white; padding: 20px; text-align: center; 
            box-shadow: 0 -5px 20px rgba(0,0,0,0.05); 
            border-top: 1px solid #eee; z-index: 999;
        }
        
        .btn-kirim { 
            background: #1F75FE; color: white; 
            padding: 14px 50px; border: none; 
            font-size: 16px; cursor: pointer; 
            border-radius: 30px; font-weight: bold; 
            box-shadow: 0 4px 15px rgba(31, 117, 254, 0.3); 
            transition: 0.3s; 
        }
        .btn-kirim:hover { background: #0A1F44; transform: translateY(-3px); }
        
        .btn-batal {
            text-decoration: none; color: #ff6b6b; font-weight: bold; font-size: 14px; margin-right: 20px;
        }
        
        /* Empty State */
        .empty-state { text-align: center; padding: 40px; color: #666; }
        .empty-state a { color: #1F75FE; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

    <div class="container">
        
        <!-- Header -->
        <div class="quiz-header">
            <h1><?= htmlspecialchars($d_quiz['judul']); ?></h1>
            <p>Jawablah pertanyaan di bawah ini dengan jujur dan teliti.</p>
        </div>
        
        <form method="POST">
            <?php
            // Ambil Soal (Diacak)
            $q_soal = mysqli_query($conn, "SELECT * FROM soal WHERE quiz_id='$id_quiz' ORDER BY RAND()");
            
            if(mysqli_num_rows($q_soal) == 0){
                echo "<div class='empty-state'>Soal belum tersedia. Silakan hubungi guru. <br><br> <a href='quiz.php'>Kembali</a></div>";
            }

            $no = 1;
            while($s = mysqli_fetch_assoc($q_soal)){
            ?>
                <div class="soal-box">
                    <div class="soal-number">Pertanyaan <?= $no++; ?></div>
                    <div class="soal-text"><?= nl2br(htmlspecialchars($s['pertanyaan'])); ?></div>
                    
                    <div class="option-group">
                        <label class="option-label">
                            <input type="radio" name="jwb[<?= $s['id']; ?>]" value="A" required> 
                            <span><?= htmlspecialchars($s['opsi_a']); ?></span>
                        </label>
                        
                        <label class="option-label">
                            <input type="radio" name="jwb[<?= $s['id']; ?>]" value="B"> 
                            <span><?= htmlspecialchars($s['opsi_b']); ?></span>
                        </label>
                        
                        <label class="option-label">
                            <input type="radio" name="jwb[<?= $s['id']; ?>]" value="C"> 
                            <span><?= htmlspecialchars($s['opsi_c']); ?></span>
                        </label>
                        
                        <label class="option-label">
                            <input type="radio" name="jwb[<?= $s['id']; ?>]" value="D"> 
                            <span><?= htmlspecialchars($s['opsi_d']); ?></span>
                        </label>
                    </div>
                </div>
            <?php } ?>

            <!-- Tombol Submit (Hanya muncul jika ada soal) -->
            <?php if(mysqli_num_rows($q_soal) > 0) { ?>
                <div class="footer-action">
                    <a href="quiz.php" class="btn-batal" onclick="return confirm('Yakin ingin membatalkan? Jawaban tidak akan tersimpan.');">Batal</a>
                    <button type="submit" name="selesai" class="btn-kirim" onclick="return confirm('Apakah Anda yakin sudah selesai mengerjakan? Jawaban akan dikunci setelah dikirim.');">Selesai & Kirim Jawaban</button>
                </div>
            <?php } ?>
        </form>
    </div>

</body>
</html>