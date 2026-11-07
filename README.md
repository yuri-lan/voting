# Aplikasi Voting Pemilihan Kakang Senduk Ponorogo

Sistem Informasi Pemilihan Duta Wisata Daerah Berbasis Web. Aplikasi ini dibuat untuk memfasilitasi proses pemilihan Kakang dan Senduk Ponorogo secara digital, transparan, dan *real-time*.

Proyek ini dibangun dengan **UI/UX Modern (Dark Space & Glassmorphism Theme)** untuk memberikan pengalaman pengguna yang berbeda dan menarik.

---

## Fitur Utama

### Halaman Pemilih (User)
- **Desain Futuristik**: Tema luar angkasa (Dark Mode) dengan animasi bintang yang bergerak dan efek kaca reflektif (Glassmorphism).
- **Katalog Kontestan**: Lihat daftar kontestan Kakang & Senduk beserta profil lengkap (Visi, Misi, Prestasi).
- **Sistem Voting Aman**: 
  - Wajib login sebelum memilih.
  - Satu akun hanya bisa memilih 1 Kakang dan 1 Senduk.
  - Dilengkapi validasi anti *double-vote* dengan transaksi database.
- **Klasemen Live (Real-time)**: Halaman peringkat otomatis yang ter-*update* setiap 15 detik tanpa perlu *refresh*.
- **Podium Juara**: Tampilan khusus untuk Juara 1, 2, dan 3.

### Halaman Administrator (Admin)
- **Dashboard Statistik**: Total pemilih, total suara, dan **Grafik Batang (Chart.js)** interaktif untuk memantau perolehan suara.
- **Manajemen Kontestan (CRUD)**: Tambah, Edit, dan Hapus data kontestan beserta foto.
- **Manajemen Pemilih**: Pantau dan hapus akun pemilih.
- **Pengaturan Periode**: Buka dan tutup bilik suara voting sesuai jadwal.
- **Rekap & Export Data**: Fitur *Download* rekapitulasi suara ke format **Excel** dan **PDF** (Print).
- **Notifikasi Interaktif**: Menggunakan **SweetAlert2** untuk semua notifikasi (sukses, error, konfirmasi) agar tidak membosankan.

---

## Teknologi yang Digunakan
- **Bahasa**: PHP Native, JavaScript, HTML, CSS
- **Database**: MySQL (MariaDB)
- **Framework UI**: Bootstrap 5
- **Library Tambahan**: 
  - [Chart.js](https://www.chartjs.org/) (Grafik Dashboard)
  - [SweetAlert2](https://sweetalert2.github.io/) (Pop-up Notifikasi)
  - [FontAwesome](https://fontawesome.com/) (Ikon)
  - [Google Fonts](https://fonts.google.com/) (Poppins)

---

## Cara Menjalankan Proyek (Instalasi Lokal)

### Prasyarat
- Sudah menginstall **XAMPP** (Apache & MySQL).

### Langkah-langkah
1. **Clone Repository**
   Buka terminal/CMD dan jalankan:
   ```bash
   git clone https://github.com/yuri-lan/voting.git