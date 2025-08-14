<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

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

$filter_username = $_GET['username'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$filter_status = $_GET['status'] ?? '';

$query = "
SELECT * FROM (
    -- Data Hadir
    SELECT 
        pegawai.username,
        login_data.tanggal,
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

    -- Data Izin
    SELECT 
        pegawai.username,
        izin.tanggal_mulai AS tanggal,
        NULL AS login_pertama,
        NULL AS logout_terakhir,
        CONCAT(izin.tanggal_mulai, '||', izin.tanggal_selesai) AS durasi_kerja,
        'Izin' AS status
    FROM izin
    JOIN pegawai ON izin.id_pegawai = pegawai.id_pegawai
    WHERE pegawai.username LIKE '%$filter_username%' AND izin.status = 'Disetujui'
    " . ($start_date && $end_date ? " AND izin.tanggal_mulai BETWEEN '$start_date' AND '$end_date'" : "") . "

    UNION

    -- Data Cuti
    SELECT 
        pegawai.username,
        cuti.tanggal_mulai AS tanggal,
        NULL AS login_pertama,
        NULL AS logout_terakhir,
        CONCAT(cuti.tanggal_mulai, '||', cuti.tanggal_selesai) AS durasi_kerja,
        'Cuti' AS status
    FROM cuti
    JOIN pegawai ON cuti.id_pegawai = pegawai.id_pegawai
    WHERE pegawai.username LIKE '%$filter_username%' AND cuti.status = 'Disetujui'
    " . ($start_date && $end_date ? " AND cuti.tanggal_mulai BETWEEN '$start_date' AND '$end_date'" : "") . "

    UNION

    -- Data Onsite
    SELECT 
        pegawai.username,
        onsite.tanggal_mulai AS tanggal,
        NULL AS login_pertama,
        NULL AS logout_terakhir,
        CONCAT(onsite.tanggal_mulai, '||', onsite.tanggal_selesai) AS durasi_kerja,
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
$query .= " ORDER BY tanggal DESC";

$result = mysqli_query($connection, $query) or die("Query gagal: " . mysqli_error($connection));
$res = mysqli_query($connection, "SELECT setting_value FROM settings WHERE setting_key = 'judul_report'");
$row = mysqli_fetch_assoc($res);
$judul = $row['setting_value'] ?? 'Judul Report';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<section class="section">
    <div class="section-header">
        <h1><?= htmlspecialchars($judul) ?></h1>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped w-100" id="table-1">
                            <thead>
                                <form method="GET" action="">
                                    <div class="form-group">
                                        <button id="filterButton" class="btn btn-primary">Filter</button>
                                        <button id="resetButton" class="btn btn-danger">Reset Filter</button>
                                        <a href="download.php?username=<?= urlencode($filter_username); ?>&start_date=<?= $start_date; ?>&end_date=<?= $end_date; ?>&status=<?= urlencode($filter_status); ?>" target="_blank" class="btn btn-success">Download PDF</a>
                                        <a href="download_excel.php?username=<?= urlencode($filter_username); ?>&start_date=<?= $start_date; ?>&end_date=<?= $end_date; ?>&status=<?= urlencode($filter_status); ?>" target="_blank" class="btn btn-success">Download EXCEL</a>
                                    </div>
                                </form>
                                <tr>
                                    <th>Nama Karyawan</th>
                                    <th>Tanggal</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Pulang</th>
                                    <th>Durasi Kerja</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td><?= $row['username']; ?></td>
                                        <td>
                                            <?php
                                            if (($row['status'] === 'Izin' || $row['status'] === 'Cuti' || $row['status'] === 'Onsite') && strpos($row['durasi_kerja'], '||') !== false) {
                                                list($mulai, $selesai) = explode('||', $row['durasi_kerja']);
                                                echo formatTanggal($mulai) . ' sampai ' . formatTanggal($selesai);
                                            } else {
                                                echo formatTanggal($row['tanggal']);
                                            }
                                            ?>
                                        </td>
                                        <td><?= $row['login_pertama'] ?? ''; ?></td>
                                        <td>
                                            <?php
                                            if ($row['status'] === 'Hadir') {
                                                echo empty($row['logout_terakhir']) ? 'Belum Logout' : $row['logout_terakhir'];
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($row['status'] === 'Hadir') {
                                                echo formatDurasiKerja($row['durasi_kerja']);
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <span class="badge 
                                                <?= $row['status'] === 'Izin' ? 'badge-warning' : 
                                                    ($row['status'] === 'Cuti' ? 'badge-info' : 
                                                    ($row['status'] === 'Onsite' ? 'badge-primary' : 'badge-success')) ?>">
                                                <?= $row['status']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

<script>
document.getElementById('filterButton').addEventListener('click', function(event) {
    event.preventDefault();

    Swal.fire({
        title: '<h3 style="font-size: 25px; margin-bottom: 10px;">Filter Absensi</h3>',
        html: `
            <div style="display: flex; flex-direction: column; gap: 15px; text-align: left;">
                <div><label>Nama Pengguna:</label><input id="swal-username" class="swal2-input"></div>
                <div><label>Tanggal Awal:</label><input type="date" id="swal-start-date" class="swal2-input"></div>
                <div><label>Tanggal Akhir:</label><input type="date" id="swal-end-date" class="swal2-input"></div>
                <div>
                    <label>Status:</label>
                    <input list="status-options" id="swal-status" class="swal2-input" placeholder="Pilih status">
                    <datalist id="status-options">
                        <option value="Hadir">
                        <option value="Izin">
                        <option value="Cuti">
                        <option value="Onsite">
                    </datalist>
                </div>
            </div>`,
        showCancelButton: true,
        confirmButtonText: 'Filter',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const username = document.getElementById('swal-username').value.trim();
            const startDate = document.getElementById('swal-start-date').value;
            const endDate = document.getElementById('swal-end-date').value;
            const status = document.getElementById('swal-status').value;

            if (!username && !startDate && !endDate && !status) {
                Swal.showValidationMessage('Harap isi minimal satu filter!');
                return false;
            }

            let queryParams = [];
            if (username) queryParams.push('username=' + encodeURIComponent(username));
            if (startDate) queryParams.push('start_date=' + encodeURIComponent(startDate));
            if (endDate) queryParams.push('end_date=' + encodeURIComponent(endDate));
            if (status) queryParams.push('status=' + encodeURIComponent(status));

            window.location.href = '?' + queryParams.join('&');
        }
    });
});

document.getElementById('resetButton').addEventListener('click', function(event) {
    event.preventDefault();
    window.location.href = window.location.pathname;
});
</script>

            </div>
        </div>
    </div>
</section>
<?php require_once '../layout/_bottom.php'; ?>
<script src="../assets/js/page/modules-datatables.js"></script>
