<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    $q_foto = mysqli_query($koneksi, "SELECT foto FROM kontestan WHERE id = '$id_hapus'");
    $d_foto = mysqli_fetch_assoc($q_foto);
    if ($d_foto && $d_foto['foto'] != "" && file_exists("../uploads/" . $d_foto['foto'])) {
        unlink("../uploads/" . $d_foto['foto']);
    }
    mysqli_query($koneksi, "DELETE FROM kontestan WHERE id = '$id_hapus'");
    header("Location: kontestan.php?pesan=hapus");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Kontestan - Admin</title>
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
        .card-custom { background: #fff; border-radius: 20px; padding: 25px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.04); }
        .table img { border-radius: 10px; object-fit: cover; }
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
            <h5 class="mb-0 fw-bold">Manajemen Kontestan</h5>
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

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Daftar Kontestan</h4>
            <a href="tambah-kontestan.php" class="btn btn-primary px-4 py-2" style="border-radius: 10px;">
                <i class="fa-solid fa-plus me-2"></i> Tambah Kontestan
            </a>
        </div>

        <div class="card-custom">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>No. Peserta</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Asal Instansi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($koneksi, "SELECT * FROM kontestan ORDER BY kategori ASC, nomor_peserta ASC");
                        while ($data = mysqli_fetch_assoc($query)) {
                            $foto = ($data['foto'] && file_exists("../uploads/".$data['foto'])) ? "../uploads/".$data['foto'] : "https://via.placeholder.com/50?text=No+Img";
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><img src="<?= $foto ?>" width="50" height="50" alt="Foto"></td>
                            <td><span class="badge bg-secondary"><?= $data['nomor_peserta'] ?></span></td>
                            <td class="fw-bold"><?= $data['nama'] ?></td>
                            <td>
                                <?php if($data['kategori'] == 'Kakang'): ?>
                                    <span class="badge bg-primary">Kakang</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Senduk</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $data['asal_instansi'] ?></td>
                            <td>
                                <a href="edit-kontestan.php?id=<?= $data['id'] ?>" class="btn btn-warning btn-sm text-white"><i class="fa-solid fa-pen"></i></a>
                                <a href="kontestan.php?hapus=<?= $data['id'] ?>" class="btn btn-danger btn-sm btn-hapus"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const pesan = urlParams.get('pesan');
        if (pesan === 'tambah') { Swal.fire({ title: 'Berhasil Ditambahkan!', text: 'Data baru telah masuk.', icon: 'success', confirmButtonColor: '#4e54c8', timer: 3000, showConfirmButton: false }); }
        else if (pesan === 'edit') { Swal.fire({ title: 'Berhasil Diperbarui!', text: 'Data telah disimpan.', icon: 'success', confirmButtonColor: '#4e54c8', timer: 3000, showConfirmButton: false }); }
        else if (pesan === 'hapus') { Swal.fire({ title: 'Terhapus!', text: 'Data telah dihapus.', icon: 'success', confirmButtonColor: '#4e54c8', timer: 3000, showConfirmButton: false }); }

        document.querySelectorAll('.btn-hapus').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                Swal.fire({
                    title: 'Yakin mau hapus?', text: "Data tidak bisa dikembalikan!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6c757d', confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal', customClass: { confirmButton: 'btn btn-danger mx-2', cancelButton: 'btn btn-secondary mx-2' }, buttonsStyling: false
                }).then((result) => { if (result.isConfirmed) { window.location.href = href; } });
            });
        });

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