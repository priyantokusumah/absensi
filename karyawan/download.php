<?php
require_once '../helper/connection.php';
require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');
session_start();

function formatDurasiKerja($durasi)
{
    if (!$durasi || $durasi === 'Belum Ada Durasi Kerja') {
        return 'Belum Ada Durasi Kerja';
    }

    $parts = explode(":", $durasi);

    if (count($parts) < 3) {
        return 'Belum Ada Durasi Kerja';
    }

    list($jam, $menit, $detik) = $parts;
    $hasil = [];
    if ($jam > 0) $hasil[] = "$jam jam";
    if ($menit > 0) $hasil[] = "$menit menit";
    return implode(" ", $hasil);
}

$id_pegawai = $_SESSION['id_pegawai'];
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Ambil nama pegawai
$query_pegawai = "SELECT username FROM pegawai WHERE id_pegawai = '$id_pegawai'";
$result_pegawai = mysqli_query($connection, $query_pegawai);
$pegawai = mysqli_fetch_assoc($result_pegawai);
$username = $pegawai['username'] ?? 'Tidak Diketahui';

// Ambil data absensis
$query = "
    SELECT 
        p.username,
        DATE(l.time) AS tanggal,
        TIME(MIN(l.time)) AS login_pertama,
        TIME(MAX(lo.logout_time)) AS logout_terakhir,
        CASE 
            WHEN MAX(lo.logout_time) IS NULL 
            THEN 'Belum Ada Durasi Kerja'
            ELSE TIMEDIFF(MAX(lo.logout_time), MIN(l.time)) 
        END AS durasi_kerja
    FROM login l
    LEFT JOIN pegawai p ON l.username = p.username
    LEFT JOIN logout lo ON lo.username = l.username AND DATE(lo.logout_time) = DATE(l.time)
    WHERE p.id_pegawai = '$id_pegawai'
";

// **Tambahkan filter tanggal jika ada**
if (!empty($start_date) && !empty($end_date)) {
    $query .= " AND DATE(l.time) BETWEEN '$start_date' AND '$end_date'";
}

// **Tambahkan GROUP BY yang benar**
$query .= " GROUP BY p.username, tanggal  
            ORDER BY tanggal ASC, logout_terakhir ASC;";


$result = mysqli_query($connection, $query);

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

// Judul
$pdf->Image('../img/logo.png', 10, 5, 14);
$pdf->SetLineWidth(0.5);
$pdf->Line(10, 18, 200, 18);
$pdf->Cell(0, 10, 'Laporan Absensi', 0, 1, 'C');
$pdf->Cell(0, 10, "Nama Karyawan: $username", 0, 1, 'C');

if (!empty($start_date) && !empty($end_date)) {
    $pdf->Cell(0, 10, "Periode: " . date('d-m-Y', strtotime($start_date)) . " sampai dengan " . date('d-m-Y', strtotime($end_date)), 0, 1, 'C');
}

$pdf->Ln(10);

// Header tabel
$pdf->SetFillColor(52, 152, 219);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('helvetica', 'B', 10);
$widths = [10,40, 40, 40, 60];
$headers = ['No', 'Tanggal', 'Jam Masuk', 'Jam Pulang', 'Durasi Kerja'];

foreach ($headers as $i => $header) {
    $pdf->Cell($widths[$i], 10, $header, 1, 0, 'C', 1);
}
$pdf->Ln();

// Data tabel
$pdf->SetFont('helvetica', '', 10);
$pdf->SetTextColor(0, 0, 0);
$fill = false;
$no = 1;

while ($row = mysqli_fetch_assoc($result)) {
    $pdf->SetFillColor(240, 240, 240);
    $pdf->Cell($widths[0], 10, $no++, 1, 0, 'C', $fill);
    $pdf->Cell($widths[1], 10, date('d-m-Y', strtotime($row['tanggal'])), 1, 0, 'C', $fill);
    $pdf->Cell($widths[2], 10, $row['login_pertama'], 1, 0, 'C', $fill);
    $pdf->Cell($widths[3], 10, empty($row['logout_terakhir']) ? 'Belum Logout' : $row['logout_terakhir'], 1, 0, 'C', $fill);
    $pdf->Cell($widths[4], 10, formatDurasiKerja($row['durasi_kerja']), 1, 0, 'C', $fill);
    $pdf->Ln();
    $fill = !$fill;
}

$pdf->Output('Laporan Absensi Karyawan.pdf', 'D');
exit;
?>
`