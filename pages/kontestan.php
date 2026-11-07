<?php
session_start();
require_once '../config/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kontestan - Kakang Senduk</title>
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
            padding-top: 80px;
        }

        /* ANIMASI BINTANG */
        .stars-bg { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: -1; background: linear-gradient(to bottom, #0b1120, #0f172a); }
        .stars, .stars2 { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-repeat: repeat; }
        .stars { background-image: radial-gradient(1.5px 1.5px at 20px 30px, #fff, rgba(0,0,0,0)), radial-gradient(1.5px 1.5px at 40px 70px, #fff, rgba(0,0,0,0)), radial-gradient(1.5px 1.5px at 50px 160px, #fff, rgba(0,0,0,0)), radial-gradient(1.5px 1.5px at 90px 40px, #fff, rgba(0,0,0,0)); background-size: 200px 200px; animation: twinkle 4s infinite alternate; }
        .stars2 { background-image: radial-gradient(2px 2px at 10px 10px, var(--primary), rgba(0,0,0,0)), radial-gradient(2px 2px at 150px 150px, #fff, rgba(0,0,0,0)); background-size: 300px 300px; animation: twinkle 6s infinite alternate-reverse; opacity: 0.6; }
        @keyframes twinkle { 0% { opacity: 0.2; transform: scale(0.9); } 100% { opacity: 1; transform: scale(1.1); } }

        /* NAVBAR (Di-scope biar gak ganggu Tab) */
        .navbar { background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(15px); border-bottom: 1px solid rgba(255,255,255,0.05); padding: 15px 0; }
        .navbar-brand { font-weight: 700; font-size: 22px; color: white !important; }
        .navbar .nav-link { font-weight: 500; color: #94a3b8 !important; margin: 0 10px; transition: 0.3s; }
        .navbar .nav-link:hover, .navbar .nav-link.active { color: var(--primary) !important; text-shadow: 0 0 10px rgba(0,210,255,0.5); }

        /* TAB MENU (PILIHAN KAKANG / SENDuk) - DIPERBAIKI */
        .nav-pills .nav-link { 
            color: #94a3b8 !important; 
            border-radius: 50px; 
            padding: 10px 30px; 
            font-weight: 600; 
            transition: 0.3s; 
            border: 1px solid rgba(255,255,255,0.1); 
        }
        .nav-pills .nav-link.active { 
            background: var(--primary) !important; 
            color: #0f172a !important; 
            box-shadow: 0 0 15px rgba(0,210,255,0.4); 
            border-color: var(--primary) !important; 
        }

        /* KARTU KACA REFLEKTIF */
        .card-contestant { 
            background: rgba(30, 41, 59, 0.4); 
            backdrop-filter: blur(10px); 
            border: 1px solid rgba(255, 255, 255, 0.05); 
            border-radius: 20px; 
            overflow: hidden; 
            transition: all 0.4s ease; 
            position: relative;
            height: 100%;
        }
        
        .card-contestant:hover { 
            transform: translateY(-10px); 
            border-color: rgba(0, 210, 255, 0.4); 
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5), 0 0 20px rgba(0, 210, 255, 0.1); 
        }

        /* Efek Kilau (Shine) */
        .card-contestant::before {
            content: ''; position: absolute; top: 0; left: -100%; width: 50%; height: 100%;
            background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.1) 50%, rgba(255,255,255,0) 100%);
            transform: skewX(-25deg); transition: 0.7s; z-index: 2; pointer-events: none;
        }
        .card-contestant:hover::before { left: 150%; }

        /* Gambar Kontestan */
        .img-wrapper { position: relative; height: 280px; overflow: hidden; background: #1e293b; }
        .img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .card-contestant:hover .img-wrapper img { transform: scale(1.1); }
        
        .badge-number { position: absolute; top: 15px; right: 15px; background: var(--primary); color: #0f172a; font-weight: 800; font-size: 16px; padding: 8px 15px; border-radius: 50px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); z-index: 3; }
        
        /* Isi Kartu */
        .card-body-custom { padding: 20px; position: relative; z-index: 3; }
        .contestant-name { font-size: 1.2rem; font-weight: 700; color: white; margin-bottom: 5px; }
        .contestant-instansi { font-size: 0.85rem; color: var(--primary); margin-bottom: 15px; }
        .contestant-desc { font-size: 0.9rem; color: #94a3b8; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 20px; }

        /* Tombol */
        .btn-detail { background: transparent; border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 50px; font-size: 14px; transition: 0.3s; }
        .btn-detail:hover { background: white; color: #0f172a; }
        .btn-vote { background: var(--primary); color: #0f172a; border: none; border-radius: 50px; font-weight: 700; font-size: 14px; box-shadow: 0 0 10px rgba(0,210,255,0.3); transition: 0.3s; }
        .btn-vote:hover { background: #fff; box-shadow: 0 0 20px rgba(0,210,255,0.8); }

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
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link active" href="kontestan.php">Kontestan</a></li>
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
                        <a href="../auth/login.php" class="btn btn-outline-light rounded-pill px-4 me-2 fw-bold btn-sm" style="border-color: rgba(255,255,255,0.2);">Login</a>
                        <a href="../auth/register.php" class="btn btn-info rounded-pill px-4 fw-bold btn-sm" style="background: var(--primary); color: #0f172a; border: none;">Daftar</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- KONTEN UTAMA -->
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-white mb-2">Daftar Kontestan</h2>
            <p class="text-secondary">Kenali lebih dekat putra-putri terbaik yang akan menjadi Duta Wisata Ponorogo.</p>
        </div>

        <!-- TAB MENU -->
        <ul class="nav nav-pills justify-content-center mb-5" id="pills-tab" role="tablist">
            <li class="nav-item me-2" role="presentation">
                <button class="nav-link active" id="pills-kakang-tab" data-bs-toggle="pill" data-bs-target="#pills-kakang" type="button" role="tab"><i class="fa-solid fa-mars me-2"></i> Kakang</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-senduk-tab" data-bs-toggle="pill" data-bs-target="#pills-senduk" type="button" role="tab"><i class="fa-solid fa-venus me-2"></i> Senduk</button>
            </li>
        </ul>

        <div class="tab-content" id="pills-tabContent">
            
            <!-- TAB KAKANG -->
            <div class="tab-pane fade show active" id="pills-kakang" role="tabpanel">
                <div class="row g-4">
                    <?php
                    $q_kakang = mysqli_query($koneksi, "SELECT * FROM kontestan WHERE kategori = 'Kakang' ORDER BY nomor_peserta ASC");
                    while($row = mysqli_fetch_assoc($q_kakang)) {
                        $foto = ($row['foto'] && file_exists("../uploads/".$row['foto'])) ? "../uploads/".$row['foto'] : "https://via.placeholder.com/400x400/1e293b/64748b?text=No+Image";
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card-contestant">
                            <div class="img-wrapper">
                                <span class="badge-number"><?= $row['nomor_peserta'] ?></span>
                                <img src="<?= $foto ?>" alt="<?= $row['nama'] ?>">
                            </div>
                            <div class="card-body-custom">
                                <h5 class="contestant-name"><?= $row['nama'] ?></h5>
                                <div class="contestant-instansi"><i class="fa-solid fa-school me-1"></i> <?= $row['asal_instansi'] ?></div>
                                <p class="contestant-desc"><?= $row['deskripsi'] ?></p>
                                <div class="d-flex gap-2">
                                    <a href="detail-kontestan.php?id=<?= $row['id'] ?>" class="btn btn-detail w-50 py-2">Lihat Detail</a>
                                    <a href="voting.php" class="btn btn-vote w-50 py-2"><i class="fa-solid fa-check-to-slot me-1"></i> Vote</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <!-- TAB SENDuk -->
            <div class="tab-pane fade" id="pills-senduk" role="tabpanel">
                <div class="row g-4">
                    <?php
                    $q_senduk = mysqli_query($koneksi, "SELECT * FROM kontestan WHERE kategori = 'Senduk' ORDER BY nomor_peserta ASC");
                    while($row = mysqli_fetch_assoc($q_senduk)) {
                        $foto = ($row['foto'] && file_exists("../uploads/".$row['foto'])) ? "../uploads/".$row['foto'] : "https://via.placeholder.com/400x400/1e293b/64748b?text=No+Image";
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card-contestant">
                            <div class="img-wrapper">
                                <span class="badge-number"><?= $row['nomor_peserta'] ?></span>
                                <img src="<?= $foto ?>" alt="<?= $row['nama'] ?>">
                            </div>
                            <div class="card-body-custom">
                                <h5 class="contestant-name"><?= $row['nama'] ?></h5>
                                <div class="contestant-instansi"><i class="fa-solid fa-school me-1"></i> <?= $row['asal_instansi'] ?></div>
                                <p class="contestant-desc"><?= $row['deskripsi'] ?></p>
                                <div class="d-flex gap-2">
                                    <a href="detail-kontestan.php?id=<?= $row['id'] ?>" class="btn btn-detail w-50 py-2">Lihat Detail</a>
                                    <a href="voting.php" class="btn btn-vote w-50 py-2"><i class="fa-solid fa-check-to-slot me-1"></i> Vote</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>

        </div>
    </div>

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