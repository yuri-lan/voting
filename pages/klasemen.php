<?php
session_start();
require_once '../config/koneksi.php';

// Query Total Suara Keseluruhan
$q_total_kakang = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM voting WHERE kategori = 'Kakang'");
$total_kakang = mysqli_fetch_assoc($q_total_kakang)['total'];

$q_total_senduk = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM voting WHERE kategori = 'Senduk'");
$total_senduk = mysqli_fetch_assoc($q_total_senduk)['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klasemen Live - Kakang Senduk</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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

        /* TAB MENU */
        .nav-pills .nav-link { color: #94a3b8 !important; border-radius: 50px; padding: 10px 30px; font-weight: 600; transition: 0.3s; border: 1px solid rgba(255,255,255,0.1); }
        .nav-pills .nav-link.active { background: var(--primary) !important; color: #0f172a !important; box-shadow: 0 0 15px rgba(0,210,255,0.4); border-color: var(--primary) !important; }

        /* PODIUM JUARA */
        .podium-card {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            position: relative;
            transition: 0.3s;
        }
        .podium-card:hover { transform: translateY(-5px); }
        
        .podium-img { width: 100px; height: 100px; object-fit: cover; border-radius: 50%; margin-bottom: 15px; border: 4px solid; }
        
        /* Warna Juara */
        .juara-1 { border-color: #ffd700; box-shadow: 0 0 20px rgba(255, 215, 0, 0.2); }
        .juara-1 .podium-img { border-color: #ffd700; }
        .juara-1 .rank-badge { background: #ffd700; color: #000; }
        
        .juara-2 { border-color: #c0c0c0; }
        .juara-2 .podium-img { border-color: #c0c0c0; }
        .juara-2 .rank-badge { background: #c0c0c0; color: #000; }

        .juara-3 { border-color: #cd7f32; }
        .juara-3 .podium-img { border-color: #cd7f32; }
        .juara-3 .rank-badge { background: #cd7f32; color: #fff; }

        .rank-badge { position: absolute; top: -15px; left: 50%; transform: translateX(-50%); padding: 5px 20px; border-radius: 50px; font-weight: 800; font-size: 14px; }

        /* TABLE LIST */
        .table-custom { background: rgba(30, 41, 59, 0.4); border-radius: 15px; overflow: hidden; }
        .table-custom table { color: white; margin-bottom: 0; }
        .table-custom th { background: rgba(15, 23, 42, 0.8); color: #94a3b8; border-bottom: none; padding: 15px; }
        .table-custom td { background: transparent; border-bottom: 1px solid rgba(255,255,255,0.05); padding: 15px; vertical-align: middle; }
        
        .progress { height: 8px; background-color: rgba(255,255,255,0.1); border-radius: 10px; }
        
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
                    <li class="nav-item"><a class="nav-link active" href="klasemen.php">Klasemen</a></li>
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
            <h1 class="fw-bold text-white mb-2">Klasemen Sementara</h1>
            <p class="text-secondary">Pantau perolehan suara secara langsung. Halaman ini akan <i>refresh</i> otomatis.</p>
            
            <div class="d-inline-flex gap-3 mt-3 flex-wrap justify-content-center">
                <span class="badge bg-dark px-4 py-2 border border-info rounded-pill"><i class="fa-solid fa-mars text-info me-2"></i> Total Suara Kakang: <b class="text-white"><?= $total_kakang ?></b></span>
                <span class="badge bg-dark px-4 py-2 border border-danger rounded-pill"><i class="fa-solid fa-venus text-danger me-2"></i> Total Suara Senduk: <b class="text-white"><?= $total_senduk ?></b></span>
            </div>
        </div>

        <!-- TAB MENU -->
        <ul class="nav nav-pills justify-content-center mb-5" id="pills-tab" role="tablist">
            <li class="nav-item me-2" role="presentation">
                <button class="nav-link active" id="pills-kakang-tab" data-bs-toggle="pill" data-bs-target="#pills-kakang" type="button" role="tab"><i class="fa-solid fa-mars me-2"></i> Klasemen Kakang</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-senduk-tab" data-bs-toggle="pill" data-bs-target="#pills-senduk" type="button" role="tab"><i class="fa-solid fa-venus me-2"></i> Klasemen Senduk</button>
            </li>
        </ul>

        <div class="tab-content" id="pills-tabContent">
            
            <!-- TAB KAKANG -->
            <div class="tab-pane fade show active" id="pills-kakang" role="tabpanel">
                <?php
                $q_kakang = mysqli_query($koneksi, "
                    SELECT k.id, k.nomor_peserta, k.nama, k.foto, k.asal_instansi, COUNT(v.id) AS total_suara
                    FROM kontestan k
                    LEFT JOIN voting v ON k.id = v.kontestan_id AND v.kategori = 'Kakang'
                    WHERE k.kategori = 'Kakang'
                    GROUP BY k.id ORDER BY total_suara DESC, k.nomor_peserta ASC
                ");
                $data_kakang = [];
                while($row = mysqli_fetch_assoc($q_kakang)) { $data_kakang[] = $row; }
                ?>

                <!-- PODIUM TOP 3 -->
                <div class="row justify-content-center g-4 mb-5">
                    <?php 
                    // Urutan Podium (2, 1, 3)
                    $podium_order = [1, 0, 2]; 
                    foreach($podium_order as $index) {
                        if(isset($data_kakang[$index])) {
                            $d = $data_kakang[$index];
                            $rank = $index + 1;
                            $class = "juara-" . $rank;
                            $persen = ($total_kakang > 0) ? ($d['total_suara'] / $total_kakang) * 100 : 0;
                            $foto = ($d['foto'] && file_exists("../uploads/".$d['foto'])) ? "../uploads/".$d['foto'] : "https://via.placeholder.com/150/1e293b/64748b?text=No+Img";
                            
                            // Biar Juara 1 agak lebih gede
                            $col_class = ($rank == 1) ? "col-md-4" : "col-md-3";
                            $mt_class = ($rank == 1) ? "" : "mt-4";
                    ?>
                    <div class="<?= $col_class ?> <?= $mt_class ?>">
                        <div class="podium-card <?= $class ?>">
                            <div class="rank-badge">JUARA <?= $rank ?></div>
                            <img src="<?= $foto ?>" class="podium-img mx-auto" alt="<?= $d['nama'] ?>">
                            <h5 class="fw-bold text-white mb-1"><?= $d['nama'] ?></h5>
                            <p class="text-secondary fs-6 mb-3"><?= $d['nomor_peserta'] ?></p>
                            <h3 class="fw-bold text-white"><?= $d['total_suara'] ?> <span class="fs-6 text-muted">suara</span></h3>
                            <div class="progress mt-2">
                                <div class="progress-bar bg-info" role="progressbar" style="width: <?= $persen ?>%"></div>
                            </div>
                        </div>
                    </div>
                    <?php }} ?>
                </div>

                <!-- LIST SISANYA (Rank 4 ke bawah) -->
                <?php if(count($data_kakang) > 3): ?>
                <h5 class="text-white mb-3"><i class="fa-solid fa-list me-2"></i> Peringkat Selanjutnya</h5>
                <div class="table-custom">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="10%">Rank</th>
                                <th>Nama Kontestan</th>
                                <th width="30%">Perolehan Suara</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for($i = 3; $i < count($data_kakang); $i++): 
                                $d = $data_kakang[$i];
                                $rank = $i + 1;
                                $persen = ($total_kakang > 0) ? ($d['total_suara'] / $total_kakang) * 100 : 0;
                            ?>
                            <tr>
                                <td><b class="text-white fs-5">#<?= $rank ?></b></td>
                                <td>
                                    <span class="fw-bold text-white"><?= $d['nama'] ?></span><br>
                                    <small class="text-secondary"><?= $d['asal_instansi'] ?></small>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-between text-white mb-1">
                                        <span><?= $d['total_suara'] ?> Suara</span>
                                        <span><?= number_format($persen, 1) ?>%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-info progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $persen ?>%"></div>
                                    </div>
                                </td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

            <!-- TAB SENDuk -->
            <div class="tab-pane fade" id="pills-senduk" role="tabpanel">
                <?php
                $q_senduk = mysqli_query($koneksi, "
                    SELECT k.id, k.nomor_peserta, k.nama, k.foto, k.asal_instansi, COUNT(v.id) AS total_suara
                    FROM kontestan k
                    LEFT JOIN voting v ON k.id = v.kontestan_id AND v.kategori = 'Senduk'
                    WHERE k.kategori = 'Senduk'
                    GROUP BY k.id ORDER BY total_suara DESC, k.nomor_peserta ASC
                ");
                $data_senduk = [];
                while($row = mysqli_fetch_assoc($q_senduk)) { $data_senduk[] = $row; }
                ?>

                <!-- PODIUM TOP 3 -->
                <div class="row justify-content-center g-4 mb-5">
                    <?php 
                    foreach($podium_order as $index) {
                        if(isset($data_senduk[$index])) {
                            $d = $data_senduk[$index];
                            $rank = $index + 1;
                            $class = "juara-" . $rank;
                            $persen = ($total_senduk > 0) ? ($d['total_suara'] / $total_senduk) * 100 : 0;
                            $foto = ($d['foto'] && file_exists("../uploads/".$d['foto'])) ? "../uploads/".$d['foto'] : "https://via.placeholder.com/150/1e293b/64748b?text=No+Img";
                            
                            $col_class = ($rank == 1) ? "col-md-4" : "col-md-3";
                            $mt_class = ($rank == 1) ? "" : "mt-4";
                    ?>
                    <div class="<?= $col_class ?> <?= $mt_class ?>">
                        <div class="podium-card <?= $class ?>">
                            <div class="rank-badge">JUARA <?= $rank ?></div>
                            <img src="<?= $foto ?>" class="podium-img mx-auto" alt="<?= $d['nama'] ?>">
                            <h5 class="fw-bold text-white mb-1"><?= $d['nama'] ?></h5>
                            <p class="text-secondary fs-6 mb-3"><?= $d['nomor_peserta'] ?></p>
                            <h3 class="fw-bold text-white"><?= $d['total_suara'] ?> <span class="fs-6 text-muted">suara</span></h3>
                            <div class="progress mt-2">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $persen ?>%"></div>
                            </div>
                        </div>
                    </div>
                    <?php }} ?>
                </div>

                <!-- LIST SISANYA -->
                <?php if(count($data_senduk) > 3): ?>
                <h5 class="text-white mb-3"><i class="fa-solid fa-list me-2"></i> Peringkat Selanjutnya</h5>
                <div class="table-custom">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="10%">Rank</th>
                                <th>Nama Kontestan</th>
                                <th width="30%">Perolehan Suara</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for($i = 3; $i < count($data_senduk); $i++): 
                                $d = $data_senduk[$i];
                                $rank = $i + 1;
                                $persen = ($total_senduk > 0) ? ($d['total_suara'] / $total_senduk) * 100 : 0;
                            ?>
                            <tr>
                                <td><b class="text-white fs-5">#<?= $rank ?></b></td>
                                <td>
                                    <span class="fw-bold text-white"><?= $d['nama'] ?></span><br>
                                    <small class="text-secondary"><?= $d['asal_instansi'] ?></small>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-between text-white mb-1">
                                        <span><?= $d['total_suara'] ?> Suara</span>
                                        <span><?= number_format($persen, 1) ?>%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-danger progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $persen ?>%"></div>
                                    </div>
                                </td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- INFO WAKTU UPDATE -->
        <div class="text-center mt-5">
            <small class="text-secondary"><i class="fa-solid fa-rotate me-1 text-info"></i> Terakhir diperbarui: <?= date('d M Y, H:i:s') ?> WIB | <span id="live-indicator" class="text-success fw-bold"><i class="fa-solid fa-circle fs-6" style="font-size: 8px !important;"></i> LIVE</span></small>
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
        // 1. CEK NOTIFIKASI DARI URL (SUKSES VOTE ATAU KUOTA HABIS)
        const urlParams = new URLSearchParams(window.location.search);
        const pesan = urlParams.get('pesan');

        if (pesan === 'sukses') {
            Swal.fire({
                title: 'Voting Berhasil!',
                text: 'Terima kasih, suaramu sudah kami simpan. Silakan lihat klasemen di bawah ini.',
                icon: 'success',
                confirmButtonColor: '#00d2ff',
                confirmButtonText: '<i class="fa-solid fa-trophy me-1"></i> Lihat Klasemen',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.history.replaceState(null, null, window.location.pathname);
                    startAutoRefresh();
                }
            });
        } else if (pesan === 'kuota_habis') {
            Swal.fire({
                title: 'Kuota Voting Habis!',
                text: 'Anda sudah menggunakan hak suara untuk Kakang dan Senduk. Terima kasih telah berpartisipasi!',
                icon: 'info',
                confirmButtonColor: '#00d2ff',
                confirmButtonText: 'Mengerti',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.history.replaceState(null, null, window.location.pathname);
                    startAutoRefresh();
                }
            });
        } else {
            // Kalau bukan habis vote atau kuota habis, langsung jalanin auto refresh
            startAutoRefresh();
        }

        // 2. FUNGSI AUTO REFRESH
        function startAutoRefresh() {
            setInterval(function() {
                if (document.visibilityState === 'visible') {
                    window.location.reload();
                }
            }, 15000); // 15 detik
        }

        // 3. SCRIPT LOGOUT
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