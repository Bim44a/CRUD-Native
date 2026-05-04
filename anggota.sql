-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 04 Bulan Mei 2026 pada 22.11
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perpustakaan`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `anggota`
--

CREATE TABLE `anggota` (
  `id_anggota` int(11) NOT NULL,
  `kode_anggota` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telepon` varchar(15) NOT NULL,
  `alamat` text NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `pekerjaan` varchar(50) DEFAULT NULL,
  `tanggal_daftar` date NOT NULL,
  `status` enum('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `anggota`
--

INSERT INTO `anggota` (`id_anggota`, `kode_anggota`, `nama`, `email`, `telepon`, `alamat`, `tanggal_lahir`, `jenis_kelamin`, `pekerjaan`, `tanggal_daftar`, `status`, `foto`, `created_at`, `updated_at`) VALUES
(1, 'AGT-001', 'Budi Santoso', 'budi.santoso@email.com', '081234567890', 'Jl. Merdeka No. 10, Jakarta', '1995-05-15', 'Laki-laki', 'Mahasiswa', '2024-01-10', 'Aktif', NULL, '2026-04-23 13:11:16', '2026-04-23 13:11:16'),
(2, 'AGT-002', 'Siti Nurhaliza', 'siti.nur@email.com', '081234567891', 'Jl. Sudirman No. 25, Bandung', '1998-08-20', 'Perempuan', 'Pegawai', '2024-01-15', 'Aktif', NULL, '2026-04-23 13:11:16', '2026-04-23 13:11:16'),
(3, 'AGT-003', 'Ahmad Dhani', 'ahmad.dhani@email.com', '081234567892', 'Jl. Gatot Subroto No. 5, Surabaya', '1992-03-10', 'Laki-laki', 'Pegawai', '2024-02-01', 'Aktif', NULL, '2026-04-23 13:11:16', '2026-04-23 13:11:16'),
(4, 'AGT-004', 'Dewi Lestari', 'dewi.lestari@email.com', '081234567893', 'Jl. Ahmad Yani No. 30, Yogyakarta', '2000-12-05', 'Perempuan', 'Mahasiswa', '2024-02-10', 'Aktif', NULL, '2026-04-23 13:11:16', '2026-04-23 13:11:16'),
(5, 'AGT-005', 'Rizky Febian', 'rizky.feb@email.com', '081234567894', 'Jl. Diponegoro No. 15, Semarang', '1997-07-18', 'Laki-laki', 'Pelajar', '2024-02-15', 'Nonaktif', NULL, '2026-04-23 13:11:16', '2026-04-23 13:11:16'),
(6, 'AGT-006', 'Andi Saputra', 'andi1@email.com', '081234567801', 'Pekalongan', '2000-01-01', 'Laki-laki', 'Karyawan', '2024-01-01', 'Aktif', NULL, '2026-05-02 09:20:53', '2026-05-02 09:20:53'),
(7, 'AGT-007', 'Budi Wijaya', 'budi2@email.com', '081234567802', 'Batang', '1998-05-10', 'Laki-laki', 'Mahasiswa', '2024-01-02', 'Aktif', NULL, '2026-05-02 09:20:53', '2026-05-02 09:20:53'),
(8, 'AGT-008', 'Citra Dewi', 'citra3@email.com', '081234567803', 'Pemalang', '1995-07-12', 'Perempuan', 'Guru', '2024-01-03', 'Nonaktif', NULL, '2026-05-02 09:20:53', '2026-05-02 09:20:53'),
(9, 'AGT-009', 'Dewi Lestari', 'dewi4@email.com', '081234567804', 'Tegal', '1999-03-20', 'Perempuan', 'Dokter', '2024-01-04', 'Aktif', NULL, '2026-05-02 09:20:53', '2026-05-02 09:20:53'),
(10, 'AGT-010', 'Eko Prasetyo', 'eko5@email.com', '081234567805', 'Pekalongan', '2001-09-15', 'Laki-laki', 'Mahasiswa', '2024-01-05', 'Aktif', NULL, '2026-05-02 09:20:53', '2026-05-02 09:20:53'),
(11, 'AGT-011', 'Bima Adi Nugroho', 'bimaadi44@email.com', '087745514313', 'Pekalongan', '1997-11-11', 'Laki-laki', 'Programmer', '2024-01-06', 'Nonaktif', NULL, '2026-05-02 09:20:53', '2026-05-02 09:20:53'),
(12, 'AGT-012', 'Gita Permata', 'gita7@email.com', '081234567807', 'Malang', '1996-02-14', 'Perempuan', 'Designer', '2024-01-07', 'Aktif', NULL, '2026-05-02 09:20:53', '2026-05-02 09:20:53'),
(13, 'AGT-013', 'Hendra Gunawan', 'hendra8@email.com', '081234567808', 'Palembang', '1994-06-18', 'Laki-laki', 'Wiraswasta', '2024-01-08', 'Aktif', NULL, '2026-05-02 09:20:53', '2026-05-02 09:20:53'),
(14, 'AGT-014', 'Intan Sari', 'intan9@email.com', '081234567809', 'Semarang', '2002-12-25', 'Perempuan', 'Mahasiswa', '2024-01-09', 'Aktif', NULL, '2026-05-02 09:20:53', '2026-05-02 09:20:53'),
(15, 'AGT-015', 'Joko Susilo', 'joko10@email.com', '081234567810', 'Bekasi', '1993-08-08', 'Laki-laki', 'Karyawan', '2024-01-10', 'Nonaktif', NULL, '2026-05-02 09:20:53', '2026-05-02 09:20:53');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id_anggota`),
  ADD UNIQUE KEY `kode_anggota` (`kode_anggota`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id_anggota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
