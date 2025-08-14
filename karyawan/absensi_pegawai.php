<?php
require_once '../layout/_topkaryawan.php';
require_once '../helper/connection.php';

// Format tanggal dd-NamaBulan-YYYY
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

//Function untuk Durasi Kerja
function formatDurasiKerja($durasi) {
  if ($durasi === 'Belum Ada Durasi Kerja' || $durasi === null) {
      return 'Belum Ada Durasi Kerja';
  }

  list($jam, $menit, $detik) = explode(":", $durasi);
  
  $hasil = [];
  if ($jam > 0) {
      $hasil[] = "$jam jam";
  }
  if ($menit > 0) {
      $hasil[] = "$menit menit";
  }

  return implode(" ", $hasil);
}

// Ambil data absensi
$id_pegawai = $_SESSION['id_pegawai'];
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

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
            ORDER BY tanggal DESC, logout_terakhir DESC;";


// Menjalankan query dan pengecekan error
$result = mysqli_query($connection, $query);
if (!$result) {
    die("Query gagal: " . mysqli_error($connection));  // Menampilkan pesan error jika query gagal
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<section class="section">
<div class="section-header">
        <h1>Absensi Karyawan</h1>
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
            <a href="download.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" target="_blank" class="btn btn-success">Download PDF</a>
          </form>
                <tr>
                  <th>Nama Karyawan</th>
                  <th width="20%">Tanggal</th>
                  <th>Login Pertama</th>
                  <th>Logout Terakhir</th>
                  <th>Durasi Kerja</th>
                  <th>Mac Address</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                  <tr>
                      <td><?php echo $row['username']; ?></td>
                      <td>
                          <span style="font-size: 14px; display:flex; flex-direction: column;">
                              <?php echo formatTanggal($row['tanggal']); ?>
                          </span>
                      </td>
                      <td><?php echo $row['login_pertama']; ?></td>
                      <td>
                          <?php if (empty($row['logout_terakhir'])): ?>
                              <a href="http://192.168.10.1/status" class="btn btn-primary btn-sm" target="_blank">
                                  Belum Ada Jam Pulang
                              </a>
                          <?php else: ?>
                              <?php echo $row['logout_terakhir']; ?>
                          <?php endif; ?>
                      </td>
                      <td>
                          <?php echo formatDurasiKerja($row['durasi_kerja']); ?>
                      </td>
                      <td><?php echo $row['username']; ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
<script>
document.getElementById('filterButton').addEventListener('click', function(event) {
    event.preventDefault(); // Mencegah pengiriman form default

    Swal.fire({
        title: '<h3 style="font-size: 25px; margin-bottom: 10px;">Filter Absensi</h3>',
        html: `
            <div style="display: flex; flex-direction: column; gap: 15px; text-align: left;">
                <div style="display: flex; flex-direction: column; gap: 5px;">
                    <label style="font-weight: bold;">Tanggal Awal:</label>
                    <input type="date" id="swal-start-date" class="swal2-input" style="padding: 10px; font-size: 14px;">
                </div>
                <div style="display: flex; flex-direction: column; gap: 5px;">
                    <label style="font-weight: bold;">Tanggal Akhir:</label>
                    <input type="date" id="swal-end-date" class="swal2-input" style="padding: 10px; font-size: 14px;">
                </div>
            </div>`,
        showCancelButton: true,
        confirmButtonText: 'Filter',
        cancelButtonText: 'Batal',
        customClass: {
            confirmButton: 'swal2-confirm swal2-styled',
            cancelButton: 'swal2-cancel swal2-styled'
        },
        preConfirm: () => {
            const startDate = document.getElementById('swal-start-date').value;
            const endDate = document.getElementById('swal-end-date').value;

            if (!startDate || !endDate) {
                Swal.showValidationMessage('Harap pilih kedua tanggal!');
                return false;
            }

            let queryParams = [];
            queryParams.push('start_date=' + encodeURIComponent(startDate));
            queryParams.push('end_date=' + encodeURIComponent(endDate));

            window.location.href = '?' + queryParams.join('&');
        }
    });
});

document.getElementById('resetButton').addEventListener('click', function(event) {
    event.preventDefault(); // Mencegah pengiriman form default
    window.location.href = window.location.pathname; // Muat ulang halaman tanpa parameter query
});
</script>
      </div>
    </div>
  </div>
</section>
<?php require_once '../layout/_bottom.php'; ?>
<script src="../assets/js/page/modules-datatables.js"></script>