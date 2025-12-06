<?php
$host = "localhost";
$user = "root";
$pass = "";        // Laragon defaultnya kosong
$db   = "db_elearning";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>