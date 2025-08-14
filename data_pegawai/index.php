<?php
session_start(); 
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Ambil data judul
$res = mysqli_query($connection, "SELECT setting_value FROM settings WHERE setting_key = 'judul_data'");
$row = mysqli_fetch_assoc($res);
$judul = $row ? $row['setting_value'] : 'Judul Data';

// Ambil data pegawai
$result = mysqli_query($connection, "SELECT * FROM pegawai ORDER BY username ASC");
?>

<!-- IziToast -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">
<script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>

<!-- SweetAlert2 untuk hapus -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1><?= htmlspecialchars($judul) ?></h1>
    <a href="./create.php" class="btn btn-primary">Tambah Data</a>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped w-100" id="table-1">
              <thead>
                <tr>
                  <th>Nama Karyawan</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th style="width: 150px">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($data = mysqli_fetch_array($result)) : ?>
                  <tr>
                    <td><?= htmlspecialchars($data['username']) ?></td>
                    <td><?= htmlspecialchars($data['role']) ?></td>
                    <td>
                      <?php if ($data['status'] === 'Aktif') : ?>
                        <span class="badge badge-success">Aktif</span>
                      <?php elseif ($data['status'] === 'Tidak Aktif') : ?>
                        <span class="badge badge-danger">Tidak Aktif</span>
                      <?php elseif ($data['status'] === 'Pending') : ?>
                        <span class="badge badge-warning">Pending</span>
                      <?php else : ?>
                        <span class="badge badge-secondary"><?= htmlspecialchars($data['status']) ?></span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <!-- Tombol edit -->
                      <a class="btn btn-sm btn-info" href="edit.php?id_pegawai=<?= $data['id_pegawai'] ?>">
                        <i class="fas fa-edit fa-fw"></i>
                      </a>

                      <!-- Tombol hapus (kalau mau aktifkan) -->
                      <!--
                      <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $data['id_pegawai'] ?>)">
                        <i class="fas fa-trash fa-fw"></i>
                      </button>
                      -->
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
</section>

<!-- SweetAlert2 Delete Function -->
<script>
  function confirmDelete(id) {
    Swal.fire({
      title: 'Apakah Anda yakin?',
      text: "Data tidak bisa dikembalikan setelah dihapus!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = 'delete.php?id_pegawai=' + id;
      }
    });
  }
</script>

<?php require_once '../layout/_bottom.php'; ?>

<!-- Notifikasi IziToast -->
<?php if (isset($_SESSION['info'])): ?>
  <script>
    iziToast.<?= $_SESSION['info']['status'] === 'success' ? 'success' : 'error' ?>({
      title: '<?= $_SESSION['info']['status'] === 'success' ? 'Sukses' : 'Gagal' ?>',
      message: '<?= $_SESSION['info']['message'] ?>',
      position: 'topCenter',
      timeout: 5000
    });
  </script>
  <?php unset($_SESSION['info']); ?>
<?php endif; ?>

<!-- DataTables JS khusus jika kamu pakai datatables -->
<script src="../assets/js/page/modules-datatables.js"></script>
