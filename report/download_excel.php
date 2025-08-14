<?php
require_once('../helper/connection.php');

function formatTanggal($tanggal) {
    return $tanggal ? date('d-m-Y', strtotime($tanggal)) : '-';
}

function formatDurasiKerja($durasi) {
    if (!$durasi || $durasi === 'Belum Ada Durasi Kerja') return 'Belum Ada Durasi Kerja';
    list($jam, $menit, $detik) = explode(":", $durasi);
    $hasil = [];
    if ($jam > 0) $hasil[] = "$jam jam";
    if ($menit > 0) $hasil[] = "$menit menit";
    return implode(" ", $hasil);
}

// Filter
$filter_username = $_GET['username'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$filter_status = $_GET['status'] ?? '';

// Query
$query = "
SELECT * FROM (
    SELECT 
        pegawai.username,
        login_data.tanggal AS tanggal,
        NULL AS tanggal_mulai,
        NULL AS tanggal_selesai,
        login_data.login_pertama,
        logout_data.logout_terakhir,
        CASE 
            WHEN logout_data.logout_terakhir IS NULL 
            THEN 'Belum Ada Durasi Kerja'
            ELSE TIMEDIFF(logout_data.logout_terakhir, login_data.login_pertama) 
        END AS durasi_kerja,
        'Hadir' AS status
    FROM (
        SELECT username, DATE(time) AS tanggal, MIN(TIME(time)) AS login_pertama
        FROM login
        GROUP BY username, DATE(time)
    ) AS login_data
    LEFT JOIN (
        SELECT username, DATE(logout_time) AS tanggal, MAX(TIME(logout_time)) AS logout_terakhir
        FROM logout
        GROUP BY username, DATE(logout_time)
    ) AS logout_data 
    ON login_data.username = logout_data.username AND login_data.tanggal = logout_data.tanggal
    JOIN pegawai ON login_data.username = pegawai.username
    WHERE pegawai.username LIKE '%$filter_username%'
    " . ($start_date && $end_date ? " AND login_data.tanggal BETWEEN '$start_date' AND '$end_date'" : "") . "

    UNION

    SELECT 
        pegawai.username,
        NULL AS tanggal,
        izin.tanggal_mulai,
        izin.tanggal_selesai,
        NULL, NULL, NULL,
        'Izin' AS status
    FROM izin
    JOIN pegawai ON izin.id_pegawai = pegawai.id_pegawai
    WHERE pegawai.username LIKE '%$filter_username%' AND izin.status = 'Disetujui'
    " . ($start_date && $end_date ? " AND izin.tanggal_mulai BETWEEN '$start_date' AND '$end_date'" : "") . "

    UNION

    SELECT 
        pegawai.username,
        NULL AS tanggal,
        cuti.tanggal_mulai,
        cuti.tanggal_selesai,
        NULL, NULL, NULL,
        'Cuti' AS status
    FROM cuti
    JOIN pegawai ON cuti.id_pegawai = pegawai.id_pegawai
    WHERE pegawai.username LIKE '%$filter_username%' AND cuti.status = 'Disetujui'
    " . ($start_date && $end_date ? " AND cuti.tanggal_mulai BETWEEN '$start_date' AND '$end_date'" : "") . "

    UNION

    SELECT 
        pegawai.username,
        NULL AS tanggal,
        onsite.tanggal_mulai,
        onsite.tanggal_selesai,
        NULL, NULL, NULL,
        'Onsite' AS status
    FROM onsite
    JOIN pegawai ON onsite.id_pegawai = pegawai.id_pegawai
    WHERE pegawai.username LIKE '%$filter_username%'
    " . ($start_date && $end_date ? " AND onsite.tanggal_mulai BETWEEN '$start_date' AND '$end_date'" : "") . "
) AS data_utama
";

if (!empty($filter_status)) {
    $query .= " WHERE status = '$filter_status'";
}
$query .= " ORDER BY COALESCE(tanggal, tanggal_mulai) ASC";

// Eksekusi query
$result = mysqli_query($connection, $query);
if (!$result) die("Query gagal: " . mysqli_error($connection));

// Header Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"Rekap_Absensi_Karyawan.xls\"");
header("Pragma: no-cache");
header("Expires: 0");

// Output HTML dengan styling
echo "
<html>
<head>
<meta charset='UTF-8'>
<style>
    table {
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        font-size: 10pt;
    }
    th, td {
        border: 1px solid #000;
        padding: 5px;
        text-align: center;
    }
    th {
        font-weight: bold;
    }
</style>
</head>
<body>

<h3 style='text-align:center;'>Rekap Absensi Karyawan</h3>
<table>
<tr>
    <th>No</th>
    <th>Nama Karyawan</th>
    <th>Tanggal</th>
    <th>Jam Masuk</th>
    <th>Jam Pulang</th>
    <th>Durasi Kerja</th>
    <th>Status</th>
</tr>
";

$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $status = $row['status'];

    if ($status === 'Hadir') {
        $tanggal = formatTanggal($row['tanggal']);
        $jam_masuk = $row['login_pertama'] ?? '-';
        $jam_pulang = $row['logout_terakhir'] ? $row['logout_terakhir'] : 'Belum Logout';
        $durasi = formatDurasiKerja($row['durasi_kerja']);
    } else {
        $tanggal = formatTanggal($row['tanggal_mulai']) . ' sampai dengan ' . formatTanggal($row['tanggal_selesai']);
        $jam_masuk = $jam_pulang = $durasi = '-';
    }

    echo "<tr>
        <td>{$no}</td>
        <td>{$row['username']}</td>
        <td>{$tanggal}</td>
        <td>{$jam_masuk}</td>
        <td>{$jam_pulang}</td>
        <td>{$durasi}</td>
        <td>{$status}</td>
    </tr>";
    $no++;
}

echo "</table></body></html>";
exit;
?>
