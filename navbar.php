<?php
// navbar.php
// Pastikan session sudah dimulai di file induk
$id_nav = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 0;
$q_nav = mysqli_query($conn, "SELECT * FROM users WHERE id='$id_nav'");
$d_nav = mysqli_fetch_assoc($q_nav);

$foto_profil = 'https://via.placeholder.com/40'; 
if(isset($d_nav['foto']) && $d_nav['foto'] != 'default.png' && file_exists('uploads/'.$d_nav['foto'])){
    $foto_profil = 'uploads/'.$d_nav['foto'];
}
?>

<!-- META TAG WAJIB RESPONSIVE -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

<style>
    /* --- CSS NAVBAR PREMIUM & RESPONSIVE --- */
    
    /* 1. LOGIKA TAMPILAN (PENTING) */
    /* Secara default (Desktop), sembunyikan elemen khusus mobile */
    .mobile-only { display: none !important; }
    
    /* Wrapper Utama */
    .navbar-wrapper {
        background-color: #0A1F44; /* Navy Dark */
        border-bottom: 4px solid #1F75FE; /* Aksen Biru */
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 4px 20px rgba(10, 31, 68, 0.3);
    }

    .navbar-container {
        max-width: 1200px;
        margin: 0 auto;
        height: 70px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 20px;
        font-family: 'Segoe UI', sans-serif;
        position: relative;
    }

    /* LOGO BRAND */
    .nav-brand {
        font-size: 22px; font-weight: 900; color: #FFFFFF; text-decoration: none;
        letter-spacing: 0.5px; display: flex; align-items: center; gap: 5px; z-index: 1002;
    }
    .nav-brand span { color: #1F75FE; font-size: 30px; line-height: 0; }

    /* MENU TENGAH (Desktop) */
    .nav-menu { display: flex; gap: 5px; align-items: center; }

    .nav-link {
        text-decoration: none; color: #EAF3FF; font-weight: 600;
        padding: 8px 18px; border-radius: 30px;
        transition: all 0.3s; font-size: 14px; white-space: nowrap;
    }
    .nav-link:hover { background-color: #1F75FE; color: white; transform: translateY(-2px); }

    /* PROFIL KANAN (Desktop) */
    .nav-right { display: flex; align-items: center; gap: 15px; }

    .nav-profile-link {
        display: flex; align-items: center; gap: 10px; text-decoration: none;
        padding-left: 15px; border-left: 1px solid rgba(255,255,255,0.1);
    }

    .profile-info { text-align: right; line-height: 1.2; }
    .profile-name { font-weight: 700; font-size: 13px; display: block; color: white; }
    .profile-role { font-size: 10px; color: #1F75FE; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; }
    .profile-img { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 2px solid #1F75FE; }

    .btn-logout {
        color: #ff6b6b; font-weight: 700; text-decoration: none; font-size: 18px;
        margin-left: 5px; transition: 0.3s; display: flex; align-items: center;
    }
    .btn-logout:hover { color: white; transform: rotate(90deg); }

    /* HAMBURGER MENU (Mobile Only - Default Hidden) */
    .hamburger { display: none; cursor: pointer; flex-direction: column; gap: 5px; z-index: 1002; }
    .bar { width: 25px; height: 3px; background-color: white; border-radius: 5px; transition: 0.3s; }

    /* --- 2. MEDIA QUERIES KHUSUS HP (Max Width 900px) --- */
    @media (max-width: 900px) {
        /* Tampilkan elemen khusus mobile */
        .mobile-only { display: flex !important; }
        
        /* Sembunyikan profil desktop */
        .nav-right { display: none !important; }

        /* Tampilkan Hamburger */
        .hamburger { display: flex; }

        /* Sembunyikan Menu Desktop Default & Ubah jadi Dropdown */
        .nav-menu {
            position: absolute;
            top: 70px; left: 0; width: 100%;
            background-color: #0A1F44;
            flex-direction: column;
            align-items: flex-start;
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            box-shadow: 0 10px 10px rgba(0,0,0,0.2);
        }

        /* Ketika Menu Aktif (Diklik) */
        .navbar-wrapper.active .nav-menu {
            max-height: 500px; /* Beri ruang cukup */
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .nav-link {
            display: block; width: 100%; padding: 15px 30px;
            border-radius: 0; text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            box-sizing: border-box;
        }
        .nav-link:hover { background-color: #1F75FE; transform: none; }

        /* Style Hamburger Animasi */
        .navbar-wrapper.active .bar:nth-child(2) { opacity: 0; }
        .navbar-wrapper.active .bar:nth-child(1) { transform: translateY(8px) rotate(45deg); }
        .navbar-wrapper.active .bar:nth-child(3) { transform: translateY(-8px) rotate(-45deg); }
        
        /* Mobile Profile Item Style */
        .mobile-profile {
            align-items: center; gap: 15px;
            padding: 20px 30px; width: 100%;
            background: #081936; /* Sedikit lebih gelap */
            color: white; text-decoration: none;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            box-sizing: border-box;
        }
    }
</style>

<div class="navbar-wrapper" id="myNavbar">
    <div class="navbar-container">
        
        <!-- BRAND -->
        <a href="index.php" class="nav-brand">Mentora<span>.</span></a>

        <!-- HAMBURGER ICON (Hanya Muncul di HP) -->
        <div class="hamburger" onclick="toggleMenu()">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>

        <!-- MENU LINKS -->
        <div class="nav-menu" id="navMenu">
            
            <!-- PROFILE KHUSUS MOBILE (Hanya Muncul di HP karena class mobile-only) -->
            <a href="profil.php" class="mobile-profile mobile-only">
                <img src="<?= $foto_profil; ?>" style="width:40px; height:40px; border-radius:50%; object-fit:cover; border: 2px solid #1F75FE;">
                <div>
                    <div style="font-weight:bold; font-size:15px; color:white;">Profil Saya</div>
                    <div style="font-size:12px; opacity:0.7; color:#EAF3FF;">Edit Akun & Password</div>
                </div>
            </a>

            <a href="index.php" class="nav-link">Kelas</a>
            <a href="jadwal.php" class="nav-link">Jadwal</a>
            <a href="pengumuman.php" class="nav-link">Pengumuman</a>
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] != 'admin') { ?>
                <a href="lihat_nilai.php" class="nav-link">Nilai</a>
            <?php } ?>

            <a href="logout.php" class="nav-link mobile-only" style="color:#ff6b6b; font-weight:bold;">Keluar</a>
        </div>

        <!-- PROFIL KANAN (Hanya Muncul di Desktop karena class nav-right didisplay:none di mobile) -->
        <div class="nav-right">
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] != 'admin') { ?>
                <a href="profil.php" class="nav-profile-link" title="Edit Profil">
                    <div class="profile-info">
                        <span class="profile-name"><?= isset($d_nav['nama_lengkap']) ? substr($d_nav['nama_lengkap'], 0, 15) : 'User'; ?></span>
                        <span class="profile-role"><?= $_SESSION['role']; ?></span>
                    </div>
                    <img src="<?= $foto_profil; ?>" class="profile-img" alt="Profil">
                </a>
            <?php } else { ?>
                <div class="nav-profile-link">
                    <div class="profile-info"><span class="profile-name">Admin</span><span class="profile-role">PUSAT</span></div>
                    <div style="width:40px; height:40px; background:#1F75FE; color:white; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold;">A</div>
                </div>
            <?php } ?>
            <a href="logout.php" class="btn-logout" title="Keluar">&#10005;</a> 
        </div>

    </div>
</div>

<script>
    // Script untuk Toggle Menu di Mobile
    function toggleMenu() {
        const navbar = document.getElementById('myNavbar');
        navbar.classList.toggle('active');
    }
</script>