<?php
session_start();
require_once '../config/koneksi.php';

// Kalau udah login, lempar ke halaman utama
if (isset($_SESSION['user_id'])) {
    header("Location: ../pages/index.php");
    exit;
}

$pesan = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $pesan = "<script>Swal.fire('Peringatan', 'Email dan Password wajib diisi!', 'warning');</script>";
    } else {
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($koneksi, $query);

        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Verifikasi password hash
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['nama']     = $user['nama'];
                $_SESSION['role']     = $user['role'];

                // Redirect berdasarkan role
                if ($user['role'] === 'admin') {
                    header("Location: ../admin/dashboard.php");
                } else {
                    header("Location: ../pages/index.php");
                }
                exit;
            } else {
                $pesan = "<script>Swal.fire('Gagal!', 'Password yang Anda masukkan salah.', 'error');</script>";
            }
        } else {
            $pesan = "<script>Swal.fire('Gagal!', 'Email tidak ditemukan dalam sistem.', 'error');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kakang Senduk</title>
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* ANIMASI BINTANG */
        .stars-bg { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: -1; background: linear-gradient(to bottom, #0b1120, #0f172a); }
        .stars, .stars2 { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-repeat: repeat; }
        .stars { background-image: radial-gradient(1.5px 1.5px at 20px 30px, #fff, rgba(0,0,0,0)), radial-gradient(1.5px 1.5px at 40px 70px, #fff, rgba(0,0,0,0)), radial-gradient(1.5px 1.5px at 50px 160px, #fff, rgba(0,0,0,0)), radial-gradient(1.5px 1.5px at 90px 40px, #fff, rgba(0,0,0,0)); background-size: 200px 200px; animation: twinkle 4s infinite alternate; }
        .stars2 { background-image: radial-gradient(2px 2px at 10px 10px, var(--primary), rgba(0,0,0,0)), radial-gradient(2px 2px at 150px 150px, #fff, rgba(0,0,0,0)); background-size: 300px 300px; animation: twinkle 6s infinite alternate-reverse; opacity: 0.6; }
        @keyframes twinkle { 0% { opacity: 0.2; transform: scale(0.9); } 100% { opacity: 1; transform: scale(1.1); } }

        /* KARTU KACA (GLASSMORPHISM) */
        .glass-card {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5), 0 0 20px rgba(0, 210, 255, 0.1);
            position: relative;
            z-index: 10;
        }

        .brand-title { font-weight: 700; font-size: 24px; color: white; text-align: center; margin-bottom: 5px; }
        .brand-title i { color: #ffd700; }
        .brand-subtitle { color: #94a3b8; text-align: center; font-size: 14px; margin-bottom: 30px; }

        /* FORM INPUTS */
        .form-label { font-size: 14px; font-weight: 500; color: #cbd5e1; }
        .input-group-text {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-right: none;
            color: var(--primary);
            border-radius: 10px 0 0 10px;
        }
        .form-control {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-left: none;
            color: white;
            border-radius: 0 10px 10px 0;
            padding: 12px 15px;
        }
        .form-control:focus {
            background: rgba(15, 23, 42, 0.8);
            border-color: var(--primary);
            box-shadow: 0 0 10px rgba(0, 210, 255, 0.2);
            color: white;
        }
        .form-control::placeholder { color: #64748b; }

        /* BUTTONS */
        .btn-glow {
            background: linear-gradient(135deg, #00d2ff, #3a7bd5);
            color: white;
            font-weight: 700;
            padding: 12px;
            border-radius: 10px;
            border: none;
            width: 100%;
            box-shadow: 0 5px 15px rgba(0, 210, 255, 0.3);
            transition: 0.3s;
        }
        .btn-glow:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 210, 255, 0.6);
            color: white;
        }

        .link-custom { color: var(--primary); text-decoration: none; font-weight: 600; transition: 0.3s; }
        .link-custom:hover { color: white; text-shadow: 0 0 10px rgba(0,210,255,0.5); }
        
        .btn-back { position: absolute; top: 20px; left: 20px; color: #94a3b8; text-decoration: none; transition: 0.3s; z-index: 20;}
        .btn-back:hover { color: white; transform: translateX(-5px); }
    </style>
</head>
<body>

    <!-- Tombol Kembali -->
    <a href="../pages/index.php" class="btn-back"><i class="fa-solid fa-arrow-left fs-4"></i></a>

    <!-- BACKGROUND BINTANG -->
    <div class="stars-bg">
        <div class="stars"></div>
        <div class="stars2"></div>
    </div>

    <div class="glass-card">
        <div class="brand-title"><i class="fa-solid fa-crown me-2"></i> Kakang Senduk</div>
        <div class="brand-subtitle">Silakan masuk ke akun Anda</div>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-glow mb-4">Login Sekarang</button>
        </form>

        <div class="text-center">
            <p class="mb-0 text-secondary" style="font-size: 14px;">Belum punya akun? <a href="register.php" class="link-custom">Daftar di sini</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- LIBRARY SWEETALERT -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- TAMPILKAN PESAN ERROR/SWEETALERT -->
    <?= $pesan; ?>
</body>
</html>