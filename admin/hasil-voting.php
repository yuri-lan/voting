<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// --- LOGIKA EXPORT EXCEL ---
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Rekap_Suara_Kakang_Senduk_" . date('Ymd') . ".xls");
    
    echo "<html><head><meta charset='UTF-8'></head><body>";
    echo "<h3>Rekap Perolehan Suara Kakang Senduk Ponorogo</h3>";
    echo "<p>Diunduh pada: " . date('d M Y, H:i:s') . " WIB</p>";
    
    // Tabel Kakang
    echo "<h4>Klasemen Kakang</h4>";
    echo "<table border='1' cellpadding='5' cellspacing='0'>
            <tr><th>Rank</th><th>Nomor Peserta</th><th>Nama</th><th>Asal Instansi</th><th>Total Suara</th></tr>";
    $q_k = mysqli_query($koneksi, "SELECT k.nomor_peserta, k.nama, k.asal_instansi, COUNT(v.id) AS total_suara FROM kontestan k LEFT JOIN voting v ON k.id = v.kontestan_id AND v.kategori = 'Kakang' WHERE k.kategori = 'Kakang' GROUP BY k.id ORDER BY total_suara DESC");
    $rank = 1;
    while($d = mysqli_fetch_assoc($q_k)) {
        echo "<tr><td>{$rank}</td><td>{$d['nomor_peserta']}</td><td>{$d['nama']}</td><td>{$d['asal_instansi']}</td><td>{$d['total_suara']}</td></tr>";
        $rank++;
    }
    echo "</table><br>";

    // Tabel Senduk
    echo "<h4>Klasemen Senduk</h4>";
    echo "<table border='1' cellpadding='5' cellspacing='0'>
            <tr><th>Rank</th><th>Nomor Peserta</th><th>Nama</th><th>Asal Instansi</th><th>Total Suara</th></tr>";
    $q_s = mysqli_query($koneksi, "SELECT k.nomor_peserta, k.nama, k.asal_instansi, COUNT(v.id) AS total_suara FROM kontestan k LEFT JOIN voting v ON k.id = v.kontestan_id AND v.kategori = 'Senduk' WHERE k.kategori = 'Senduk' GROUP BY k.id ORDER BY total_suara DESC");
    $rank = 1;
    while($d = mysqli_fetch_assoc($q_s)) {
        echo "<tr><td>{$rank}</td><td>{$d['nomor_peserta']}</td><td>{$d['nama']}</td><td>{$d['asal_instansi']}</td><td>{$d['total_suara']}</td></tr>";
        $rank++;
    }
    echo "</table>";
    echo "</body></html>";
    exit;
}

// --- QUERY DATA UNTUK TAMPILAN WEB ---
$q_total_kakang = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM voting WHERE kategori = 'Kakang'");
$total_kakang = mysqli_fetch_assoc($q_total_kakang)['total'];

