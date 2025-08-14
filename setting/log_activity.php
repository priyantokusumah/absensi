<?php
require_once '../layout/_top.php';
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
$result = mysqli_query($connection, "SELECT * FROM log_activity ORDER BY waktu DESC");
?>

<section class="section">
  <div class="section-header">
    <h1>Riwayat Aktivitas</h1>
  </div>

  <div class="card">
    <div class="card-body table-responsive">
      <table class="table table-striped table-hover" id="table-1">
        <thead>
          <tr>
            <th>No</th>
            <th width="15%">Tanggal</th>
            <th>Waktu</th>
            <th>Username</th>
            <th>Role</th>
            <th>Menu</th>
            <th>Deskripsi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          while ($row = mysqli_fetch_assoc($result)) :
          ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?php echo formatTanggal($row['tanggal']); ?></td>
              <td><?= date('H:i:s', strtotime($row['waktu'])) ?></td>
              <td><?= htmlspecialchars($row['username']) ?></td>
              <td><span class="badge badge-info"><?= $row['role'] ?></span></td>
              <td><span class="badge badge-secondary"><?= $row['menu'] ?></span></td>
              <td><?= ($row['aksi']) ?></td>
            </tr>
          <?php endwhile; ?>
          <?php if (mysqli_num_rows($result) === 0): ?>
            <tr><td colspan="6" class="text-center text-muted">Belum ada log aktivitas</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>
<script src="../assets/js/page/modules-datatables.js"></script>
