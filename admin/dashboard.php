<?php
session_start();
require_once '../config/koneksi.php';

// Proteksi: Kalau belum login ATAU bukan admin, tendang!
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// --- QUERY STATISTIK DASHBOARD ---
// 1. Total Kontestan
$q_kontestan = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM kontestan");
$total_kontestan = mysqli_fetch_assoc($q_kontestan)['total'];

// 2. Total Pemilih
$q_pemilih = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM users WHERE role = 'pemilih'");
$total_pemilih = mysqli_fetch_assoc($q_pemilih)['total'];

// 3. Total Suara Kakang
$q_suara_kakang = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM voting WHERE kategori = 'Kakang'");
$total_suara_kakang = mysqli_fetch_assoc($q_suara_kakang)['total'];

// 4. Total Suara Senduk
$q_suara_senduk = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM voting WHERE kategori = 'Senduk'");
$total_suara_senduk = mysqli_fetch_assoc($q_suara_senduk)['total'];

// --- QUERY DATA UNTUK GRAFIK ---
// Ambil perolehan suara tiap kontestan Kakang
$q_grafik_kakang = mysqli_query($koneksi, "
    SELECT k.nama, COUNT(v.id) AS total_suara 
    FROM kontestan k 
    LEFT JOIN voting v ON k.id = v.kontestan_id AND v.kategori = 'Kakang' 
    WHERE k.kategori = 'Kakang' 
    GROUP BY k.id ORDER BY total_suara DESC
");
$nama_kakang = [];
$suara_kakang = [];
while($row = mysqli_fetch_assoc($q_grafik_kakang)) {
    $nama_kakang[] = $row['nama'];
    $suara_kakang[] = $row['total_suara'];
}

// Ambil perolehan suara tiap kontestan Senduk
$q_grafik_senduk = mysqli_query($koneksi, "
    SELECT k.nama, COUNT(v.id) AS total_suara 
    FROM kontestan k 
    LEFT JOIN voting v ON k.id = v.kontestan_id AND v.kategori = 'Senduk' 
    WHERE k.kategori = 'Senduk' 
    GROUP BY k.id ORDER BY total_suara DESC
");
$nama_senduk = [];
$suara_senduk = [];
while($row = mysqli_fetch_assoc($q_grafik_senduk)) {
    $nama_senduk[] = $row['nama'];
    $suara_senduk[] = $row['total_suara'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Kakang Senduk</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #4e54c8;
            --primary-light: #8f94fb;
            --bg-color: #f0f2f5;
            --sidebar-bg: #1e1e2f;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            color: #333;
            overflow-x: hidden;
        }

        /* --- SIDEBAR --- */
        .sidebar {
            height: 100vh;
            width: 260px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--sidebar-bg);
            color: #fff;
            transition: 0.3s;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .sidebar .brand {
            padding: 20px;
            text-align: center;
            font-size: 20px;
            font-weight: 700;
            background: linear-gradient(45deg, var(--primary), var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 1px;
        }

        .sidebar .nav-link {
            color: #a0a5b1;
            padding: 15px 25px;
            font-size: 15px;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: 0.3s;
            border-left: 4px solid transparent;
            text-decoration: none;
        }

        .sidebar .nav-link i {
            width: 30px;
            font-size: 18px;
        }

        .sidebar .nav-link:hover, 
        .sidebar .nav-link.active {
            background: linear-gradient(90deg, rgba(78,84,200,0.1) 0%, rgba(78,84,200,0) 100%);
            color: #fff;
            border-left: 4px solid var(--primary-light);
        }

        /* --- MAIN CONTENT --- */
        .main-content {
            margin-left: 260px;
            padding: 20px 30px;
            transition: 0.3s;
        }

        /* Topbar */
        .topbar {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            margin-bottom: 30px;
        }

        /* Stat Cards */
        .stat-card {
            background: #fff;
            border-radius: 20px;
            padding: 25px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.04);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(78,84,200,0.1);
        }

        .icon-wrapper {
            width: 55px;
            height: 55px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .bg-gradient-blue { background: linear-gradient(135deg, #4e54c8, #8f94fb); color: white; }
        .bg-gradient-green { background: linear-gradient(135deg, #11998e, #38ef7d); color: white; }
        .bg-gradient-orange { background: linear-gradient(135deg, #f12711, #f5af19); color: white; }
        .bg-gradient-pink { background: linear-gradient(135deg, #ff0844, #ffb199); color: white; }

        .stat-title { font-size: 14px; color: #888; font-weight: 500; margin-bottom: 5px; }
        .stat-number { font-size: 28px; font-weight: 700; color: #222; margin: 0; }

        /* Info Panel */
        .info-panel {
            background: #fff;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.04);
            border: none;
            height: 100%;
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div class="brand">
            <i class="fa-solid fa-crown text-warning me-2"></i> Kakang Senduk
        </div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link active" href="dashboard.php"><i class="fa-solid fa-border-all"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="kontestan.php"><i class="fa-solid fa-users"></i> Data Kontestan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="pemilih.php"><i class="fa-solid fa-user-check"></i> Data Pemilih</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="periode.php"><i class="fa-regular fa-calendar-days"></i> Periode Voting</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="hasil-voting.php"><i class="fa-solid fa-chart-simple"></i> Hasil Voting</a>
            </li>
            <li class="nav-item mt-5">
                <a class="nav-link text-danger btn-logout" href="../auth/logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
            </li>
        </ul>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        
        <!-- TOPBAR -->
        <div class="topbar">
            <h5 class="mb-0 fw-bold">Dashboard Overview</h5>
            <div class="d-flex align-items-center">
                <div class="me-3 text-end d-none d-md-block">
                    <div class="fw-bold fs-6"><?= $_SESSION['nama']; ?></div>
                    <small class="text-muted">Administrator</small>
                </div>
                <div class="bg-gradient-blue rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                    <i class="fa-solid fa-user-shield text-white"></i>
                </div>
            </div>
        </div>

        <!-- WELCOME BANNER -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; background: linear-gradient(135deg, #4e54c8, #8f94fb); color: white;">
            <div class="card-body p-4 position-relative overflow-hidden">
                <h3 class="fw-bold mb-2">Halo, <?= $_SESSION['nama']; ?>! 👋</h3>
                <p class="mb-0 opacity-75">Selamat datang kembali. Pantau jalannya pemilihan Kakang Senduk hari ini.</p>
                <i class="fa-solid fa-crown position-absolute" style="font-size: 100px; right: 20px; bottom: -20px; opacity: 0.2;"></i>
            </div>
        </div>

        <!-- STATISTIK CARDS (ANGKA DINAMIS) -->
        <div class="row g-4 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <div class="icon-wrapper bg-gradient-blue"><i class="fa-solid fa-users"></i></div>
                    <div class="stat-title">Total Kontestan</div>
                    <div class="stat-number"><?= $total_kontestan ?></div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <div class="icon-wrapper bg-gradient-green"><i class="fa-solid fa-user-check"></i></div>
                    <div class="stat-title">Total Pemilih</div>
                    <div class="stat-number"><?= $total_pemilih ?></div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <div class="icon-wrapper bg-gradient-orange"><i class="fa-solid fa-mars"></i></div>
                    <div class="stat-title">Suara Kakang</div>
                    <div class="stat-number"><?= $total_suara_kakang ?></div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <div class="icon-wrapper bg-gradient-pink"><i class="fa-solid fa-venus"></i></div>
                    <div class="stat-title">Suara Senduk</div>
                    <div class="stat-number"><?= $total_suara_senduk ?></div>
                </div>
            </div>
        </div>

        <!-- CHARTS & INFO -->
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="info-panel">
                    <h6 class="fw-bold mb-4"><i class="fa-solid fa-chart-pie text-primary me-2"></i> Grafik Perolehan Suara</h6>
                    
                    <!-- TABS UNTUK GRAFIK -->
                    <ul class="nav nav-tabs mb-4" id="grafikTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="grafik-kakang-tab" data-bs-toggle="tab" data-bs-target="#grafik-kakang" type="button" role="tab">Grafik Kakang</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="grafik-senduk-tab" data-bs-toggle="tab" data-bs-target="#grafik-senduk" type="button" role="tab">Grafik Senduk</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="grafikTabContent">
                        <!-- CHART KAKANG -->
                        <div class="tab-pane fade show active" id="grafik-kakang" role="tabpanel">
                            <canvas id="chartKakang" style="max-height: 300px;"></canvas>
                        </div>
                        <!-- CHART SENDuk -->
                        <div class="tab-pane fade" id="grafik-senduk" role="tabpanel">
                            <canvas id="chartSenduk" style="max-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="info-panel">
                    <h6 class="fw-bold mb-4"><i class="fa-solid fa-clock text-success me-2"></i> Status Periode</h6>
                    <div class="alert alert-success bg-success bg-opacity-10 border-0 text-success text-center py-4" style="border-radius: 15px;">
                        <i class="fa-solid fa-check-circle fs-1 mb-2"></i>
                        <h5 class="fw-bold mb-1">Voting Aktif!</h5>
                        <small>Pantau terus klasemen</small>
                    </div>
                    <a href="../pages/index.php" target="_blank" class="btn btn-outline-primary w-100 mt-3 py-2" style="border-radius: 10px;">
                        <i class="fa-solid fa-eye me-2"></i> Lihat Tampilan User
                    </a>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- LIBRARY CHART.JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // --- KONFIGURASI GRAFIK KAKANG ---
        const ctxKakang = document.getElementById('chartKakang').getContext('2d');
        const chartKakang = new Chart(ctxKakang, {
            type: 'bar',
            data: {
                labels: <?= json_encode($nama_kakang) ?>,
                datasets: [{
                    label: 'Jumlah Suara',
                    data: <?= json_encode($suara_kakang) ?>,
                    backgroundColor: 'rgba(78, 84, 200, 0.7)',
                    borderColor: 'rgba(78, 84, 200, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

        // --- KONFIGURASI GRAFIK SENDuk ---
        const ctxSenduk = document.getElementById('chartSenduk').getContext('2d');
        const chartSenduk = new Chart(ctxSenduk, {
            type: 'bar',
            data: {
                labels: <?= json_encode($nama_senduk) ?>,
                datasets: [{
                    label: 'Jumlah Suara',
                    data: <?= json_encode($suara_senduk) ?>,
                    backgroundColor: 'rgba(255, 8, 68, 0.7)',
                    borderColor: 'rgba(255, 8, 68, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    </script>
    
    <!-- SWEETALERT LOGOUT -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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