-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 14, 2025 at 08:08 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `absensi_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cuti`
--

CREATE TABLE `cuti` (
  `id_cuti` int(11) NOT NULL,
  `id_pegawai` int(11) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `alasan` text DEFAULT NULL,
  `status` enum('Pending','Disetujui','Ditolak') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cuti`
--

INSERT INTO `cuti` (`id_cuti`, `id_pegawai`, `tanggal_mulai`, `tanggal_selesai`, `alasan`, `status`) VALUES
(16, 18, '2025-07-19', '2025-07-20', 'test', 'Disetujui'),
(17, 18, '2025-07-23', '2025-07-24', 'Test', 'Disetujui'),
(18, 18, '2025-07-23', '2025-07-24', 'ADa urusuan keluarga yang tidak dapat saya  tunda di jakarta kalau mau ikut hayu we karena ini cuma test\r\n', 'Disetujui');

-- --------------------------------------------------------

--
-- Table structure for table `holiday`
--

CREATE TABLE `holiday` (
  `id` int(11) NOT NULL,
  `nama_libur` varchar(100) DEFAULT NULL,
  `tanggal_libur` date DEFAULT NULL,
  `jenis_libur` enum('Nasional','Cuti Bersama','Custom') DEFAULT 'Nasional'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `izin`
--

CREATE TABLE `izin` (
  `id_izin` int(11) NOT NULL,
  `id_pegawai` int(11) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `alasan` text NOT NULL,
  `file_izin` varchar(255) NOT NULL,
  `status` enum('Pending','Disetujui','Ditolak') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `username` varchar(10) NOT NULL,
  `Time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`id`, `username`, `Time`) VALUES
(263, 'marcel', '2025-02-13 00:39:38'),
(264, 'pito', '2025-02-13 00:43:25'),
(265, 'marcel', '2025-02-25 00:39:38');

-- --------------------------------------------------------

--
-- Table structure for table `logout`
--

