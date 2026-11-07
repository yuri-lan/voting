<?php
session_start();
require_once '../config/koneksi.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? $_GET['id'] : 0;

// Ambil data kontestan
$query = mysqli_query($koneksi, "SELECT * FROM kontestan WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

// Kalau data nggak ada, balikin ke halaman kontestan
if (!$data) {
    header("Location: kontestan.php");
    exit;
}

$foto = ($data['foto'] && file_exists("../uploads/".$data['foto'])) ? "../uploads/".$data['foto'] : "https://via.placeholder.com/600x800/1e293b/64748b?text=No+Image";
$warna_badge = ($data['kategori'] == 'Kakang') ? 'var(--primary)' : '#ff7eb3'; // Cyan untuk Kakang, Pink untuk Senduk
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail <?= $data['nama'] ?> - Kakang Senduk</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --primary: #00d2ff; 
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

        /* NAVBAR */
        .navbar { background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(15px); border-bottom: 1px solid rgba(255,255,255,0.05); padding: 15px 0; }
        .navbar-brand { font-weight: 700; font-size: 22px; color: white !important; }
        .navbar .nav-link { font-weight: 500; color: #94a3b8 !important; margin: 0 10px; transition: 0.3s; }
        .navbar .nav-link:hover, .navbar .nav-link.active { color: var(--primary) !important; text-shadow: 0 0 10px rgba(0,210,255,0.5); }

        /* KARTU UTAMA */
        .detail-card {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }

        /* Foto Profil */
        .profile-img {
            width: 100%;
            border-radius: 20px;
            object-fit: cover;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            border: 3px solid rgba(255, 255, 255, 0.1);
        }

        /* Teks & Konten */
        .contestant-name { font-size: 2.5rem; font-weight: 800; color: white; margin-bottom: 5px; }
        .badge-category { background: <?= $warna_badge ?>; color: #0f172a; font-weight: 800; font-size: 14px; padding: 8px 20px; border-radius: 50px; letter-spacing: 1px; }
        
        .section-title { font-size: 1.1rem; font-weight: 700; color: var(--primary); margin-top: 30px; margin-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px; }
        .section-text { font-size: 0.95rem; color: #cbd5e1; line-height: 1.8; white-space: pre-line; }
        
        .info-row { display: flex; margin-bottom: 10px; font-size: 0.95rem; }
        .info-label { width: 150px; color: #94a3b8; font-weight: 500; }
        .info-value { color: white; font-weight: 600; }

        /* Tombol */
        .btn-back { background: rgba(255,255,255,0.1); color: white; border-radius: 50px; padding: 10px 25px; font-weight: 600; transition: 0.3s; border: 1px solid rgba(255,255,255,0.1); }
        .btn-back:hover { background: white; color: #0f172a; }
        .btn-vote-lg { background: var(--primary); color: #0f172a; border: none; border-radius: 50px; padding: 15px; font-weight: 700; font-size: 1.1rem; box-shadow: 0 0 20px rgba(0,210,255,0.4); transition: 0.3s; width: 100%; margin-top: 30px; }
        .btn-vote-lg:hover { background: #fff; box-shadow: 0 0 30px rgba(0,210,255,0.8); transform: translateY(-3px); }

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
                        <a href="../auth/login.php" class="btn btn-outline-light rounded-pill px-4 me-2 fw-bold btn-sm" style="border-color: rgba(255,255,255,0.2);">Login</a>
                        <a href="../auth/register.php" class="btn btn-info rounded-pill px-4 fw-bold btn-sm" style="background: var(--primary); color: #0f172a; border: none;">Daftar</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- KONTEN UTAMA -->
    <div class="container py-5">
        <div class="mb-4">
            <a href="kontestan.php" class="btn btn-back text-decoration-none"><i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Daftar</a>
        </div>

        <div class="detail-card">
            <div class="row g-5">
                <!-- Kolom Kiri: Foto -->
                <div class="col-lg-5 text-center">
                    <img src="<?= $foto ?>" alt="<?= $data['nama'] ?>" class="profile-img mb-4">
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge bg-secondary px-3 py-2 fs-6 rounded-pill">No. Urut: <?= $data['nomor_peserta'] ?></span>
                        <span class="badge-category"><?= $data['kategori'] ?></span>
                    </div>
                </div>

                <!-- Kolom Kanan: Biodata & Teks -->
                <div class="col-lg-7">
                    <h1 class="contestant-name"><?= $data['nama'] ?></h1>
                    <p class="text-secondary mb-4"><i class="fa-solid fa-school me-2"></i> <?= $data['asal_instansi'] ?></p>

                    <div class="info-row">
                        <div class="info-label">Tempat, Tgl Lahir</div>
                        <div class="info-value">: 
                            <?= $data['tempat_lahir'] ? $data['tempat_lahir'] : '-' ?>, 
                            <?= $data['tanggal_lahir'] ? date('d F Y', strtotime($data['tanggal_lahir'])) : '-' ?>
                        </div>
                    </div>

                    <div class="section-title"><i class="fa-solid fa-bullseye me-2"></i> Visi</div>
                    <div class="section-text"><?= $data['visi'] ? $data['visi'] : 'Belum ada data visi.' ?></div>

                    <div class="section-title"><i class="fa-solid fa-list-check me-2"></i> Misi</div>
                    <div class="section-text"><?= $data['misi'] ? $data['misi'] : 'Belum ada data misi.' ?></div>

                    <div class="section-title"><i class="fa-solid fa-trophy me-2"></i> Prestasi</div>
                    <div class="section-text"><?= $data['prestasi'] ? $data['prestasi'] : 'Belum ada data prestasi.' ?></div>

                    <div class="section-title"><i class="fa-solid fa-user me-2"></i> Profil Singkat</div>
                    <div class="section-text"><?= $data['deskripsi'] ? $data['deskripsi'] : 'Belum ada deskripsi profil.' ?></div>

                    <!-- Tombol Vote -->
                    <a href="voting.php" class="btn btn-vote-lg text-decoration-none text-center d-block"><i class="fa-solid fa-check-to-slot me-2"></i> VOTE <?= strtoupper($data['nama']) ?> SEKARANG!</a>
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