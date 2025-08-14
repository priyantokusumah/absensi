<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$id_pegawai = $_GET['id_pegawai'];
$query = mysqli_query($connection, "SELECT * FROM pegawai WHERE id_pegawai='$id_pegawai'");
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>Ubah Data Pegawai</h1>
    <a href="./index.php" class="btn btn-light">Kembali</a>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <!-- // Form -->
          <form action="./update.php" method="post">
            <?php
            while ($row = mysqli_fetch_array($query)) {
            ?>
              <input type="hidden" name="id_pegawai" value="<?= $row['id_pegawai'] ?>">
              <table cellpadding="8" class="w-100">
                <tr>
                  <td>Nama Pegawai</td>
                  <td><input class="form-control" type="text" name="username" size="20" required value="<?= $row['username'] ?>"></td>
                </tr>
                <tr>
                  <td>Role</td>
                  <td>
                    <select class="form-control" name="role" id="role" required>
                      <option value="Manajemen" <?php if ($row['role'] == "Manajemen") {
                                              echo "selected";
                                            } ?>>Manajemen</option>
                      <option value="Karyawan" <?php if ($row['role'] == "Karyawan") {
                                                echo "selected";
                                              } ?>>Karyawan</option>
                    </select>
                  </td>
                </tr>

                <tr>
                  <td>Reset Password</td>
                  <td><input class="form-control" type="password" name="password" size="20"></td>
                </tr>

                <tr>
                  <td>Status</td>
                  <td>
                    <select class="form-control" name="status" id="active" required>
                    <option value="Pending" <?php if (isset($row['status']) && $row['status'] == "Pending") { echo "selected"; } ?>>Pending</option>
                    <option value="Aktif" <?php if (isset($row['status']) && $row['status'] == "Aktif") { echo "selected"; } ?>>Aktif</option>
                    <option value="Tidak Aktif" <?php if (isset($row['status']) && $row['status'] == "Tidak Aktif") { echo "selected"; } ?>>Tidak Aktif</option>
                    </select>
                  </td>
               </tr>
            
                <tr>
                  <td>
                    <input class="btn btn-primary d-inline" type="submit" name="proses" value="Ubah">
                    <a href="./index.php" class="btn btn-danger ml-1">Batal</a>
                  <td>
                </tr>
              </table>

            <?php } ?>
          </form>
        </div>
      </div>
    </div>
</section>

<?php
require_once '../layout/_bottom.php';
?>