CREATE TABLE `logout` (
  `id` int(11) NOT NULL,
  `username` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `logout_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logout`
--

INSERT INTO `logout` (`id`, `username`, `logout_time`) VALUES
(986, 'marcel', '2025-02-13 10:26:01');

-- --------------------------------------------------------

--
-- Table structure for table `log_activity`
--

CREATE TABLE `log_activity` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `menu` varchar(100) DEFAULT NULL,
  `aksi` text DEFAULT NULL,
  `tanggal` date DEFAULT curdate(),
  `waktu` time DEFAULT curtime()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_activity`
--

INSERT INTO `log_activity` (`id`, `username`, `role`, `menu`, `aksi`, `tanggal`, `waktu`) VALUES
(10, 'Admin', 'Manajemen', 'Cuti', 'Mengubah status cuti user <b>marcel</b> dari <b>Pending</b> ke <b>Disetujui</b>', '2025-07-18', '17:37:15'),
(11, 'marcel', 'Karyawan', 'Izin', 'Melakukan Pengajuan izin', '2025-07-18', '17:38:16'),
(12, 'Admin', 'Manajemen', 'Izin', 'Mengubah status izin user <b>marcel</b> dari <b>Pending</b> ke <b>Disetujui</b>', '2025-07-18', '17:38:32'),
(13, 'marcel', 'Karyawan', 'Cuti', 'Melakukan Pengajuan Cuti', '2025-07-22', '17:40:29'),
(14, 'Admin', 'Manajemen', 'Cuti', 'Mengubah status cuti user <b>marcel</b> dari <b>Pending</b> ke <b>Disetujui</b>', '2025-07-22', '17:40:50'),
(15, 'Admin', 'Manajemen', 'Pegawai', 'Mengubah data pegawai username : Status dari \'Tidak Aktif\' menjadi \'Aktif\'', '2025-07-22', '17:42:11'),
(16, 'Admin', 'Manajemen', 'Pegawai', 'Mengubah data pegawai username : Status dari \'Aktif\' menjadi \'Tidak Aktif\'', '2025-07-22', '17:43:40'),
(17, 'Admin', 'Manajemen', 'Pegawai', 'Mengubah data pegawai username : Status dari \'<b>Tidak Aktif</b>\' menjadi \'<b>Pending</b>\'', '2025-07-22', '17:46:00'),
(18, 'Admin', 'Manajemen', 'Pegawai', 'Mengubah data pegawai username : Status dari \'<b>Pending</b>\' menjadi \'<b>Aktif</b>\'', '2025-07-22', '17:47:14'),
(19, 'birril', 'Karyawan', 'Izin', 'Melakukan Pengajuan izin', '2025-07-22', '17:47:51'),
(20, 'Admin', 'Manajemen', 'Izin', 'Mengubah status izin user <b>birril</b> dari <b>Pending</b> ke <b>Disetujui</b>', '2025-07-22', '17:48:10'),
(21, 'marcel', 'Karyawan', 'Cuti', 'Melakukan Pengajuan Cuti', '2025-07-22', '19:12:22'),
(22, 'marcel', 'Karyawan', 'Izin', 'Melakukan Pengajuan izin', '2025-08-01', '13:50:03'),
(23, 'Admin', 'Manajemen', 'Izin', 'Mengubah status izin user <b>marcel</b> dari <b>Pending</b> ke <b>Disetujui</b>', '2025-08-01', '13:50:35'),
(24, 'Admin', 'Manajemen', 'Cuti', 'Mengubah status cuti user <b>marcel</b> dari <b>Pending</b> ke <b>Disetujui</b>', '2025-08-01', '18:30:52');

-- --------------------------------------------------------

--
-- Table structure for table `onsite`
--

CREATE TABLE `onsite` (
  `id_onsite` int(11) NOT NULL,
  `id_pegawai` int(11) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `alasan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `onsite`
--

INSERT INTO `onsite` (`id_onsite`, `id_pegawai`, `tanggal_mulai`, `tanggal_selesai`, `alasan`) VALUES
(2, 18, '2025-08-02', '2025-08-03', 'test');

-- --------------------------------------------------------

--
-- Table structure for table `pegawai`
--

CREATE TABLE `pegawai` (
  `id_pegawai` int(11) NOT NULL,
  `username` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(100) NOT NULL,
  `password` varchar(250) NOT NULL,
  `status` enum('Pending','Aktif','Tidak Aktif') DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pegawai`
--

INSERT INTO `pegawai` (`id_pegawai`, `username`, `role`, `password`, `status`) VALUES
(14, 'pito', 'Karyawan', '$2y$10$qXR3z0eaSrUalSQAoZzsX.kcozJtskkOXFo9njrmauxMnKPZ57m9O', 'Aktif'),
(18, 'marcel', 'Karyawan', '$2y$10$Hcezbm.KBKul57//i2s2Re05UMrxl8nWFj./g/5eAYF8Dv8d9Bc3q', 'Aktif'),
(21, 'Admin', 'Manajemen', '$2y$10$7Ap1jwpKzLuWhQoYrtY45Oqpy7y5zdOzwfr1PUdYhy/jqHoyuENdq', 'Aktif'),
(22, 'birril', 'Karyawan', '$2y$10$fEEOMcLc/QoI8rMVDSs1zeitwaSzDPbOaaI5C8mrjKFhJ2Jdcm4wa', 'Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'judul_dashboard', 'Absensi Karyawan Phire Studio'),
(2, 'judul_data', 'List Data Pegawai'),
(3, 'judul_izin', 'Manajemen Izin Karyawan'),
(4, 'judul_cuti', 'Manajemen Cuti Karyawan'),
(5, 'judul_report', 'Report Absensi Karyawan '),
(6, 'judul_dashboard_karyawan', 'Selamat Datang Di Dashbord Absensi Karyawan '),
(7, 'judul_izin_karyawan', 'Riwayat Izin Karyawan'),
(8, 'judul_cuti_karyawan', 'Riwayat Cuti Karyawan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cuti`
--
ALTER TABLE `cuti`
  ADD PRIMARY KEY (`id_cuti`),
  ADD KEY `id_pegawai` (`id_pegawai`);

--
-- Indexes for table `holiday`
--
ALTER TABLE `holiday`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `izin`
--
ALTER TABLE `izin`
  ADD PRIMARY KEY (`id_izin`),
  ADD KEY `id_pegawai` (`id_pegawai`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logout`
--
ALTER TABLE `logout`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log_activity`
--
ALTER TABLE `log_activity`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `onsite`
--
ALTER TABLE `onsite`
  ADD PRIMARY KEY (`id_onsite`),
  ADD KEY `id_pegawai` (`id_pegawai`);

--
-- Indexes for table `pegawai`
--
ALTER TABLE `pegawai`
  ADD PRIMARY KEY (`id_pegawai`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cuti`
--
ALTER TABLE `cuti`
  MODIFY `id_cuti` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `holiday`
--
ALTER TABLE `holiday`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `izin`
--
ALTER TABLE `izin`
  MODIFY `id_izin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=266;

--
-- AUTO_INCREMENT for table `logout`
--
ALTER TABLE `logout`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=987;

--
-- AUTO_INCREMENT for table `log_activity`
--
ALTER TABLE `log_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `onsite`
--
ALTER TABLE `onsite`
  MODIFY `id_onsite` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pegawai`
--
ALTER TABLE `pegawai`
  MODIFY `id_pegawai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cuti`
--
ALTER TABLE `cuti`
  ADD CONSTRAINT `cuti_ibfk_1` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id_pegawai`) ON DELETE CASCADE;

--
-- Constraints for table `izin`
--
ALTER TABLE `izin`
  ADD CONSTRAINT `izin_ibfk_1` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id_pegawai`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
