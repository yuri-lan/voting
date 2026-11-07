-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 07 Nov 2026 pada 15.35
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_voting_kakang_senduk`
--
CREATE DATABASE IF NOT EXISTS `db_voting_kakang_senduk` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_voting_kakang_senduk`;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kontestan`
--

DROP TABLE IF EXISTS `kontestan`;
CREATE TABLE `kontestan` (
  `id` int(11) NOT NULL,
  `nomor_peserta` varchar(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kategori` enum('Kakang','Senduk') NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `asal_instansi` varchar(150) DEFAULT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `visi` text DEFAULT NULL,
  `misi` text DEFAULT NULL,
  `prestasi` text DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kontestan`
--

INSERT DELAYED IGNORE INTO `kontestan` (`id`, `nomor_peserta`, `nama`, `kategori`, `foto`, `asal_instansi`, `tempat_lahir`, `tanggal_lahir`, `visi`, `misi`, `prestasi`, `deskripsi`, `created_at`) VALUES
(1, 'K-01', 'Bagas Pritiwiwi', 'Kakang', '1794053334_WhatsApp Image 2025-05-20 at 15.37.05.jpeg', 'SMKN 1 Ponorogo', '', '0000-00-00', 'Melestarikan budaya Ponorogo', 'Aktif di seni Reog', '', 'Kakang nomor 1, hobi seni dan budaya.', '2026-11-07 18:07:19'),
(2, 'K-02', 'Rizky Hidayatullah', 'Kakang', '', 'SMAN 1 Ponorogo', '', '0000-00-00', 'Mempromosikan wisata daerah', 'Aktif di pariwisata', '', 'Kakang nomor 2, suka traveling.', '2026-11-07 18:07:19'),
(3, 'K-03', 'Fajar Nugrono', 'Kakang', '', 'SMKN 2 Ponorogo', '', '0000-00-00', 'Mengangkat UMKM lokal', 'Bantu UMKM Ponorogo', '', 'Kakang nomor 3, penggemar kuliner.', '2026-11-07 18:07:19'),
(4, 'S-01', 'Ayu Lestari', 'Senduk', NULL, 'SMKN 1 Ponorogo', NULL, NULL, 'Melestarikan tari tradisional', 'Aktif di sanggar tari', NULL, 'Senduk nomor 1, penari tradisional.', '2026-11-07 18:07:19'),
(5, 'S-02', 'Dinda Maharani', 'Senduk', NULL, 'SMAN 2 Ponorogo', NULL, NULL, 'Promosi wisata Ponorogo', 'Aktif di komunitas wisata', NULL, 'Senduk nomor 2, suka fotografi.', '2026-11-07 18:07:19'),
(6, 'S-03', 'Citra Dewi', 'Senduk', NULL, 'SMKN 3 Ponorogo', NULL, NULL, 'Mengembangkan batik lokal', 'Aktif di batik tulis', NULL, 'Senduk nomor 3, pecinta batik.', '2026-11-07 18:07:19');

-- --------------------------------------------------------

--
-- Struktur dari tabel `periode_voting`
--

DROP TABLE IF EXISTS `periode_voting`;
CREATE TABLE `periode_voting` (
  `id` int(11) NOT NULL,
  `nama_periode` varchar(100) NOT NULL,
  `tanggal_mulai` datetime NOT NULL,
  `tanggal_selesai` datetime NOT NULL,
  `status` enum('Aktif','Nonaktif') NOT NULL DEFAULT 'Nonaktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `periode_voting`
--

INSERT DELAYED IGNORE INTO `periode_voting` (`id`, `nama_periode`, `tanggal_mulai`, `tanggal_selesai`, `status`) VALUES
(1, 'Pemilihan Kakang Senduk 2025', '2026-11-07 18:07:19', '2026-11-14 18:07:19', 'Aktif');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','pemilih') NOT NULL DEFAULT 'pemilih',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT DELAYED IGNORE INTO `users` (`id`, `nama`, `email`, `no_hp`, `password`, `role`, `created_at`) VALUES
(1, 'Administrator', 'admin@kakangsenduk.test', '081234567890', '$2y$10$rW7nd6kEQaRfw2E12Gnp5Of3asfr7X3hX99KS5HWpgsQRt8wqZmX2', 'admin', '2026-11-07 18:07:19'),
(2, 'Budiana', 'budiana@email.com', '0877777776', '$2y$10$9GaOCmp3t2ojBdmZ6ko47OJXyVWAqag0cqefbk8/iDBr4Ni0NnnU2', 'pemilih', '2026-11-07 18:56:12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `voting`
--

DROP TABLE IF EXISTS `voting`;
CREATE TABLE `voting` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `kontestan_id` int(11) NOT NULL,
  `kategori` enum('Kakang','Senduk') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `voting`
--

INSERT DELAYED IGNORE INTO `voting` (`id`, `user_id`, `kontestan_id`, `kategori`, `created_at`) VALUES
(1, 2, 1, 'Kakang', '2026-11-07 13:48:58'),
(2, 2, 5, 'Senduk', '2026-11-07 13:48:58');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `kontestan`
--
ALTER TABLE `kontestan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `periode_voting`
--
ALTER TABLE `periode_voting`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `voting`
--
ALTER TABLE `voting`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_vote_per_kategori` (`user_id`,`kategori`),
  ADD KEY `fk_voting_kontestan` (`kontestan_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `kontestan`
--
ALTER TABLE `kontestan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `periode_voting`
--
ALTER TABLE `periode_voting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `voting`
--
ALTER TABLE `voting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `voting`
--
ALTER TABLE `voting`
  ADD CONSTRAINT `fk_voting_kontestan` FOREIGN KEY (`kontestan_id`) REFERENCES `kontestan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_voting_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