$q_total_senduk = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM voting WHERE kategori = 'Senduk'");
$total_senduk = mysqli_fetch_assoc($q_total_senduk)['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Voting - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #4e54c8; --primary-light: #8f94fb; --bg-color: #f0f2f5; --sidebar-bg: #1e1e2f; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-color); color: #333; overflow-x: hidden; }
        
        /* SIDEBAR & MAIN */
        .sidebar { height: 100vh; width: 260px; position: fixed; top: 0; left: 0; background-color: var(--sidebar-bg); color: #fff; transition: 0.3s; box-shadow: 4px 0 15px rgba(0,0,0,0.1); z-index: 1000; }
        .sidebar .brand { padding: 20px; text-align: center; font-size: 20px; font-weight: 700; background: linear-gradient(45deg, var(--primary), var(--primary-light)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; letter-spacing: 1px; }
        .sidebar .nav-link { color: #a0a5b1; padding: 15px 25px; font-size: 15px; font-weight: 500; display: flex; align-items: center; transition: 0.3s; border-left: 4px solid transparent; text-decoration: none; }
        .sidebar .nav-link i { width: 30px; font-size: 18px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: linear-gradient(90deg, rgba(78,84,200,0.1) 0%, rgba(78,84,200,0) 100%); color: #fff; border-left: 4px solid var(--primary-light); }
        .main-content { margin-left: 260px; padding: 20px 30px; }
        
        /* TOPBAR & CARDS */
        .topbar { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border-radius: 15px; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 30px; }
        .card-custom { background: #fff; border-radius: 20px; padding: 25px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.04); }
        
        /* TAB & TABLE */
        .nav-tabs .nav-link { border: none; color: #666; font-weight: 600; padding: 15px 25px; border-radius: 10px 10px 0 0; }
        .nav-tabs .nav-link.active { color: var(--primary); border-bottom: 3px solid var(--primary); background: transparent; }
        .table img { border-radius: 10px; object-fit: cover; }
        .progress { height: 8px; border-radius: 10px; background-color: #e9ecef; }
        .rank-1 { background-color: #ffc107; color: #000; font-weight: bold; }
        .rank-2 { background-color: #c0c0c0; color: #000; font-weight: bold; }
        .rank-3 { background-color: #cd7f32; color: #fff; font-weight: bold; }

        /* --- CSS KHUSUS UNTUK PRINT / PDF --- */
        @media print {
            body { background-color: white !important; color: black !important; }
            .sidebar, .topbar, .btn-action-group, footer, .nav-tabs { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; }
            .card-custom { box-shadow: none !important; border: 1px solid #ddd !important; padding: 0 !important; }
            .table { color: black !important; }
            .table th { background-color: #f8f9fa !important; color: black !important; }
            .text-white, .text-secondary, .text-muted { color: black !important; }
            .progress { border: 1px solid #ddd; }
            .progress-bar { background-color: #333 !important; }
            .tab-content > .tab-pane { display: block !important; opacity: 1 !important; page-break-inside: avoid; margin-bottom: 30px; }
            #print-title { display: block !important; text-align: center; margin-bottom: 30px; }
        }
        #print-title { display: none; }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="brand"><i class="fa-solid fa-crown text-warning me-2"></i> Kakang Senduk</div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fa-solid fa-border-all"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="kontestan.php"><i class="fa-solid fa-users"></i> Data Kontestan</a></li>
            <li class="nav-item"><a class="nav-link" href="pemilih.php"><i class="fa-solid fa-user-check"></i> Data Pemilih</a></li>
            <li class="nav-item"><a class="nav-link" href="periode.php"><i class="fa-regular fa-calendar-days"></i> Periode Voting</a></li>
            <li class="nav-item"><a class="nav-link active" href="hasil-voting.php"><i class="fa-solid fa-chart-simple"></i> Hasil Voting</a></li>
            <li class="nav-item mt-5"><a class="nav-link text-danger btn-logout" href="../auth/logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="topbar">
            <h5 class="mb-0 fw-bold">Hasil Pemilihan</h5>
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

        <!-- HEADER & TOMBOL EXPORT -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h3 class="fw-bold mb-0">Rekap Perolehan Suara</h3>
            <div class="btn-action-group">
                <a href="hasil-voting.php?export=excel" class="btn btn-success px-4 py-2" style="border-radius: 10px;">
                    <i class="fa-solid fa-file-excel me-2"></i> Export Excel
                </a>
                <button onclick="window.print()" class="btn btn-danger px-4 py-2" style="border-radius: 10px;">
                    <i class="fa-solid fa-file-pdf me-2"></i> Cetak PDF
                </button>
            </div>
        </div>

        <!-- JUDUL KHUSUS PRINT -->
        <div id="print-title">
            <h2>Hasil Pemilihan Kakang Senduk Ponorogo</h2>
            <p>Tanggal Cetak: <?= date('d M Y, H:i:s') ?> WIB</p>
            <hr>
        </div>

        <!-- INFO TOTAL SUARA -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card-custom d-flex align-items-center justify-content-between border-start border-4 border-primary">
                    <div>
                        <h6 class="text-muted mb-1">Total Suara Masuk (Kakang)</h6>
                        <h2 class="fw-bold mb-0 text-primary"><?= $total_kakang ?> <span class="fs-6 text-muted fw-normal">suara</span></h2>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fa-solid fa-mars fs-3"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-custom d-flex align-items-center justify-content-between border-start border-4 border-danger">
                    <div>
                        <h6 class="text-muted mb-1">Total Suara Masuk (Senduk)</h6>
                        <h2 class="fw-bold mb-0 text-danger"><?= $total_senduk ?> <span class="fs-6 text-muted fw-normal">suara</span></h2>
                    </div>
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fa-solid fa-venus fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABEL KLASEMEN -->
        <div class="card-custom">
            <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="kakang-tab" data-bs-toggle="tab" data-bs-target="#kakang" type="button" role="tab"><i class="fa-solid fa-mars me-2"></i> Klasemen Kakang</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="senduk-tab" data-bs-toggle="tab" data-bs-target="#senduk" type="button" role="tab"><i class="fa-solid fa-venus me-2"></i> Klasemen Senduk</button>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent">
                <!-- TAB KAKANG -->
                <div class="tab-pane fade show active" id="kakang" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">Rank</th>
                                    <th width="10%">Foto</th>
                                    <th>No. Peserta & Nama</th>
                                    <th width="25%">Perolehan Suara</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $rank = 1;
                                $query_kakang = mysqli_query($koneksi, "
                                    SELECT k.id, k.nomor_peserta, k.nama, k.foto, COUNT(v.id) AS total_suara
                                    FROM kontestan k
                                    LEFT JOIN voting v ON k.id = v.kontestan_id AND v.kategori = 'Kakang'
                                    WHERE k.kategori = 'Kakang'
                                    GROUP BY k.id
                                    ORDER BY total_suara DESC, k.nomor_peserta ASC
                                ");
                                
                                while($row = mysqli_fetch_assoc($query_kakang)) {
                                    $persen = ($total_kakang > 0) ? ($row['total_suara'] / $total_kakang) * 100 : 0;
                                    $foto = ($row['foto'] && file_exists("../uploads/".$row['foto'])) ? "../uploads/".$row['foto'] : "https://via.placeholder.com/50?text=No+Img";
                                    
                                    $rank_class = "";
                                    if($rank == 1) $rank_class = "rank-1";
                                    elseif($rank == 2) $rank_class = "rank-2";
                                    elseif($rank == 3) $rank_class = "rank-3";
                                ?>
                                <tr>
                                    <td><span class="badge <?= $rank_class ?> fs-6 rounded-circle p-2" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;"><?= $rank++ ?></span></td>
                                    <td><img src="<?= $foto ?>" width="50" height="50"></td>
                                    <td>
                                        <span class="badge bg-secondary mb-1"><?= $row['nomor_peserta'] ?></span><br>
                                        <span class="fw-bold fs-6"><?= $row['nama'] ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="fw-bold"><?= $row['total_suara'] ?> Suara</span>
                                            <span class="text-muted"><?= number_format($persen, 1) ?>%</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $persen ?>%"></div>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB SENDuk -->
                <div class="tab-pane fade" id="senduk" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">Rank</th>
                                    <th width="10%">Foto</th>
                                    <th>No. Peserta & Nama</th>
                                    <th width="25%">Perolehan Suara</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $rank = 1;
                                $query_senduk = mysqli_query($koneksi, "
                                    SELECT k.id, k.nomor_peserta, k.nama, k.foto, COUNT(v.id) AS total_suara
                                    FROM kontestan k
                                    LEFT JOIN voting v ON k.id = v.kontestan_id AND v.kategori = 'Senduk'
                                    WHERE k.kategori = 'Senduk'
                                    GROUP BY k.id
                                    ORDER BY total_suara DESC, k.nomor_peserta ASC
                                ");
                                
                                while($row = mysqli_fetch_assoc($query_senduk)) {
                                    $persen = ($total_senduk > 0) ? ($row['total_suara'] / $total_senduk) * 100 : 0;
                                    $foto = ($row['foto'] && file_exists("../uploads/".$row['foto'])) ? "../uploads/".$row['foto'] : "https://via.placeholder.com/50?text=No+Img";
                                    
                                    $rank_class = "";
                                    if($rank == 1) $rank_class = "rank-1";
                                    elseif($rank == 2) $rank_class = "rank-2";
                                    elseif($rank == 3) $rank_class = "rank-3";
                                ?>
                                <tr>
                                    <td><span class="badge <?= $rank_class ?> fs-6 rounded-circle p-2" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;"><?= $rank++ ?></span></td>
                                    <td><img src="<?= $foto ?>" width="50" height="50"></td>
                                    <td>
                                        <span class="badge bg-secondary mb-1"><?= $row['nomor_peserta'] ?></span><br>
                                        <span class="fw-bold fs-6"><?= $row['nama'] ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="fw-bold"><?= $row['total_suara'] ?> Suara</span>
                                            <span class="text-muted"><?= number_format($persen, 1) ?>%</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar bg-danger progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $persen ?>%"></div>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="text-end mt-4">
                <small class="text-muted"><i class="fa-solid fa-rotate me-1"></i> Terakhir diperbarui: <?= date('d M Y, H:i:s') ?> WIB</small>
            </div>
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