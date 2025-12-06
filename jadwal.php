<?php
session_start();
include 'koneksi.php';
$id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 0;

// --- LOGIKA SIMPAN / UPDATE JADWAL ---
if(isset($_POST['simpan_jadwal']) && ($_SESSION['role'] == 'guru' || $_SESSION['role'] == 'admin')){
    $id_kelas = $_POST['id_kelas'];
    $hari = $_POST['hari'];
    $mulai = $_POST['jam_mulai'];
    $selesai = $_POST['jam_selesai'];

    // Validasi kepemilikan jika Guru
    if($_SESSION['role'] == 'guru'){
        $cek = mysqli_query($conn, "SELECT id FROM kelas WHERE id='$id_kelas' AND guru_id='$id_user'");
        if(mysqli_num_rows($cek) == 0){
            echo "<script>alert('Akses ditolak! Ini bukan kelas Anda.'); window.location='jadwal.php';</script>";
            exit();
        }
    }

    // Update Jadwal
    $query = "UPDATE kelas SET hari='$hari', jam_mulai='$mulai', jam_selesai='$selesai' WHERE id='$id_kelas'";
    
    if(mysqli_query($conn, $query)){
        echo "<script>alert('Jadwal berhasil disimpan!'); window.location='jadwal.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan jadwal.');</script>";
    }
}

