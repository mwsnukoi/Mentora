<?php
// --- LOGIKA SMART FOOTER ---

// 1. Cek apakah user adalah ADMIN? Jika ya, sembunyikan footer.
if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    return; // Stop, jangan tampilkan apa-apa ke bawah
}

// 2. Cek halaman apa yang sedang dibuka?
$halaman_saat_ini = basename($_SERVER['PHP_SELF']);

// 3. Daftar halaman yang TIDAK BOLEH ada footer (Quiz & Ujian)
$halaman_tanpa_footer = [
    'quiz.php', 
    'kerjakan_quiz.php', 
    'atur_soal.php'
];

// Jika halaman saat ini ada di daftar blacklist, sembunyikan footer
if(in_array($halaman_saat_ini, $halaman_tanpa_footer)) {
    return; // Stop
}
?>

<style>
    /* CSS Footer Mentora */
    .footer {
        background-color: #0A1F44; /* Navy Dark */
        color: white;
        padding: 40px 0 30px;
        margin-top: 80px;
        font-family: 'Segoe UI', sans-serif;
        text-align: center;
        font-size: 14px;
        border-top: 5px solid #1F75FE; /* Aksen garis biru */
    }
    
    .footer-content {
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }

    .footer-brand {
        font-weight: 800;
        font-size: 22px;
        letter-spacing: 1px;
        margin-bottom: 5px;
    }
    .footer-brand span { color: #1F75FE; }

    .footer-links {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .footer-links a {
        color: #EAF3FF;
        text-decoration: none;
        font-weight: 500;
        transition: 0.2s;
        padding-bottom: 2px;
        border-bottom: 1px solid transparent;
    }
    .footer-links a:hover { 
        color: white; 
        border-bottom: 1px solid #1F75FE; 
    }

    .copyright {
        color: #8898aa;
        font-size: 13px;
        margin-top: 20px;
        line-height: 1.6;
    }

    /* --- KHUSUS MOBILE (HP) --- */
    @media (max-width: 768px) {
        /* Sembunyikan Link Navigasi di Footer HP */
        .footer-links {
            display: none;
        }
        
        /* Sesuaikan jarak agar tetap rapi */
        .footer {
            padding: 30px 0;
            margin-top: 40px;
        }
        .copyright {
            margin-top: 5px;
            font-size: 12px;
        }
    }
</style>

<div class="footer">
    <div class="footer-content">
        <!-- Brand (Tetap Muncul) -->
        <div class="footer-brand">Mentora<span>.</span></div>
        
        <!-- Navigasi Footer (Akan Hilang di HP) -->
        <div class="footer-links">
            <a href="index.php">Dashboard</a>
            <a href="jadwal.php">Jadwal Akademik</a>
            <a href="pengumuman.php">Pengumuman</a>
            
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] != 'admin') { ?>
                <a href="lihat_nilai.php">Nilai</a>
            <?php } ?>
        </div>

        <!-- Copyright (Tetap Muncul) -->
        <div class="copyright">
            &copy; <?= date('Y'); ?> Mentora Learning Management System.<br>
            Dikembangkan untuk kemajuan pendidikan.
        </div>
    </div>
</div>