<?php
session_start();
include 'koneksi.php';

if($_SESSION['role'] != 'guru') header("location:index.php");
$kelas_id = $_GET['kelas_id'];

// Ambil nama kelas untuk judul
$q_kelas = mysqli_query($conn, "SELECT nama_kelas FROM kelas WHERE id='$kelas_id'");
$d_kelas = mysqli_fetch_assoc($q_kelas);

if(isset($_POST['simpan'])){
    // Amankan input teks dasar
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $bab = mysqli_real_escape_string($conn, $_POST['bab']);
    $tipe = mysqli_real_escape_string($conn, $_POST['tipe']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    $content = "";
    
    // Logic Upload File & Konten
    if($tipe == 'pdf' || $tipe == 'ppt' || $tipe == 'video'){
        if(!empty($_FILES['file_upload']['name'])){
            $filename = $_FILES['file_upload']['name'];
            $tmp_name = $_FILES['file_upload']['tmp_name'];
            $new_name = time()."_".$filename;
            
            // Upload file
            move_uploaded_file($tmp_name, 'uploads/'.$new_name);
            
            // Amankan nama file sebelum masuk DB
            $content = mysqli_real_escape_string($conn, $new_name);
        }
    } else if ($tipe == 'youtube'){
        // Amankan Link Youtube
        $content = mysqli_real_escape_string($conn, $_POST['link_youtube']);
    } else {
        // Amankan Teks Bacaan (PENTING: Ini sumber error sebelumnya)
        $content = mysqli_real_escape_string($conn, $_POST['teks_materi']);
    }

    $query = "INSERT INTO materi (kelas_id, judul, bab, tipe, file_content, deskripsi) VALUES ('$kelas_id', '$judul', '$bab', '$tipe', '$content', '$deskripsi')";
    
    if(mysqli_query($conn, $query)){
        echo "<script>alert('Materi berhasil diupload!'); window.location='lihat_kelas.php?id=$kelas_id';</script>";
    } else {
        // Tampilkan error spesifik jika gagal (untuk debugging)
        echo "<script>alert('Gagal: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Materi - Mentora</title>
    <style>
        /* Tema Mentora */
        body { font-family: 'Segoe UI', sans-serif; background: #EAF3FF; margin: 0; }
        .container { max-width: 700px; margin: 40px auto; padding: 0 20px; padding-bottom: 80px; }
        
        /* TOMBOL KEMBALI */
        .btn-back {
            display: inline-flex; align-items: center; gap: 8px;
            text-decoration: none; color: #3C4A5A; font-weight: 700;
            font-size: 16px; margin-bottom: 20px; transition: 0.3s;
        }
        .btn-back:hover { color: #1F75FE; transform: translateX(-5px); }

        /* CARD FORM */
        .card-form {
            background: white; padding: 40px; border-radius: 16px;
            box-shadow: 0 10px 40px rgba(10, 31, 68, 0.08);
            border-top: 6px solid #1F75FE; /* Aksen Biru Modern */
        }

        /* Header Form */
        .form-header { margin-bottom: 30px; text-align: center; }
        .form-header h2 { margin: 0; color: #0A1F44; font-size: 24px; font-weight: 800; }
        .form-header p { margin: 5px 0 0; color: #666; font-size: 14px; }
        .class-badge { 
            background: #EAF3FF; color: #1F75FE; padding: 4px 10px; 
            border-radius: 6px; font-weight: bold; font-size: 12px; display: inline-block; margin-top: 5px;
        }

        /* Form Elements */
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-weight: 700; color: #3C4A5A; margin-bottom: 8px; font-size: 14px; }
        
        input[type="text"], textarea, select, input[type="file"] {
            width: 100%; padding: 12px 15px; 
            border: 1px solid #ddd; border-radius: 8px;
            box-sizing: border-box; font-family: inherit; font-size: 14px;
            background: #F8F9FA; transition: 0.3s;
        }
        
        input:focus, textarea:focus, select:focus {
            border-color: #1F75FE; background: white; outline: none;
            box-shadow: 0 0 0 3px rgba(31, 117, 254, 0.1);
        }

        /* Area Input Khusus (Dynamic) */
        .dynamic-input {
            background: #f0f7ff; padding: 20px; border-radius: 10px; 
            border: 1px dashed #1F75FE; margin-top: 10px;
            display: none; /* Sembunyi Default */
        }

        /* Tombol Submit */
        .btn-submit {
            width: 100%; padding: 14px;
            background: #0A1F44; color: white;
            border: none; border-radius: 8px;
            font-weight: bold; font-size: 16px; cursor: pointer;
            transition: 0.2s; margin-top: 10px;
            box-shadow: 0 4px 15px rgba(10, 31, 68, 0.2);
        }
        .btn-submit:hover { background: #1F75FE; transform: translateY(-2px); }

        /* Icon Helper */
        .input-hint { font-size: 12px; color: #888; margin-top: 5px; display: block; }
    </style>
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="container">
        <a href="lihat_kelas.php?id=<?= $kelas_id; ?>" class="btn-back"><span>&larr;</span> Batal & Kembali</a>
        
        <div class="card-form">
            <div class="form-header">
                <h2>Upload Materi Baru</h2>
                <p>Tambahkan bahan ajar untuk kelas:</p>
                <span class="class-badge"><?= htmlspecialchars($d_kelas['nama_kelas']); ?></span>
            </div>

            <form method="POST" enctype="multipart/form-data">
                
                <!-- JUDUL -->
                <div class="form-group">
                    <label class="form-label">Judul Materi</label>
                    <input type="text" name="judul" required placeholder="Contoh: Pengantar Algoritma Dasar">
                </div>

                <!-- BAB -->
                <div class="form-group">
                    <label class="form-label">Nama BAB / Modul</label>
                    <input type="text" name="bab" required placeholder="Contoh: BAB 1 - Pendahuluan">
                    <span class="input-hint">Gunakan nama BAB yang sama untuk mengelompokkan materi.</span>
                </div>

                <!-- DESKRIPSI -->
                <div class="form-group">
                    <label class="form-label">Deskripsi Singkat (Opsional)</label>
                    <textarea name="deskripsi" rows="3" placeholder="Jelaskan sedikit tentang materi ini..."></textarea>
                </div>

                <!-- PILIHAN TIPE -->
                <div class="form-group">
                    <label class="form-label">Tipe Materi</label>
                    <select name="tipe" id="tipe" onchange="cekTipe()" required>
                        <option value="" disabled selected>- Pilih Tipe Konten -</option>
                        <option value="pdf">📄 File PDF</option>
                        <option value="ppt">📊 File Presentasi (PPT/Word/Excel)</option>
                        <option value="video">🎥 Upload Video (MP4)</option>
                        <option value="youtube">▶️ Link YouTube</option>
                        <option value="teks">📝 Artikel / Teks Bacaan</option>
                    </select>
                </div>

                <!-- INPUT DINAMIS (Muncul sesuai pilihan) -->
                
                <!-- 1. INPUT FILE -->
                <div id="input-file" class="dynamic-input">
                    <label class="form-label">Pilih File (PDF/PPT/Video)</label>
                    <input type="file" name="file_upload">
                    <span class="input-hint">Maksimal ukuran file tergantung server (biasanya 2MB - 40MB).</span>
                </div>

                <!-- 2. INPUT YOUTUBE -->
                <div id="input-link" class="dynamic-input">
                    <label class="form-label">Masukkan Link YouTube</label>
                    <input type="text" name="link_youtube" placeholder="https://www.youtube.com/watch?v=...">
                </div>
                
                <!-- 3. INPUT TEKS -->
                <div id="input-teks" class="dynamic-input">
                    <label class="form-label">Tulis Materi Lengkap</label>
                    <textarea name="teks_materi" rows="10" placeholder="Tulis isi materi pelajaran di sini..."></textarea>
                </div>

                <button type="submit" name="simpan" class="btn-submit">Simpan & Publikasikan</button>
            </form>
        </div>
    </div>


    <!-- SCRIPT INTERAKTIF -->
    <script>
        function cekTipe(){
            let tipe = document.getElementById('tipe').value;
            
            // Sembunyikan semua dulu
            document.getElementById('input-file').style.display = 'none';
            document.getElementById('input-link').style.display = 'none';
            document.getElementById('input-teks').style.display = 'none';

            // Tampilkan yang sesuai
            if(tipe == 'pdf' || tipe == 'ppt' || tipe == 'video'){
                document.getElementById('input-file').style.display = 'block';
            } else if(tipe == 'youtube'){
                document.getElementById('input-link').style.display = 'block';
            } else if(tipe == 'teks'){
                document.getElementById('input-teks').style.display = 'block';
            }
        }
    </script>
</body>
</html>