// --- LOGIKA HAPUS / RESET JADWAL ---
if(isset($_POST['hapus_jadwal']) && ($_SESSION['role'] == 'guru' || $_SESSION['role'] == 'admin')){
    $id_kelas = $_POST['id_kelas'];

    // Validasi kepemilikan jika Guru
    if($_SESSION['role'] == 'guru'){
        $cek = mysqli_query($conn, "SELECT id FROM kelas WHERE id='$id_kelas' AND guru_id='$id_user'");
        if(mysqli_num_rows($cek) == 0){
            echo "<script>alert('Akses ditolak!'); window.location='jadwal.php';</script>";
            exit();
        }
    }

    // Reset kolom jadwal menjadi NULL/Kosong
    $query = "UPDATE kelas SET hari=NULL, jam_mulai=NULL, jam_selesai=NULL WHERE id='$id_kelas'";
    
    if(mysqli_query($conn, $query)){
        echo "<script>alert('Jadwal berhasil direset!'); window.location='jadwal.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus jadwal.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Jadwal Mentora</title>
    <!-- META TAG RESPONSIVE -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Skema Warna Mentora */
        body { font-family: 'Segoe UI', sans-serif; background: #EAF3FF; margin: 0; }
        .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; min-height: 80vh; }
        
        /* Style Tabel */
        .table-box { 
            background: white; 
            border-radius: 12px; 
            box-shadow: 0 4px 15px rgba(10, 31, 68, 0.05); 
            padding: 25px; 
            border-top: 5px solid #0A1F44; /* Navy Dark Border */
        }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #0A1F44; color: white; font-weight: 600; } /* Header Tabel Navy */
        tr:hover { background-color: #f9f9f9; }
        
        /* Style Panel Pengaturan */
        .control-panel { 
            background: white; 
            border: 1px solid #EAF3FF; 
            padding: 30px; 
            border-radius: 12px; 
            margin-bottom: 30px; 
            box-shadow: 0 4px 15px rgba(10, 31, 68, 0.05); 
            border-left: 6px solid #1F75FE; /* Biru Modern Border */
        }
        .control-panel h3 { margin-top: 0; color: #0A1F44; margin-bottom: 20px; font-weight: 700; }
        .control-panel p { color: #3C4A5A; margin-bottom: 25px; }
        
        /* Form Elements */
        .form-group { margin-bottom: 20px; }
        .form-label { font-weight: bold; display: block; margin-bottom: 8px; color: #3C4A5A; font-size: 14px; }
        
        select, input[type="time"] { 
            width: 100%; padding: 12px; 
            border: 1px solid #ccc; border-radius: 8px; 
            font-family: inherit; box-sizing: border-box; font-size: 14px;
            background: #F8F9FA;
        }
        select:focus, input:focus { border-color: #1F75FE; outline: none; background: white; }
        
        .form-row { display: flex; gap: 20px; }
        .form-col { flex: 1; }
        
        /* Tombol */
        .btn-group { display: flex; gap: 10px; margin-top: 25px; }
        
        .btn-save { 
            flex: 2; 
            background: #1F75FE; /* Biru Modern */
            color: white; border: none; padding: 14px; border-radius: 8px; 
            cursor: pointer; font-weight: bold; font-size: 16px; transition: 0.3s; 
        }
        .btn-save:hover { background: #0A1F44; } /* Hover jadi Navy */
        
        .btn-delete { 
            flex: 1; 
            background: white; 
            color: #dc3545; border: 1px solid #dc3545; 
            padding: 14px; border-radius: 8px; 
            cursor: pointer; font-weight: bold; font-size: 16px; transition: 0.3s; 
        }
        .btn-delete:hover { background: #dc3545; color: white; }

        /* --- MEDIA QUERY KHUSUS MOBILE (HP) --- */
        @media (max-width: 768px) {
            .container { padding: 15px; margin-top: 20px; }
            .control-panel { padding: 20px; border-left-width: 5px; }
            
            /* Stack form ke bawah di HP */
            .form-row { flex-direction: column; gap: 15px; }
            
            /* Perbesar Input di HP */
            select, input[type="time"] { 
                padding: 15px; /* Lebih tebal */
                font-size: 16px; /* Teks lebih besar */
                border-radius: 10px;
            }
            .form-label { font-size: 15px; margin-bottom: 6px; }
            .btn-save, .btn-delete { padding: 15px; font-size: 16px; }

            /* --- TABEL VERTIKAL (KARTU) --- */
            .table-box { padding: 15px; background: transparent; box-shadow: none; border-top: none; }
            
            /* Sembunyikan Header Asli */
            .table-box thead { display: none; }
            
            /* Baris menjadi Kartu */
            .table-box tr {
                display: block;
                margin-bottom: 15px;
                border: 1px solid #eee;
                border-radius: 12px;
                background: #fff;
                padding: 15px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            }
            
            /* Sel menjadi baris-baris di dalam kartu */
            .table-box td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: right;
                padding: 10px 0;
                border-bottom: 1px solid #f5f5f5;
                font-size: 14px;
            }
            
            .table-box td:last-child { border-bottom: none; }
            
            /* Label Header Semu (dari atribut data-label) */
            .table-box td::before {
                content: attr(data-label);
                font-weight: 700;
                color: #0A1F44;
                text-transform: uppercase;
                font-size: 12px;
                margin-right: 15px;
                text-align: left;
            }
        }
    </style>
</head>
<body>

    <!-- HEADER: Admin Header vs Navbar Biasa -->
    <?php 
    if($_SESSION['role'] == 'admin') {
        echo '<div style="padding:15px 30px; background:#0A1F44; display:flex; justify-content:space-between; align-items:center; color:white; box-shadow: 0 4px 10px rgba(10, 31, 68, 0.2);">
                <h3 style="margin:0; font-weight:700; letter-spacing:1px; font-size:18px;">⚙️ Kelola Jadwal</h3>
                <a href="index.php" style="text-decoration:none; font-weight:bold; color:white; border:1px solid white; padding:6px 12px; border-radius:6px; font-size:12px;">&laquo; Kembali</a>
              </div>';
    } else {
        include 'navbar.php'; 
    }
    ?>

    <div class="container">
        
        <!-- AREA PENGATURAN JADWAL (Form Dropdown) -->
        <?php if($_SESSION['role'] == 'guru' || $_SESSION['role'] == 'admin') { ?>
            <div class="control-panel">
                <h3>⚙️ Atur / Update Jadwal</h3>
                <p>Pilih kelas, lalu tentukan jadwal baru.</p>
                
                <form method="POST">
                    <!-- BAGIAN 1: PILIH KELAS (DROPDOWN) -->
                    <div class="form-group">
                        <label class="form-label">Pilih Kelas</label>
                        <select name="id_kelas" required>
                            <option value="">-- Klik untuk Memilih Kelas --</option>
                            <?php
                            // Query Dropdown
                            if($_SESSION['role'] == 'admin'){
                                $q_control = mysqli_query($conn, "SELECT k.*, u.nama_lengkap as guru FROM kelas k JOIN users u ON k.guru_id = u.id ORDER BY k.nama_kelas ASC");
                            } else {
                                $q_control = mysqli_query($conn, "SELECT * FROM kelas WHERE guru_id='$id_user' ORDER BY nama_kelas ASC");
                            }
                            
                            while($kg = mysqli_fetch_assoc($q_control)){
                                $label = $kg['nama_kelas'];
                                if($_SESSION['role'] == 'admin') $label .= " (Guru: " . $kg['guru'] . ")";
                                
                                $info_jadwal = (!empty($kg['hari'])) ? " [Saat ini: {$kg['hari']}, " . substr($kg['jam_mulai'],0,5) . "]" : " [Belum diatur]";
                                
                                echo "<option value='{$kg['id']}'>$label $info_jadwal</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- BAGIAN 2: ATUR WAKTU -->
                    <div class="form-row">
                        <div class="form-col">
                            <label class="form-label">Pilih Hari</label>
                            <select name="hari">
                                <option value="">- Pilih Hari -</option>
                                <?php 
                                $days = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
                                foreach($days as $d){
                                    echo "<option value='$d'>$d</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-col">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" name="jam_mulai">
                        </div>
                        <div class="form-col">
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" name="jam_selesai">
                        </div>
                    </div>

                    <!-- BAGIAN 3: TOMBOL AKSI -->
                    <div class="btn-group">
                        <button type="submit" name="simpan_jadwal" class="btn-save">Simpan Jadwal</button>
                        <button type="submit" name="hapus_jadwal" class="btn-delete" onclick="return confirm('Yakin ingin mereset jadwal kelas ini menjadi kosong?');">Reset Jadwal</button>
                    </div>
                </form>
            </div>
        <?php } ?>

        <!-- TABEL JADWAL DI BAWAH -->
        <div class="table-box">
            <h2 style="margin-top:0; padding-bottom:15px; color:#0A1F44; font-size:20px;">📅 Jadwal Pelajaran Aktif</h2>
            
            <?php if($_SESSION['role'] != 'siswa') { ?>
                <p style="font-size:13px; color:#3C4A5A; margin-bottom:20px;">*Tabel ini menampilkan hasil pengaturan jadwal yang telah tersimpan.</p>
            <?php } ?>

            <table>
                <thead>
                    <tr>
                        <th width="15%">Hari</th>
                        <th width="20%">Waktu</th>
                        <th width="35%">Mata Kuliah / Kelas</th>
                        <th>Pengajar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Logic Query Tampilan Tabel
                    if($_SESSION['role'] == 'siswa'){
                        $q = mysqli_query($conn, "SELECT k.*, u.nama_lengkap as guru FROM kelas k JOIN kelas_siswa ks ON k.id = ks.kelas_id JOIN users u ON k.guru_id = u.id WHERE ks.siswa_id='$id_user' ORDER BY FIELD(k.hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'), k.jam_mulai ASC");
                    } elseif($_SESSION['role'] == 'guru') {
                        $q = mysqli_query($conn, "SELECT k.*, u.nama_lengkap as guru FROM kelas k JOIN users u ON k.guru_id = u.id WHERE k.guru_id='$id_user' ORDER BY FIELD(k.hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'), k.jam_mulai ASC");
                    } else {
                        $q = mysqli_query($conn, "SELECT k.*, u.nama_lengkap as guru FROM kelas k JOIN users u ON k.guru_id = u.id ORDER BY FIELD(k.hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'), k.jam_mulai ASC");
                    }

                    if(mysqli_num_rows($q) == 0) echo "<tr><td colspan='4' align='center' style='color:#3C4A5A; padding:30px;'>Belum ada jadwal yang aktif.</td></tr>";

                    while($row = mysqli_fetch_assoc($q)){
                        $waktu = (!empty($row['jam_mulai'])) ? substr($row['jam_mulai'],0,5) . " - " . substr($row['jam_selesai'],0,5) : "-";
                        $hari = (!empty($row['hari'])) ? $row['hari'] : "-";
                    ?>
                        <tr>
                            <td data-label="Hari" style="font-weight:bold; color:#1F75FE;"><?= $hari; ?></td>
                            <td data-label="Waktu"><?= $waktu; ?></td>
                            <td data-label="Mata Kuliah / Kelas" style="font-weight:500; color:#3C4A5A;"><?= $row['nama_kelas']; ?></td>
                            <td data-label="Pengajar" style="color:#3C4A5A;"><?= $row['guru']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>