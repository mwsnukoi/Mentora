<?php
session_start();
include 'koneksi.php';

if(!isset($_GET['id'])) { header("location:index.php"); exit(); }
$kelas_id = $_GET['id'];
$id_user = $_SESSION['id_user'];

// --- LOGIKA HAPUS MATERI ---
if(isset($_GET['hapus_materi']) && $_SESSION['role'] == 'guru'){
    $id_materi = $_GET['hapus_materi'];
    $cek = mysqli_query($conn, "SELECT m.file_content, m.kelas_id FROM materi m JOIN kelas k ON m.kelas_id = k.id WHERE m.id='$id_materi' AND k.guru_id='$id_user'");
    if(mysqli_num_rows($cek) > 0){
        $d_mat = mysqli_fetch_assoc($cek);
        if(!empty($d_mat['file_content']) && file_exists("uploads/".$d_mat['file_content'])) unlink("uploads/".$d_mat['file_content']);
        mysqli_query($conn, "DELETE FROM komentar WHERE materi_id='$id_materi'");
        mysqli_query($conn, "DELETE FROM materi WHERE id='$id_materi'");
        echo "<script>alert('Materi berhasil dihapus.'); window.location='lihat_kelas.php?id=$kelas_id';</script>";
    }
}

// --- LOGIKA UPDATE KELAS ---
if(isset($_POST['update_kelas']) && $_SESSION['role'] == 'guru'){
    $nama_baru = mysqli_real_escape_string($conn, $_POST['nama_kelas']);
    $desk_baru = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    mysqli_query($conn, "UPDATE kelas SET nama_kelas='$nama_baru', deskripsi='$desk_baru' WHERE id='$kelas_id' AND guru_id='$id_user'");
    echo "<script>alert('Info kelas diperbarui!'); window.location='lihat_kelas.php?id=$kelas_id';</script>";
}

