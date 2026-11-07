<?php
session_start();
require_once '../config/koneksi.php';

// 1. Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// 2. Cek Metode Request (Harus POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/voting.php");
    exit;
}

$user_id   = $_SESSION['user_id'];
$kakang_id = isset($_POST['kakang_id']) ? (int)$_POST['kakang_id'] : 0;
$senduk_id = isset($_POST['senduk_id']) ? (int)$_POST['senduk_id'] : 0;
$waktu     = date('Y-m-d H:i:s');

// 3. Cek Status Periode Voting
$q_periode = mysqli_query($koneksi, "SELECT status FROM periode_voting ORDER BY id DESC LIMIT 1");
$periode = mysqli_fetch_assoc($q_periode);
if (!$periode || $periode['status'] !== 'Aktif') {
    echo "<script>alert('Gagal! Periode voting sudah ditutup.'); window.location.href='../pages/voting.php';</script>";
    exit;
}

// 4. Cek apakah user sudah pernah vote (validasi server-side)
$q_cek = mysqli_query($koneksi, "SELECT kategori FROM voting WHERE user_id = '$user_id'");
$sudah_vote = [];
while ($row = mysqli_fetch_assoc($q_cek)) {
    $sudah_vote[] = $row['kategori'];
}

if (in_array('Kakang', $sudah_vote) && in_array('Senduk', $sudah_vote)) {
    echo "<script>alert('Anda sudah memberikan suara untuk kedua kategori!'); window.location.href='../pages/klasemen.php';</script>";
    exit;
}

// 5. Mulai Transaksi Database (Penting!)
mysqli_begin_transaction($koneksi);

try {
    // Simpan Suara Kakang
    if ($kakang_id > 0 && !in_array('Kakang', $sudah_vote)) {
        // Pastikan ID yang dipilih valid dan memang kategori Kakang
        $cek_k = mysqli_query($koneksi, "SELECT id FROM kontestan WHERE id = '$kakang_id' AND kategori = 'Kakang'");
        if (mysqli_num_rows($cek_k) == 0) throw new Exception("Kontestan Kakang tidak valid.");
        
        $insert_k = "INSERT INTO voting (user_id, kontestan_id, kategori, created_at) VALUES ('$user_id', '$kakang_id', 'Kakang', '$waktu')";
        if (!mysqli_query($koneksi, $insert_k)) throw new Exception("Gagal menyimpan suara Kakang.");
    }

    // Simpan Suara Senduk
    if ($senduk_id > 0 && !in_array('Senduk', $sudah_vote)) {
        // Pastikan ID yang dipilih valid dan memang kategori Senduk
        $cek_s = mysqli_query($koneksi, "SELECT id FROM kontestan WHERE id = '$senduk_id' AND kategori = 'Senduk'");
        if (mysqli_num_rows($cek_s) == 0) throw new Exception("Kontestan Senduk tidak valid.");

        $insert_s = "INSERT INTO voting (user_id, kontestan_id, kategori, created_at) VALUES ('$user_id', '$senduk_id', 'Senduk', '$waktu')";
        if (!mysqli_query($koneksi, $insert_s)) throw new Exception("Gagal menyimpan suara Senduk.");
    }

    // Kalau semua lancar, Commit (Simpan permanen)
    mysqli_commit($koneksi);
    
        // Redirect langsung ke klasemen dengan status sukses
    header("Location: ../pages/klasemen.php?pesan=sukses");
    
} catch (Exception $e) {
    // Kalau ada error, Batalkan semua (Rollback)
    mysqli_rollback($koneksi);
    $error_msg = $e->getMessage();
    echo "<script>alert('Error Sistem: $error_msg'); window.location.href='../pages/voting.php';</script>";
    exit;
}
?>