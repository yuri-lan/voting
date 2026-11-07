<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$query_periode = mysqli_query($koneksi, "SELECT * FROM periode_voting ORDER BY id DESC LIMIT 1");
$data = mysqli_fetch_assoc($query_periode);

if (!$data) {
    mysqli_query($koneksi, "INSERT INTO periode_voting (nama_periode, tanggal_mulai, tanggal_selesai, status) VALUES ('Pemilihan Kakang Senduk', NOW(), NOW(), 'Nonaktif')");
    header("Location: periode.php");
    exit;
}

$pesan = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_periode = mysqli_real_escape_string($koneksi, $_POST['nama_periode']);
    $tanggal_mulai = str_replace('T', ' ', $_POST['tanggal_mulai']);
    $tanggal_selesai = str_replace('T', ' ', $_POST['tanggal_selesai']);
    $status = $_POST['status'];
    $id_periode = $data['id'];
    
    $query_update = "UPDATE periode_voting SET nama_periode = '$nama_periode', tanggal_mulai = '$tanggal_mulai', tanggal_selesai = '$tanggal_selesai', status = '$status' WHERE id = '$id_periode'";
                     
    if (mysqli_query($koneksi, $query_update)) {
        header("Location: periode.php?pesan=update");
        exit;
    } else {
        $pesan = "<div class='alert alert-danger'>Gagal update: " . mysqli_error($koneksi) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Periode Voting - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #4e54c8; --primary-light: #8f94fb; --bg-color: #f0f2f5; --sidebar-bg: #1e1e2f; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-color); color: #333; overflow-x: hidden; }
        .sidebar { height: 100vh; width: 260px; position: fixed; top: 0; left: 0; background-color: var(--sidebar-bg); color: #fff; transition: 0.3s; box-shadow: 4px 0 15px rgba(0,0,0,0.1); z-index: 1000; }
        .sidebar .brand { padding: 20px; text-align: center; font-size: 20px; font-weight: 700; background: linear-gradient(45deg, var(--primary), var(--primary-light)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; letter-spacing: 1px; }
        .sidebar .nav-link { color: #a0a5b1; padding: 15px 25px; font-size: 15px; font-weight: 500; display: flex; align-items: center; transition: 0.3s; border-left: 4px solid transparent; text-decoration: none; }
        .sidebar .nav-link i { width: 30px; font-size: 18px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: linear-gradient(90deg, rgba(78,84,200,0.1) 0%, rgba(78,84,200,0) 100%); color: #fff; border-left: 4px solid var(--primary-light); }
        .main-content { margin-left: 260px; padding: 20px 30px; }
        .topbar { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border-radius: 15px; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 30px; }
        .card-custom { background: #fff; border-radius: 20px; padding: 30px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.04); }
        input[type="datetime-local"] { padding: 10px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="brand"><i class="fa-solid fa-crown text-warning me-2"></i> Kakang Senduk</div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fa-solid fa-border-all"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="kontestan.php"><i class="fa-solid fa-users"></i> Data Kontestan</a></li>
            <li class="nav-item"><a class="nav-link" href="pemilih.php"><i class="fa-solid fa-user-check"></i> Data Pemilih</a></li>
            <li class="nav-item"><a class="nav-link active" href="periode.php"><i class="fa-regular fa-calendar-days"></i> Periode Voting</a></li>
            <li class="nav-item"><a class="nav-link" href="hasil-voting.php"><i class="fa-solid fa-chart-simple"></i> Hasil Voting</a></li>
            <li class="nav-item mt-5"><a class="nav-link text-danger btn-logout" href="../auth/logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="topbar">
            <h5 class="mb-0 fw-bold">Pengaturan Waktu</h5>
            <div class="d-flex align-items-center">
                <div class="me-3 text-end d-none d-md-block">
                    <div class="fw-bold fs-6"><?= $_SESSION['nama']; ?></div>
                    <small class="text-muted">Administrator</small>
                </div>
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                    <i class="fa-solid fa-user-shield text-white"></i>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card-custom">
                    <?= $pesan; ?>
                    <?php if($data['status'] == 'Aktif'): ?>
                        <div class="alert alert-success d-flex align-items-center border-0 shadow-sm mb-4" role="alert" style="border-radius: 10px;">
                            <i class="fa-solid fa-circle-check fs-4 me-3"></i>
                            <div><h6 class="alert-heading fw-bold mb-1">Voting Sedang AKTIF</h6><span class="small">Pemilih dapat memberikan suara.</span></div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger d-flex align-items-center border-0 shadow-sm mb-4" role="alert" style="border-radius: 10px;">
                            <i class="fa-solid fa-circle-xmark fs-4 me-3"></i>
                            <div><h6 class="alert-heading fw-bold mb-1">Voting DITUTUP</h6><span class="small">Pemilih tidak dapat mengakses voting.</span></div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Nama Periode</label>
                            <input type="text" name="nama_periode" class="form-control" value="<?= $data['nama_periode'] ?>" required>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Waktu Mulai</label>
                                <input type="datetime-local" name="tanggal_mulai" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($data['tanggal_mulai'])) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Waktu Selesai</label>
                                <input type="datetime-local" name="tanggal_selesai" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($data['tanggal_selesai'])) ?>" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Status Voting</label>
                            <select name="status" class="form-select">
                                <option value="Aktif" <?= $data['status'] == 'Aktif' ? 'selected' : '' ?>>Aktif (Buka Voting)</option>
                                <option value="Nonaktif" <?= $data['status'] == 'Nonaktif' ? 'selected' : '' ?>>Nonaktif (Tutup Voting)</option>
                            </select>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary px-4 py-2 w-100" style="border-radius: 10px;"><i class="fa-solid fa-floppy-disk me-2"></i> Simpan Pengaturan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('pesan') === 'update') {
            Swal.fire({ title: 'Berhasil Disimpan!', text: 'Pengaturan periode telah diperbarui.', icon: 'success', confirmButtonColor: '#4e54c8', timer: 3000, showConfirmButton: false });
        }

        // Script Logout
        document.querySelectorAll('.btn-logout').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                Swal.fire({
                    title: 'Yakin mau logout?', text: "Kamu harus login lagi untuk masuk!", icon: 'question', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6c757d', confirmButtonText: '<i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Ya, Logout!', cancelButtonText: 'Batal', customClass: { confirmButton: 'btn btn-danger mx-2', cancelButton: 'btn btn-secondary mx-2' }, buttonsStyling: false
                }).then((result) => { if (result.isConfirmed) { window.location.href = href; } });
            });
        });
    </script>
</body>
</html>