$q_kelas = mysqli_query($conn, "SELECT * FROM kelas WHERE id='$kelas_id'");
$d_kelas = mysqli_fetch_assoc($q_kelas);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $d_kelas['nama_kelas']; ?> - Mentora</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #EAF3FF; }
        .container { max-width: 1000px; margin: 30px auto; padding: 0 20px; padding-bottom: 100px; }
        
        /* TOMBOL KEMBALI */
        .btn-back {
            display: inline-flex; align-items: center; gap: 8px;
            text-decoration: none; color: #3C4A5A; font-weight: 700;
            font-size: 16px; margin-bottom: 15px; transition: 0.3s;
        }
        .btn-back:hover { color: #1F75FE; transform: translateX(-5px); }

        /* HEADER KELAS */
        .header-kelas { 
            background: #0A1F44; color: white; padding: 35px; 
            border-radius: 16px; margin-bottom: 25px; 
            box-shadow: 0 4px 15px rgba(10,31,68,0.2); position: relative;
        }
        .header-kelas h1 { margin: 0; font-size: 32px; font-weight: 800; }
        .header-kelas p { margin: 10px 0 0; opacity: 0.9; color: #EAF3FF; max-width: 80%; }
        
        /* Kode Kelas */
        .kode-badge { 
            background: rgba(255,255,255,0.1); padding: 6px 12px; 
            border-radius: 6px; font-family: monospace; margin-top: 15px; 
            display: inline-block; font-size: 14px; cursor: pointer; border: 1px solid rgba(255,255,255,0.2);
        }
        .kode-badge:hover { background: rgba(255,255,255,0.2); }
        
        .btn-edit-kelas { position: absolute; top: 35px; right: 35px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.3); padding: 8px 15px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: bold; cursor: pointer; transition: 0.2s; }
        .btn-edit-kelas:hover { background: #1F75FE; border-color: #1F75FE; }

        /* MENU */
        .class-menu { display: flex; gap: 15px; margin-bottom: 30px; border-bottom: 2px solid #ccc; padding-bottom: 15px; }
        .menu-item { text-decoration: none; color: #3C4A5A; padding: 10px 25px; border-radius: 8px; font-weight: 700; transition: 0.3s; background: white; border: 1px solid #ddd; }
        .menu-item:hover { background: #EAF3FF; color: #0A1F44; }
        .menu-item.active { background: #1F75FE; color: white; border-color: #1F75FE; }
        
        .btn-tambah { background: #1F75FE; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px; font-weight: bold; float: right; font-size: 14px; transition: 0.2s; }
        .btn-tambah:hover { background: #0A1F44; }

        /* MODAL */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(10, 31, 68, 0.8); z-index: 2000; display: none; justify-content: center; align-items: center; }
        .modal-box { background: white; padding: 30px; border-radius: 16px; width: 100%; max-width: 500px; position: relative; }
        .modal-title { margin: 0 0 20px; color: #0A1F44; font-size: 22px; font-weight: 800; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-weight: 700; color: #3C4A5A; margin-bottom: 8px; font-size: 14px; }
        .form-input, .form-textarea { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; font-family: inherit; font-size: 14px; box-sizing: border-box; }
        .btn-save-modal { background: #1F75FE; color: white; border: none; padding: 12px 25px; border-radius: 8px; font-weight: bold; cursor: pointer; width: 100%; }
        .btn-close-modal { position: absolute; top: 15px; right: 15px; cursor: pointer; font-size: 24px; color: #999; background: none; border: none; }

        /* --- DAFTAR MATERI (LAYOUT LIST BARU) --- */
        .bab-section { 
            background: #f8f9fa; 
            border-radius: 12px; 
            padding: 20px; 
            margin-bottom: 30px; 
            border: 1px solid #e0e0e0;
        }
        
        .bab-title { 
            font-size: 18px; font-weight: 800; color: #0A1F44; 
            margin-bottom: 15px; 
        }
        
        /* Item Materi (Kartu Memanjang) */
        .materi-item {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: 0.2s;
            border-left: 5px solid transparent;
            text-decoration: none;
        }
        .materi-item:hover { transform: translateX(5px); box-shadow: 0 5px 15px rgba(0,0,0,0.08); }

        /* Bagian Kiri: Badge & Judul */
        .materi-info { display: flex; align-items: center; gap: 15px; flex-grow: 1; }
        
        /* Badge Tipe File */
        .materi-badge {
            font-size: 11px; font-weight: 800; color: white;
            padding: 6px 10px; border-radius: 6px;
            text-transform: uppercase; letter-spacing: 0.5px;
            min-width: 60px; text-align: center;
        }
        .bg-youtube { background: #28a745; } /* Hijau seperti di screenshot */
        .bg-pdf { background: #dc3545; }     /* Merah */
        .bg-video { background: #1F75FE; }   /* Biru */
        .bg-ppt { background: #fd7e14; }     /* Orange */
        .bg-teks { background: #6c757d; }    /* Abu */

        .materi-text h4 { margin: 0; font-size: 16px; color: #0A1F44; font-weight: 700; }
        .materi-text p { margin: 4px 0 0; font-size: 13px; color: #666; }
        
        /* Bagian Kanan: Tanggal & Tombol Hapus */
        .materi-action { text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 8px; }
        .materi-date { font-size: 12px; color: #999; }
        
        .btn-hapus-item {
            color: #dc3545; font-size: 11px; font-weight: 800; 
            text-decoration: none; border: 1px solid #dc3545; 
            padding: 3px 8px; border-radius: 4px; 
            text-transform: uppercase; transition: 0.2s;
        }
        .btn-hapus-item:hover { background: #dc3545; color: white; }

        /* Siswa Keluar Kelas */
        .btn-leave-class { position: absolute; top: 35px; right: 35px; background: rgba(255, 107, 107, 0.1); color: #ff6b6b; border: 1px solid #ff6b6b; padding: 8px 15px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: bold; transition: 0.2s; }
        .btn-leave-class:hover { background: #ff6b6b; color: white; }

        .empty-state { text-align: center; padding: 40px; color: #888; font-style: italic; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <a href="index.php" class="btn-back"><span>&larr;</span> Kembali ke Dashboard</a>

        <div class="header-kelas">
            <h1><?= htmlspecialchars($d_kelas['nama_kelas']); ?></h1>
            <p><?= htmlspecialchars($d_kelas['deskripsi']); ?></p>
            <?php if($_SESSION['role'] != 'admin'){ ?>
                <div class="kode-badge" onclick="copyKode('<?= $d_kelas['kode_kelas']; ?>')">KODE: <?= $d_kelas['kode_kelas']; ?> 📋</div>
            <?php } ?>

            <?php if($_SESSION['role'] == 'guru') { ?>
                <button onclick="openModal()" class="btn-edit-kelas">✏️ Edit Info</button>
                <a href="tambah_materi.php?kelas_id=<?= $kelas_id; ?>" class="btn-tambah" style="margin-top: 20px; display: inline-block;">+ Upload Materi</a>
            <?php } ?>
            
            <?php if($_SESSION['role'] == 'siswa') { ?>
                <a href="lihat_kelas.php?id=<?= $kelas_id; ?>&keluar_kelas=<?= $kelas_id; ?>" class="btn-leave-class" onclick="return confirm('Yakin ingin keluar dari kelas ini?');">🚪 Keluar Kelas</a>
            <?php } ?>
        </div>

        <!-- MODAL -->
        <?php if($_SESSION['role'] == 'guru') { ?>
        <div class="modal-overlay" id="editModal">
            <div class="modal-box">
                <button class="btn-close-modal" onclick="closeModal()">&times;</button>
                <h2 class="modal-title">Edit Informasi Kelas</h2>
                <form method="POST">
                    <div class="form-group"><label class="form-label">Nama Kelas</label><input type="text" name="nama_kelas" class="form-input" value="<?= htmlspecialchars($d_kelas['nama_kelas']); ?>" required></div>
                    <div class="form-group"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-textarea" rows="4" required><?= htmlspecialchars($d_kelas['deskripsi']); ?></textarea></div>
                    <button type="submit" name="update_kelas" class="btn-save-modal">Simpan Perubahan</button>
                </form>
            </div>
        </div>
        <?php } ?>

        <div class="class-menu">
            <a href="lihat_kelas.php?id=<?= $kelas_id; ?>" class="menu-item active">📚 Materi</a>
            <a href="quiz.php" class="menu-item">🧠 Quiz & Ujian</a>
        </div>

        <!-- LIST MATERI (NEW LAYOUT) -->
        <?php
        $q_bab = mysqli_query($conn, "SELECT DISTINCT bab FROM materi WHERE kelas_id='$kelas_id' ORDER BY bab ASC");
        if(mysqli_num_rows($q_bab) == 0) echo "<div class='empty-state'>Belum ada materi.</div>";

        while($bab = mysqli_fetch_assoc($q_bab)){
            $nama_bab = $bab['bab'];
        ?>
            <div class="bab-section">
                <div class="bab-title"><?= htmlspecialchars($nama_bab); ?></div>
                
                <?php
                $q_materi = mysqli_query($conn, "SELECT * FROM materi WHERE kelas_id='$kelas_id' AND bab='$nama_bab' ORDER BY id ASC");
                while($m = mysqli_fetch_assoc($q_materi)){
                    // Tentukan Badge
                    $badge = "TEKS"; $bg = "bg-teks";
                    if($m['tipe'] == 'pdf') { $badge = "PDF"; $bg = "bg-pdf"; }
                    if($m['tipe'] == 'video') { $badge = "VIDEO"; $bg = "bg-video"; }
                    if($m['tipe'] == 'youtube') { $badge = "YOUTUBE"; $bg = "bg-youtube"; }
                    if($m['tipe'] == 'ppt') { $badge = "DOC"; $bg = "bg-ppt"; }
                ?>
                    <!-- Item Materi -->
                    <a href="baca_materi.php?id=<?= $m['id']; ?>" class="materi-item">
                        <div class="materi-info">
                            <span class="materi-badge <?= $bg; ?>"><?= $badge; ?></span>
                            <div class="materi-text">
                                <h4><?= htmlspecialchars($m['judul']); ?></h4>
                                <p>
                                    <?php 
                                    $desc = !empty($m['deskripsi']) ? $m['deskripsi'] : "Klik untuk membuka materi.";
                                    echo substr(htmlspecialchars($desc), 0, 80) . "..."; 
                                    ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="materi-action">
                            <span class="materi-date"><?= date('d M Y', strtotime($m['tanggal'])); ?></span>
                            <?php if($_SESSION['role'] == 'guru') { ?>
                                <!-- Tombol Hapus -->
                                <object>
                                    <a href="lihat_kelas.php?id=<?= $kelas_id; ?>&hapus_materi=<?= $m['id']; ?>" class="btn-hapus-item" onclick="return confirm('Hapus materi ini?');">HAPUS</a>
                                </object>
                            <?php } ?>
                        </div>
                    </a>
                <?php } ?>
            </div>
        <?php } ?>
    </div>

    <script>
        function openModal() { document.getElementById('editModal').style.display = 'flex'; }
        function closeModal() { document.getElementById('editModal').style.display = 'none'; }
        window.onclick = function(event) { if (event.target == document.getElementById('editModal')) closeModal(); }
        function copyKode(kode) { navigator.clipboard.writeText(kode).then(() => alert('Kode '+kode+' disalin!')); }
    </script>
</body>
</html>