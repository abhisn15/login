<?php
$servername = "localhost"; // Ganti dengan nama host MySQL Anda
$username = "root"; // Ganti dengan username MySQL Anda
$password = ""; // Ganti dengan password MySQL Anda
$dbname = "db_captcha"; // Ganti dengan nama database Anda

// Buat koneksi ke database
$koneksi = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($koneksi->connect_error) {
    die("koneksi failed: " . $koneksi->connect_error);
}
?>
