<?php
require_once('../helper/connection.php');
require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');
session_start();

// Format tanggal
function formatTanggal($tanggal) {
    if (!$tanggal) return '-';
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $tgl = date('d', strtotime($tanggal));
    $bln = (int)date('m', strtotime($tanggal));
    $thn = date('Y', strtotime($tanggal));
    return $tgl . ' ' . $bulan[$bln] . ' ' . $thn;
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

// Query gabungan
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

// Eksekusi
$result = mysqli_query($connection, $query);
if (!$result) die("Query gagal: " . mysqli_error($connection));

// PDF
$pdf = new TCPDF();
$pdf->SetMargins(5, 10, 10);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

// Header
$pdf->Image('../img/logo.png', 10, 5, 14);
$pdf->Line(10, 18, 200, 18);
$pdf->Cell(0, 10, 'Rekap Laporan Absensi Karyawan', 0, 1, 'C');
$pdf->Ln(10);

// Filter Info
if (!empty($filter_username)) {
    $label = 'Nama Karyawan : ';
    $value = $filter_username;
    $pdf->SetFont('helvetica', 'B', 11);
    $labelWidth = $pdf->GetStringWidth($label) + 2;
    $pdf->SetFont('helvetica', '', 11);
    $valueWidth = $pdf->GetStringWidth($value) + 2;
    $x = ($pdf->GetPageWidth() - $labelWidth - $valueWidth) / 2;
    $pdf->SetX($x);
    $pdf->Cell($labelWidth, 8, $label, 0, 0);
    $pdf->Cell($valueWidth, 8, $value, 0, 1);
    $pdf->Ln(5);
}
if (!empty($start_date) && !empty($end_date)) {
    $pdf->SetFont('helvetica', '', 9);
    $tanggal_range = 'Tanggal: ' . formatTanggal($start_date) . ' s/d ' . formatTanggal($end_date);
    $textWidth = $pdf->GetStringWidth($tanggal_range);
    $x = ($pdf->GetPageWidth() - $textWidth) / 2;
    $pdf->SetX($x);
    $pdf->Cell($textWidth, 8, $tanggal_range, 0, 1);
    $pdf->Ln(5);
}

// Table Header
$pdf->SetX(5);
$pdf->SetFillColor(52, 152, 219);
$pdf->SetTextColor(255);
$pdf->SetFont('helvetica', 'B', 10);

$headers = ['No', 'Nama Karyawan', 'Tanggal', 'Jam Masuk', 'Jam Pulang', 'Durasi Kerja', 'Status'];
$widths = [10, 30, 60, 20, 22, 35, 20];
foreach ($headers as $i => $header) {
    $pdf->Cell($widths[$i], 10, $header, 1, 0, 'C', 1);
}
$pdf->Ln();

// Table Body
$pdf->SetFont('helvetica', '', 8);
$pdf->SetFillColor(255);
$pdf->SetTextColor(0);
$fill = false;
$no = 1;

while ($row = mysqli_fetch_assoc($result)) {
    $status = $row['status'];

    if ($status === 'Hadir') {
        $tanggal = formatTanggal($row['tanggal']);
        $jam_masuk = $row['login_pertama'] ?? '';
        $jam_pulang = $row['logout_terakhir'] ?? 'Belum Logout';
        $durasi = formatDurasiKerja($row['durasi_kerja']);
    } else {
        $tanggal = formatTanggal($row['tanggal_mulai']) . ' s/d ' . formatTanggal($row['tanggal_selesai']);
        $jam_masuk = $jam_pulang = $durasi = '';
    }

    $pdf->SetX(5);
    $pdf->Cell($widths[0], 10, $no++, 1, 0, 'C', $fill);
    $pdf->Cell($widths[1], 10, $row['username'], 1, 0, 'C', $fill);
    $pdf->Cell($widths[2], 10, $tanggal, 1, 0, 'C', $fill);
    $pdf->Cell($widths[3], 10, $jam_masuk, 1, 0, 'C', $fill);
    $pdf->Cell($widths[4], 10, $jam_pulang, 1, 0, 'C', $fill);
    $pdf->Cell($widths[5], 10, $durasi, 1, 0, 'C', $fill);
    $pdf->Cell($widths[6], 10, $status, 1, 0, 'C', $fill);
    $pdf->Ln();
    $fill = !$fill;
}

$pdf->Output('Rekap Absensi Karyawan.pdf', 'D');
exit;
?>
