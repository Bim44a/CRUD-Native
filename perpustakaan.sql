DROP TABLE IF EXISTS `anggota`;

CREATE TABLE `anggota` (
  `id_anggota` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_anggota` VARCHAR(20) NOT NULL,
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `telepon` VARCHAR(15) NOT NULL,
  `alamat` TEXT NOT NULL,
  `tanggal_lahir` DATE NOT NULL,
  `jenis_kelamin` ENUM('Laki-laki','Perempuan') NOT NULL,
  `pekerjaan` VARCHAR(100) DEFAULT NULL,
  `tanggal_daftar` DATE NOT NULL,
  `status` ENUM('Aktif','Nonaktif') DEFAULT 'Aktif',
  `foto` VARCHAR(255) DEFAULT NULL,
  
  UNIQUE KEY `kode_anggota_unique` (`kode_anggota`),
  UNIQUE KEY `email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `anggota`
(`kode_anggota`, `nama`, `email`, `telepon`, `alamat`, `tanggal_lahir`, `jenis_kelamin`, `pekerjaan`, `tanggal_daftar`, `status`, `foto`)
VALUES
('AGT-001','Budi Santoso','budi1@email.com','081234567801','Jakarta','1995-01-10','Laki-laki','Karyawan','2024-01-01','Aktif',NULL),
('AGT-002','Siti Nurhaliza','siti2@email.com','081234567802','Bandung','1998-02-12','Perempuan','Guru','2024-01-02','Aktif',NULL),
('AGT-003','Ahmad Dhani','ahmad3@email.com','081234567803','Surabaya','1992-03-15','Laki-laki','Musisi','2024-01-03','Aktif',NULL),
('AGT-004','Dewi Lestari','dewi4@email.com','081234567804','Medan','1996-04-20','Perempuan','Penulis','2024-01-04','Aktif',NULL),
('AGT-005','Rizky Febian','rizky5@email.com','081234567805','Bekasi','2000-05-22','Laki-laki','Mahasiswa','2024-01-05','Nonaktif',NULL),

('AGT-006','Andi Saputra','andi6@email.com','081234567806','Jakarta','1999-06-10','Laki-laki','Karyawan','2024-01-06','Aktif',NULL),
('AGT-007','Budi Wijaya','budi7@email.com','081234567807','Bandung','1997-07-11','Laki-laki','Mahasiswa','2024-01-07','Aktif',NULL),
('AGT-008','Citra Dewi','citra8@email.com','081234567808','Surabaya','1995-08-12','Perempuan','Dokter','2024-01-08','Nonaktif',NULL),
('AGT-009','Dewi Anggraini','dewi9@email.com','081234567809','Medan','1993-09-13','Perempuan','Guru','2024-01-09','Aktif',NULL),
('AGT-010','Eko Prasetyo','eko10@email.com','081234567810','Semarang','2001-10-14','Laki-laki','Mahasiswa','2024-01-10','Aktif',NULL),

('AGT-011','Fajar Nugroho','fajar11@email.com','081234567811','Yogyakarta','1994-11-15','Laki-laki','Programmer','2024-01-11','Nonaktif',NULL),
('AGT-012','Gita Permata','gita12@email.com','081234567812','Malang','1996-12-16','Perempuan','Designer','2024-01-12','Aktif',NULL),
('AGT-013','Hendra Gunawan','hendra13@email.com','081234567813','Palembang','1992-01-17','Laki-laki','Wiraswasta','2024-01-13','Aktif',NULL),
('AGT-014','Intan Sari','intan14@email.com','081234567814','Makassar','2002-02-18','Perempuan','Mahasiswa','2024-01-14','Aktif',NULL),
('AGT-015','Joko Susilo','joko15@email.com','081234567815','Bekasi','1990-03-19','Laki-laki','Karyawan','2024-01-15','Nonaktif',NULL);

