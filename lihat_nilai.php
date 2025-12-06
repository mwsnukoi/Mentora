<?php
session_start();
include 'koneksi.php';
if(!isset($_SESSION['status'])) header("location:login.php");

$role = $_SESSION['role'];
$id_user = $_SESSION['id_user'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transkrip Nilai - Mentora</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Tema Mentora */
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #EAF3FF; }
        .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; min-height: 60vh; }
        
        /* Card Container */
        .card { 
            background: white; border-radius: 16px; 
            box-shadow: 0 4px 20px rgba(10,31,68,0.05); 
            padding: 30px; margin-bottom: 30px; 
            border-top: 5px solid #0A1F44; 
        }
        
        h2, h3 { margin-top: 0; color: #0A1F44; font-weight: 800; font-size: 22px; }
        p { color: #666; font-size: 15px; line-height: 1.6; }

        /* Tabel Desktop Standard */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; }
        th { background-color: #f8f9fa; color: #3C4A5A; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        tr:hover { background-color: #fbfbfb; }
        
        /* Badge Skor */
        .score-box { font-weight: bold; padding: 5px 12px; border-radius: 6px; display: inline-block; min-width: 40px; text-align: center; font-size: 13px; }
        .score-high { background: #e8f5e9; color: #2e7d32; }
        .score-med { background: #fff3e0; color: #e65100; }
        .score-low { background: #ffebee; color: #c62828; }

        /* Tombol Pilihan Kelas (Guru) */
        .class-btn { 
            display: inline-block; background: #1F75FE; color: white; 
            padding: 10px 20px; border-radius: 30px; text-decoration: none; 
            font-size: 13px; font-weight: bold; margin: 0 8px 10px 0; transition: 0.2s;
            box-shadow: 0 2px 5px rgba(31, 117, 254, 0.2);
        }
        .class-btn:hover, .class-btn.active { background: #0A1F44; transform: translateY(-2px); }
        .class-btn.active { box-shadow: none; cursor: default; }
        
        /* Tombol Kembali */
        .btn-back {
            display: inline-flex; align-items: center; gap: 8px;
            text-decoration: none; color: #3C4A5A; font-weight: 700;
            font-size: 16px; margin-bottom: 20px; transition: 0.3s;
        }
        .btn-back:hover { color: #1F75FE; transform: translateX(-5px); }
        
        .empty-state { text-align: center; padding: 40px; color: #888; font-style: italic; }

        /* --- MODE MOBILE: TABEL VERTIKAL --- */
        @media (max-width: 768px) {
            .card { padding: 20px; }
            
            /* Sembunyikan Header Tabel Asli */
            thead { display: none; }
            
            /* Ubah Baris (tr) menjadi Blok Kartu */
            tr {
                display: block;
                margin-bottom: 20px;
                border: 1px solid #eee;
                border-radius: 8px;
                background: #fff;
                box-shadow: 0 2px 5px rgba(0,0,0,0.03);
                padding: 10px;
            }
            
            /* Ubah Sel (td) menjadi Flex Row (Kiri: Label, Kanan: Isi) */
            td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: right;
                padding: 10px 5px;
                border-bottom: 1px solid #f5f5f5;
                font-size: 14px;
            }
            
            td:last-child { border-bottom: none; }
            
            /* Tampilkan Label Header dari atribut data-label */
            td::before {
                content: attr(data-label);
                font-weight: 700;
                color: #0A1F44;
                text-transform: uppercase;
                font-size: 11px;
                margin-right: 15px;
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        
        <a href="index.php" class="btn-back"><span>&larr;</span> Kembali ke Dashboard</a>

        <?php if($role == 'siswa') { ?>
            <!-- TAMPILAN SISWA -->
            <div class="card">
                <h2>📊 Transkrip Nilai Ujian</h2>
                <p>Riwayat hasil pengerjaan Quiz dan Ujian Online Anda.</p>
                
                <table>
                    <thead>
                        <tr>
                            <th>Kelas</th>
                            <th>Judul Ujian</th>
                            <th>Tanggal</th>
                            <th>Skor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($conn, "SELECT k.nama_kelas, q.judul, nq.tanggal_kerja as tgl, nq.skor 
                                                      FROM nilai_quiz nq 
                                                      JOIN quiz q ON nq.quiz_id = q.id 
                                                      JOIN kelas k ON q.kelas_id = k.id 
                                                      WHERE nq.siswa_id='$id_user' 
                                                      ORDER BY nq.tanggal_kerja DESC");

                        if(mysqli_num_rows($query) == 0) {
                            echo "<tr><td colspan='4' class='empty-state'>Belum ada nilai ujian yang tercatat.</td></tr>";
                        }

                        while($row = mysqli_fetch_assoc($query)){
                            $s = $row['skor'];
                            $cls = ($s >= 80) ? 'score-high' : (($s >= 60) ? 'score-med' : 'score-low');
                        ?>
                            <tr>
                                <td data-label="Kelas" style="font-weight:600; color:#0A1F44;"><?= htmlspecialchars($row['nama_kelas']); ?></td>
                                <td data-label="Judul Ujian"><?= htmlspecialchars($row['judul']); ?></td>
                                <td data-label="Tanggal" style="color:#777;"><?= date('d M Y', strtotime($row['tgl'])); ?></td>
                                <td data-label="Skor"><span class="score-box <?= $cls; ?>"><?= $s; ?></span></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        <?php } elseif($role == 'guru') { ?>
            <!-- TAMPILAN GURU -->
            <div class="card">
                <h2>📝 Rekap Nilai Siswa</h2>
                <p>Silakan pilih kelas di bawah ini untuk melihat daftar nilai ujian siswa.</p>
                
                <div style="margin-top:20px;">
                    <?php
                    $q_kelas = mysqli_query($conn, "SELECT * FROM kelas WHERE guru_id='$id_user'");
                    
                    if(mysqli_num_rows($q_kelas) == 0) echo "<p style='color:#999;'>Anda belum memiliki kelas.</p>";

                    while($k = mysqli_fetch_assoc($q_kelas)){
                        $active = (isset($_GET['id_kelas']) && $_GET['id_kelas'] == $k['id']) ? "active" : "";
                        echo "<a href='lihat_nilai.php?id_kelas={$k['id']}' class='class-btn $active'>{$k['nama_kelas']}</a>";
                    }
                    ?>
                </div>
            </div>

            <!-- Tabel Nilai Guru -->
            <?php if(isset($_GET['id_kelas'])) { 
                $idk = $_GET['id_kelas'];
                $n_kls = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_kelas FROM kelas WHERE id='$idk'"));
            ?>
                <div class="card">
                    <h3 style="border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 0;">
                        Nilai Ujian: <span style="color:#1F75FE;"><?= $n_kls['nama_kelas']; ?></span>
                    </h3>
                    
                    <table>
                        <thead>
                            <tr>
                                <th width="35%">Nama Siswa</th>
                                <th width="45%">Judul Ujian</th>
                                <th width="20%">Nilai Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $q_nilai = mysqli_query($conn, "SELECT u.nama_lengkap, q.judul, nq.skor 
                                FROM nilai_quiz nq 
                                JOIN users u ON nq.siswa_id = u.id 
                                JOIN quiz q ON nq.quiz_id = q.id 
                                WHERE q.kelas_id='$idk' 
                                ORDER BY u.nama_lengkap ASC");
                            
                            if(mysqli_num_rows($q_nilai) == 0) {
                                echo "<tr><td colspan='3' class='empty-state'>Belum ada data nilai ujian di kelas ini.</td></tr>";
                            }

                            while($dn = mysqli_fetch_assoc($q_nilai)){
                                $s = $dn['skor'];
                                $cls = ($s >= 80) ? 'score-high' : (($s >= 60) ? 'score-med' : 'score-low');
                                echo "<tr>
                                    <td data-label='Nama Siswa' style='font-weight:bold; color:#333;'>{$dn['nama_lengkap']}</td>
                                    <td data-label='Judul Ujian'>{$dn['judul']}</td>
                                    <td data-label='Nilai Akhir'><span class='score-box $cls'>$s</span></td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>

        <?php } ?>
    </div>
    
    
</body>
</html>