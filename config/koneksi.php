<?php
// ============================================
// File: config/koneksi.php
// Koneksi database db_voting_kakang_senduk
// ============================================

$host     = "localhost";
$user     = "root";       // default XAMPP
$password = "";           // default XAMPP kosong
$database = "db_voting_kakang_senduk";

$koneksi = mysqli_connect($host, $user, $password, $database);

// Cek koneksi
if (!$koneksi) {
    die("❌ Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset biar aman dari karakter aneh
mysqli_set_charset($koneksi, "utf8mb4");

// Uncomment baris ini buat tes koneksi (nanti hapus lagi)
// echo "✅ Koneksi database berhasil!";