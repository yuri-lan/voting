<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$pesan = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor_peserta = mysqli_real_escape_string($koneksi, $_POST['nomor_peserta']);
    $nama          = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $kategori      = $_POST['kategori'];
    $asal_instansi = mysqli_real_escape_string($koneksi, $_POST['asal_instansi']);
    $tempat_lahir  = mysqli_real_escape_string($koneksi, $_POST['tempat_lahir']);
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $visi          = mysqli_real_escape_string($koneksi, $_POST['visi']);
    $misi          = mysqli_real_escape_string($koneksi, $_POST['misi']);
    $prestasi      = mysqli_real_escape_string($koneksi, $_POST['prestasi']);
    $deskripsi     = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    
    $nama_file = "";
    if ($_FILES['foto']['name'] != "") {
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $nama_file = time() . "_" . basename($_FILES['foto']['name']);
            move_uploaded_file($_FILES['foto']['tmp_name'], "../uploads/" . $nama_file);
        }
    }

    $query = "INSERT INTO kontestan (nomor_peserta, nama, kategori, foto, asal_instansi, tempat_lahir, tanggal_lahir, visi, misi, prestasi, deskripsi) 
              VALUES ('$nomor_peserta', '$nama', '$kategori', '$nama_file', '$asal_instansi', '$tempat_lahir', '$tanggal_lahir', '$visi', '$misi', '$prestasi', '$deskripsi')";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: kontestan.php?pesan=tambah");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kontestan - Admin</title>
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
        .form-label { font-size: 14px; color: #555; }
        .form-control, .form-select { border-radius: 10px; padding: 10px 15px; border: 1px solid #e0e0e0; }
        .form-control:focus, .form-select:focus { border-color: var(--primary-light); box-shadow: 0 0 0 0.2rem rgba(143,148,251,0.25); }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="brand"><i class="fa-solid fa-crown text-warning me-2"></i> Kakang Senduk</div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fa-solid fa-border-all"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link active" href="kontestan.php"><i class="fa-solid fa-users"></i> Data Kontestan</a></li>
            <li class="nav-item"><a class="nav-link" href="pemilih.php"><i class="fa-solid fa-user-check"></i> Data Pemilih</a></li>
            <li class="nav-item"><a class="nav-link" href="periode.php"><i class="fa-regular fa-calendar-days"></i> Periode Voting</a></li>
            <li class="nav-item"><a class="nav-link" href="hasil-voting.php"><i class="fa-solid fa-chart-simple"></i> Hasil Voting</a></li>
            <li class="nav-item mt-5"><a class="nav-link text-danger btn-logout" href="../auth/logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="topbar">
            <h5 class="mb-0 fw-bold">Tambah Data</h5>
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

        <div class="d-flex align-items-center mb-4">
            <a href="kontestan.php" class="btn btn-light border me-3" style="border-radius: 10px;"><i class="fa-solid fa-arrow-left"></i></a>
            <h4 class="fw-bold mb-0">Tambah Kontestan Baru</h4>
        </div>

        <div class="card-custom">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nomor Peserta</label>
                        <input type="text" name="nomor_peserta" class="form-control" placeholder="Contoh: K-04" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Kategori</label>
                        <select name="kategori" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Kakang">Kakang</option>
                            <option value="Senduk">Senduk</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Asal Instansi</label>
                        <input type="text" name="asal_instansi" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Upload Foto</label>
                        <input type="file" name="foto" class="form-control" accept="image/jpeg, image/png">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Visi</label>
                        <textarea name="visi" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Misi</label>
                        <textarea name="misi" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Prestasi</label>
                        <textarea name="prestasi" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Deskripsi Singkat</label>
                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <hr class="my-4">
                <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius: 10px;"><i class="fa-solid fa-floppy-disk me-2"></i> Simpan Data</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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