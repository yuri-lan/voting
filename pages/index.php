<?php
session_start();
require_once '../config/koneksi.php';

// Ambil status periode voting
$q_periode = mysqli_query($koneksi, "SELECT * FROM periode_voting ORDER BY id DESC LIMIT 1");
$periode = mysqli_fetch_assoc($q_periode);
$status_voting = $periode ? $periode['status'] : 'Nonaktif';
$nama_periode = $periode ? $periode['nama_periode'] : 'Pemilihan Kakang Senduk';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Pemilihan Kakang Senduk Ponorogo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --primary: #00d2ff; 
            --secondary: #3a7bd5; 
            --bg-dark: #0f172a; 
            --text-light: #e2e8f0;
        }
        
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: var(--bg-dark); 
            color: var(--text-light); 
            overflow-x: hidden; 
        }

        /* =========================================
           ANIMASI BINTANG KELAP-KELIP
           ========================================= */
        .stars-bg {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            z-index: -1;
            background: linear-gradient(to bottom, #0b1120, #0f172a);
        }
        
        .stars, .stars2 {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background-repeat: repeat;
        }

        .stars {
            background-image: 
                radial-gradient(1.5px 1.5px at 20px 30px, #fff, rgba(0,0,0,0)),
                radial-gradient(1.5px 1.5px at 40px 70px, #fff, rgba(0,0,0,0)),
                radial-gradient(1.5px 1.5px at 50px 160px, #fff, rgba(0,0,0,0)),
                radial-gradient(1.5px 1.5px at 90px 40px, #fff, rgba(0,0,0,0));
            background-size: 200px 200px;
            animation: twinkle 4s infinite alternate;
        }

        .stars2 {
            background-image: 
                radial-gradient(2px 2px at 10px 10px, var(--primary), rgba(0,0,0,0)),
                radial-gradient(2px 2px at 150px 150px, #fff, rgba(0,0,0,0));
            background-size: 300px 300px;
            animation: twinkle 6s infinite alternate-reverse;
            opacity: 0.6;
        }

        @keyframes twinkle {
            0% { opacity: 0.2; transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1.1); }
        }

        /* =========================================
           NAVBAR DARK GLASS
           ========================================= */
        .navbar { 
            background: rgba(15, 23, 42, 0.8); 
            backdrop-filter: blur(15px); 
            border-bottom: 1px solid rgba(255,255,255,0.05); 
            padding: 15px 0; 
            transition: 0.3s; 
        }
        .navbar-brand { font-weight: 700; font-size: 22px; color: white !important; }
        .nav-link { font-weight: 500; color: #94a3b8 !important; margin: 0 10px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { color: var(--primary) !important; text-shadow: 0 0 10px rgba(0,210,255,0.5); }

        /* =========================================
           HERO SECTION
           ========================================= */
        .hero { padding: 150px 0 100px 0; position: relative; z-index: 1; }
        .hero h1 { font-size: 3.5rem; font-weight: 800; margin-bottom: 20px; line-height: 1.2; color: #fff; text-shadow: 0 0 20px rgba(0, 210, 255, 0.4); }
        .hero p { font-size: 1.2rem; color: #94a3b8; margin-bottom: 40px; max-width: 700px; margin-left: auto; margin-right: auto; }
        
        /* BUTTONS */
        .btn-custom { padding: 14px 35px; font-weight: 600; border-radius: 50px; font-size: 16px; transition: 0.3s; }
        .btn-glow { background: var(--primary); color: #0f172a; border: none; box-shadow: 0 0 15px rgba(0, 210, 255, 0.4); }
        .btn-glow:hover { background: #fff; color: #0f172a; box-shadow: 0 0 25px rgba(0, 210, 255, 0.8); transform: translateY(-3px); }
        .btn-outline-custom { background: transparent; color: white; border: 2px solid rgba(255,255,255,0.2); }
        .btn-outline-custom:hover { background: white; color: #0f172a; border-color: white; transform: translateY(-3px); }

        /* =========================================
           KARTU KACA & EFEK REFLEKTIF (SHINE)
           ========================================= */
        .glass-card { 
            background: rgba(30, 41, 59, 0.4); 
            backdrop-filter: blur(10px); 
            border: 1px solid rgba(255, 255, 255, 0.05); 
            border-radius: 20px; 
            padding: 35px 25px; 
            position: relative; 
            overflow: hidden; 
            transition: all 0.4s ease; 
            height: 100%; 
            text-align: center; 
        }
        
        .glass-card:hover { 
            transform: translateY(-10px); 
            border-color: rgba(0, 210, 255, 0.4); 
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4), 0 0 20px rgba(0, 210, 255, 0.1); 
        }

        /* Ini dia efek Reflektif/Kilau-nya */
        .glass-card::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 50%; height: 100%;
            background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0) 100%);
            transform: skewX(-25deg);
            transition: 0.7s;
        }

        .glass-card:hover::before { left: 150%; }

        .icon-box { 
            width: 65px; height: 65px; 
            margin: 0 auto 20px; 
            display: flex; justify-content: center; align-items: center; 
            border-radius: 15px; 
            font-size: 26px; 
        }

        .bg-glow-blue { background: rgba(0, 210, 255, 0.1); color: var(--primary); box-shadow: 0 0 15px rgba(0,210,255,0.2); }
        .bg-glow-green { background: rgba(56, 239, 125, 0.1); color: #38ef7d; box-shadow: 0 0 15px rgba(56,239,125,0.2); }
        .bg-glow-orange { background: rgba(245, 175, 25, 0.1); color: #f5af19; box-shadow: 0 0 15px rgba(245,175,25,0.2); }

        /* FOOTER */
        footer { background: rgba(15, 23, 42, 0.9); color: #64748b; padding: 30px 0; margin-top: 50px; border-top: 1px solid rgba(255,255,255,0.05); text-align: center; }
    </style>
</head>
<body>

    <!-- BACKGROUND BINTANG -->
    <div class="stars-bg">
        <div class="stars"></div>
        <div class="stars2"></div>
    </div>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fa-solid fa-crown text-warning me-2"></i> Kakang Senduk</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="kontestan.php">Kontestan</a></li>
                    <li class="nav-item"><a class="nav-link" href="klasemen.php">Klasemen</a></li>
                </ul>
                <div class="d-flex align-items-center">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <?php if($_SESSION['role'] === 'admin'): ?>
                            <a href="../admin/dashboard.php" class="btn btn-outline-info btn-sm rounded-pill px-3 me-3">Dashboard Admin</a>
                        <?php endif; ?>
                        <span class="me-3 fw-bold text-white d-none d-lg-inline">Halo, <?= $_SESSION['nama'] ?></span>
                        <a href="../auth/logout.php" class="btn btn-danger btn-sm rounded-pill px-3 btn-logout"><i class="fa-solid fa-right-from-bracket me-1"></i> Logout</a>
                    <?php else: ?>
                        <a href="../auth/login.php" class="btn btn-outline-custom rounded-pill px-4 me-2 fw-bold btn-sm">Login</a>
                        <a href="../auth/register.php" class="btn btn-glow rounded-pill px-4 fw-bold btn-sm">Daftar</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="container text-center position-relative">
            <div class="mb-4">
                <?php if($status_voting == 'Aktif'): ?>
                    <span class="badge bg-success px-4 py-2 rounded-pill fs-6 shadow"><i class="fa-solid fa-circle-check me-2"></i> Voting Sedang Dibuka!</span>
                <?php else: ?>
                    <span class="badge bg-danger px-4 py-2 rounded-pill fs-6 shadow"><i class="fa-solid fa-circle-xmark me-2"></i> Voting Ditutup</span>
                <?php endif; ?>
            </div>
            <h1><?= $nama_periode ?></h1>
            <p>Dukung putra-putri terbaik Ponorogo untuk menjadi Duta Wisata. Berikan suaramu dan tentukan siapa yang akan menjadi Kakang dan Senduk pilihanmu!</p>
            
            <div class="mt-4 d-flex justify-content-center gap-3 flex-wrap">
                <?php if($status_voting == 'Aktif'): ?>
                    <a href="voting.php" class="btn btn-custom btn-glow"><i class="fa-solid fa-check-to-slot me-2"></i> Voting Sekarang</a>
                <?php endif; ?>
                <a href="kontestan.php" class="btn btn-custom btn-outline-custom"><i class="fa-solid fa-users me-2"></i> Lihat Kontestan</a>
                <a href="klasemen.php" class="btn btn-custom btn-outline-custom"><i class="fa-solid fa-trophy me-2"></i> Lihat Klasemen</a>
            </div>
        </div>
    </section>

    <!-- INFO SECTION -->
    <section class="container" style="margin-top: 20px; position: relative; z-index: 10;">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="glass-card">
                    <div class="icon-box bg-glow-blue">
                        <i class="fa-solid fa-check-to-slot"></i>
                    </div>
                    <h5 class="fw-bold text-white">Voting Aman</h5>
                    <p class="text-secondary mb-0">Satu akun hanya bisa memberikan satu suara untuk masing-masing kategori Kakang dan Senduk.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card">
                    <div class="icon-box bg-glow-green">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <h5 class="fw-bold text-white">Hasil Real-time</h5>
                    <p class="text-secondary mb-0">Pantau perolehan suara secara langsung melalui halaman klasemen yang diperbarui secara otomatis.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card">
                    <div class="icon-box bg-glow-orange">
                        <i class="fa-regular fa-calendar-check"></i>
                    </div>
                    <h5 class="fw-bold text-white">Jadwal Transparan</h5>
                    <p class="text-secondary mb-0">Batas waktu voting ditentukan oleh admin. Pastikan suaramu masuk sebelum periode berakhir.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="container">
            <h5 class="fw-bold mb-3 text-white">Pemilihan Kakang Senduk Ponorogo</h5>
            <p class="mb-0">&copy; <?= date('Y'); ?> Panitia Pemilihan. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.btn-logout').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                Swal.fire({
                    title: 'Yakin mau logout?', text: "Kamu harus login lagi untuk melakukan voting!", icon: 'question', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6c757d', confirmButtonText: '<i class="fa-solid fa-right-from-bracket me-1"></i> Ya, Logout!', cancelButtonText: 'Batal', customClass: { confirmButton: 'btn btn-danger mx-2', cancelButton: 'btn btn-secondary mx-2' }, buttonsStyling: false
                }).then((result) => { if (result.isConfirmed) { window.location.href = href; } });
            });
        });
    </script>
</body>
</html>