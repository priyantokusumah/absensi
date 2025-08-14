<?php
require_once '../layout/_topkaryawan.php';
require_once '../helper/connection.php';

$id_pegawai = $_SESSION['id_pegawai'];

// Total cuti tahunan yang diberikan
$jatah_cuti = 12;

// Ambil total cuti yang sudah disetujui
$query = "SELECT SUM(DATEDIFF(tanggal_selesai, tanggal_mulai) + 1) AS total_cuti FROM cuti WHERE id_pegawai = ? AND status = 'Disetujui'";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $id_pegawai);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_cuti_diambil = $row['total_cuti'] ?? 0;

// Hitung sisa cuti
$sisa_cuti = max($jatah_cuti - $total_cuti_diambil, 0);
$res = mysqli_query($connection, "SELECT setting_value FROM settings WHERE setting_key = 'judul_dashboard_karyawan'");
$row_judul = mysqli_fetch_assoc($res);
$judul = $row_judul['setting_value'] ?? 'Judul Dashboard Karyawan';

?>

<section class="section">
<div class="section-header" style="display: flex; justify-content: space-between; width: 100%;">
         <h1><?= htmlspecialchars($judul) ?></h1>
    <h1 id="waktu-sekarang"></h1>
  </div>
  <div class="column">
    <div class="row">
      <div class="col-lg-4 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-calendar-check"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Sisa Cuti Tahunan</h4>
            </div>
            <div class="card-body">
              <?= $sisa_cuti ?> Hari
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script>
  function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('id-ID', { hour12: false }); // Format 24 jam
    document.getElementById("waktu-sekarang").innerText = timeString;
  }

  // Panggil updateTime pertama kali dan atur interval 1 detik
  updateTime();
  setInterval(updateTime, 1000);
</script>
<?php
require_once '../layout/_bottom.php';
?>
