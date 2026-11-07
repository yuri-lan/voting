<?php
session_start();
require_once '../config/koneksi.php';

// 1. Cek Login: Kalau belum login, tendang ke halaman login
if (!isset($_SESSION['user_id'])) {
    echo "<script>
        alert('Anda harus login terlebih dahulu untuk melakukan voting!');
        window.location.href='../auth/login.php';
    </script>";
    exit;
}

$user_id = $_SESSION['user_id'];

// 2. Cek Status Periode Voting
$q_periode = mysqli_query($koneksi, "SELECT * FROM periode_voting ORDER BY id DESC LIMIT 1");
$periode = mysqli_fetch_assoc($q_periode);
$status_voting = $periode ? $periode['status'] : 'Nonaktif';

// 3. Cek apakah user sudah pernah voting Kakang atau Senduk
$q_cek_kakang = mysqli_query($koneksi, "SELECT * FROM voting WHERE user_id = '$user_id' AND kategori = 'Kakang'");
$sudah_vote_kakang = mysqli_num_rows($q_cek_kakang) > 0;

$q_cek_senduk = mysqli_query($koneksi, "SELECT * FROM voting WHERE user_id = '$user_id' AND kategori = 'Senduk'");
$sudah_vote_senduk = mysqli_num_rows($q_cek_senduk) > 0;

