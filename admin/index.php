<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Hitung total pegawai
$pegawai = mysqli_query($connection, "SELECT COUNT(*) FROM pegawai");
$total_pegawai = mysqli_fetch_array($pegawai)[0];

// Hitung izin pending
$izin_pending = mysqli_query($connection, "SELECT COUNT(*) FROM izin WHERE status = 'Pending'");
$total_izin_pending = mysqli_fetch_array($izin_pending)[0];

// Hitung cuti pending
$cuti_pending = mysqli_query($connection, "SELECT COUNT(*) FROM cuti WHERE status = 'Pending'");
$total_cuti_pending = mysqli_fetch_array($cuti_pending)[0];

// Tanggal hari ini
$tanggal_hari_ini = date('Y-m-d');

// Ambil data login pertama hari ini, kecuali yang sedang cuti atau izin
$query_login_pertama = "
    SELECT l.username, TIME(MIN(l.time)) AS login_pertama
    FROM login l
    WHERE DATE(l.time) = '$tanggal_hari_ini'
      AND l.username NOT IN (
        SELECT username FROM cuti
        WHERE status = 'Disetujui'
          AND '$tanggal_hari_ini' BETWEEN tanggal_mulai AND tanggal_selesai
      )
      AND l.username NOT IN (
        SELECT p.username FROM izin i
        JOIN pegawai p ON i.id_pegawai = p.id_pegawai
        WHERE i.status = 'Disetujui'
          AND '$tanggal_hari_ini' BETWEEN i.tanggal_mulai AND i.tanggal_selesai
      )
    GROUP BY l.username
    ORDER BY login_pertama ASC
";
$login_pertama_result = mysqli_query($connection, $query_login_pertama);

// Ambil judul dashboard dari settings
$res = mysqli_query($connection, "SELECT setting_value FROM settings WHERE setting_key = 'judul_dashboard'");
$row = mysqli_fetch_assoc($res);
$judul = $row ? $row['setting_value'] : 'Judul Dashboard';
?>

<section class="section">
  <div class="section-header" style="display: flex; justify-content: space-between; width: 100%;">
    <h1><?= htmlspecialchars($judul) ?></h1>
    <h1 id="waktu-sekarang"></h1>
  </div>

  <div class="column">
    <div class="row">
      <div class="col-lg-4 col-md-6 col-sm-6 col-12">
        <a href="../data_pegawai/index.php" style="text-decoration: none;">
          <div class="card card-statistic-1">
            <div class="card-icon bg-primary"><i class="far fa-user"></i></div>
            <div class="card-wrap">
              <div class="card-header"><h4>Total Karyawan</h4></div>
              <div class="card-body"><?= $total_pegawai ?></div>
            </div>
          </div>
        </a>
      </div>

      <div class="col-lg-4 col-md-6 col-sm-6 col-12">
        <a href="../izin/index.php" style="text-decoration: none;">
          <div class="card card-statistic-1">
            <div class="card-icon bg-warning"><i class="fas fa-tasks"></i></div>
            <div class="card-wrap">
              <div class="card-header"><h4>Izin Belum Disetujui</h4></div>
              <div class="card-body"><?= $total_izin_pending ?></div>
            </div>
          </div>
        </a>
      </div>

      <div class="col-lg-4 col-md-6 col-sm-6 col-12">
        <a href="../cuti/index.php" style="text-decoration: none;">
          <div class="card card-statistic-1">
            <div class="card-icon bg-danger"><i class="fas fa-calendar"></i></div>
            <div class="card-wrap">
              <div class="card-header"><h4>Cuti Belum Disetujui</h4></div>
              <div class="card-body"><?= $total_cuti_pending ?></div>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h4>Urutan Karyawan Datang Hari Ini : <span id="tanggal-sekarang"><?= date('d F Y') ?></span></h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Peringkat</th>
              <th>Nama Karyawan</th>
              <th>Jam Masuk</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $peringkat = 1;
            while ($row = mysqli_fetch_assoc($login_pertama_result)) { ?>
              <tr>
                <td><?= $peringkat++ ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= $row['login_pertama'] ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<script>
  function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('id-ID', { hour12: false });
    document.getElementById("waktu-sekarang").innerText = timeString;
  }

  updateTime();
  setInterval(updateTime, 1000);
</script>

<?php require_once '../layout/_bottom.php'; ?>