// Kalau dua-duanya udah vote, tendang ke klasemen dengan pesan kuota habis
if ($sudah_vote_kakang && $sudah_vote_senduk) {
    header("Location: klasemen.php?pesan=kuota_habis");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bilik Suara - Kakang Senduk</title>
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

        /* KARTU VOTING */
        .vote-section-title { font-size: 1.5rem; font-weight: 700; color: white; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid rgba(255,255,255,0.1); }
        
        .vote-card-label { cursor: pointer; display: block; position: relative; margin-bottom: 20px; }
        .vote-card-label input[type="radio"] { display: none; } 

        .vote-card {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 15px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .vote-card img { width: 80px; height: 80px; object-fit: cover; border-radius: 15px; margin-right: 20px; }
        .vote-card h5 { font-size: 1.1rem; font-weight: 700; color: white; margin-bottom: 5px; }
        .vote-card p { font-size: 0.85rem; color: var(--primary); margin-bottom: 0; }

        /* Efek saat radio dipilih */
        .vote-card-label input:checked + .vote-card {
            border-color: var(--primary);
            background: rgba(0, 210, 255, 0.1);
            box-shadow: 0 0 20px rgba(0, 210, 255, 0.3);
            transform: scale(1.02);
        }

        /* Tombol Submit */
        .btn-submit-vote {
            background: linear-gradient(135deg, #00d2ff, #3a7bd5);
            color: white;
            font-weight: 800;
            font-size: 1.2rem;
            padding: 18px;
            border-radius: 50px;
            border: none;
            width: 100%;
            box-shadow: 0 10px 25px rgba(0, 210, 255, 0.4);
            transition: 0.3s;
        }
        .btn-submit-vote:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0, 210, 255, 0.6); }

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
                    <?php if($_SESSION['role'] === 'admin'): ?>
                        <a href="../admin/dashboard.php" class="btn btn-outline-info btn-sm rounded-pill px-3 me-3">Dashboard Admin</a>
                    <?php endif; ?>
                    <span class="me-3 fw-bold text-white d-none d-lg-inline">Halo, <?= $_SESSION['nama'] ?></span>
                    <a href="../auth/logout.php" class="btn btn-danger btn-sm rounded-pill px-3 btn-logout"><i class="fa-solid fa-right-from-bracket me-1"></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- KONTEN UTAMA -->
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold text-white mb-2">Bilik Suara Digital</h1>
            <p class="text-secondary">Pilih satu Kakang dan satu Senduk favoritmu. Suaramu sangat berharga!</p>
        </div>

        <!-- CEK JIKA VOTING DITUTUP -->
        <?php if($status_voting !== 'Aktif'): ?>
            <div class="alert alert-danger text-center border-0 shadow-lg" style="border-radius: 20px; background: rgba(220, 53, 69, 0.2); color: #ff6b6b;">
                <i class="fa-solid fa-lock fs-1 mb-3"></i>
                <h4 class="fw-bold">Voting Ditutup!</h4>
                <p class="mb-0">Maaf, periode voting saat ini sedang tidak aktif. Silakan hubungi administrator.</p>
            </div>
        <?php else: ?>
            
            <!-- FORM VOTING -->
            <form id="formVoting" action="../proses/simpan-voting.php" method="POST">
                <div class="row g-5">
                    
                    <!-- BAGIAN KAKANG -->
                    <div class="col-lg-6">
                        <h3 class="vote-section-title"><i class="fa-solid fa-mars me-2 text-info"></i> Pilih Kakang</h3>
                        
                        <?php if($sudah_vote_kakang): ?>
                            <div class="alert alert-success bg-success bg-opacity-10 border border-success text-success text-center" style="border-radius: 15px;">
                                <i class="fa-solid fa-circle-check fs-3 mb-2"></i><br>
                                <b>Anda sudah memilih Kakang!</b>
                            </div>
                        <?php else: ?>
                            <?php
                            $q_kakang = mysqli_query($koneksi, "SELECT * FROM kontestan WHERE kategori = 'Kakang' ORDER BY nomor_peserta ASC");
                            while($row = mysqli_fetch_assoc($q_kakang)) {
                                $foto = ($row['foto'] && file_exists("../uploads/".$row['foto'])) ? "../uploads/".$row['foto'] : "https://via.placeholder.com/100/1e293b/64748b?text=No+Img";
                            ?>
                            <label class="vote-card-label">
                                <input type="radio" name="kakang_id" value="<?= $row['id'] ?>" required>
                                <div class="vote-card">
                                    <img src="<?= $foto ?>" alt="Foto">
                                    <div>
                                        <h5><?= $row['nama'] ?></h5>
                                        <p><?= $row['nomor_peserta'] ?> | <?= $row['asal_instansi'] ?></p>
                                    </div>
                                </div>
                            </label>
                            <?php } ?>
                        <?php endif; ?>
                    </div>

                    <!-- BAGIAN SENDuk -->
                    <div class="col-lg-6">
                        <h3 class="vote-section-title"><i class="fa-solid fa-venus me-2 text-danger"></i> Pilih Senduk</h3>
                        
                        <?php if($sudah_vote_senduk): ?>
                            <div class="alert alert-success bg-success bg-opacity-10 border border-success text-success text-center" style="border-radius: 15px;">
                                <i class="fa-solid fa-circle-check fs-3 mb-2"></i><br>
                                <b>Anda sudah memilih Senduk!</b>
                            </div>
                        <?php else: ?>
                            <?php
                            $q_senduk = mysqli_query($koneksi, "SELECT * FROM kontestan WHERE kategori = 'Senduk' ORDER BY nomor_peserta ASC");
                            while($row = mysqli_fetch_assoc($q_senduk)) {
                                $foto = ($row['foto'] && file_exists("../uploads/".$row['foto'])) ? "../uploads/".$row['foto'] : "https://via.placeholder.com/100/1e293b/64748b?text=No+Img";
                            ?>
                            <label class="vote-card-label">
                                <input type="radio" name="senduk_id" value="<?= $row['id'] ?>" required>
                                <div class="vote-card">
                                    <img src="<?= $foto ?>" alt="Foto">
                                    <div>
                                        <h5><?= $row['nama'] ?></h5>
                                        <p><?= $row['nomor_peserta'] ?> | <?= $row['asal_instansi'] ?></p>
                                    </div>
                                </div>
                            </label>
                            <?php } ?>
                        <?php endif; ?>
                    </div>

                </div>

                <!-- Tombol Submit -->
                <?php if(!$sudah_vote_kakang || !$sudah_vote_senduk): ?>
                    <div class="row mt-5">
                        <div class="col-md-8 mx-auto">
                            <button type="button" id="btnSubmitVote" class="btn-submit-vote">
                                <i class="fa-solid fa-check-to-slot me-2"></i> KIRIM SUARA SAYA
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </form>

        <?php endif; ?>
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
        // 1. Konfirmasi Submit Voting (Pintar & Anti-Error)
        document.getElementById('btnSubmitVote')?.addEventListener('click', function(e) {
            const form = document.getElementById('formVoting');
            
            // Validasi HTML5 (pastikan radio yang required udah dipilih)
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            let htmlMessage = "Kamu akan memilih:<br><br>";
            let hasSelection = false;

            // Cek pilihan Kakang (jika ada di halaman)
            const kakangRadio = document.querySelector('input[name="kakang_id"]:checked');
            if (kakangRadio) {
                const kakangName = kakangRadio.nextElementSibling.querySelector('h5').innerText;
                htmlMessage += `<b style="color:#00d2ff; font-size:1.2rem;">${kakangName}</b> (Kakang)<br>`;
                hasSelection = true;
            }

            // Cek pilihan Senduk (jika ada di halaman)
            const sendukRadio = document.querySelector('input[name="senduk_id"]:checked');
            if (sendukRadio) {
                const sendukName = sendukRadio.nextElementSibling.querySelector('h5').innerText;
                htmlMessage += `<b style="color:#ff7eb3; font-size:1.2rem;">${sendukName}</b> (Senduk)<br>`;
                hasSelection = true;
            }

            if (!hasSelection) {
                Swal.fire('Peringatan', 'Silakan pilih kontestan terlebih dahulu!', 'warning');
                return;
            }

            htmlMessage += `<br><span style="color:#ff6b6b; font-size:0.9rem;"><i>Pilihan tidak dapat diubah setelah dikirim!</i></span>`;

            Swal.fire({
                title: 'Konfirmasi Pilihan',
                html: htmlMessage,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#00d2ff',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fa-solid fa-check me-1"></i> Ya, Kirim Suara!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-info text-dark mx-2 font-weight-bold',
                    cancelButton: 'btn btn-secondary mx-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Eksekusi kirim ke database
                }
            });
        });

        // 2. Konfirmasi Logout
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