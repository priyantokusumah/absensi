<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>Tambah Data Pegawai</h1>
    <a href="./index.php" class="btn btn-light">Kembali</a>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <!-- // Form -->
          <form action="./store.php" method="POST">
            <table cellpadding="8" class="w-100">

              <tr>
                <td>Nama Pegawai</td>
                <td><input class="form-control" type="text" name="username" size="20" required></td>
              </tr>

              <tr>
                <td>Role</td>
                <td>
                  <select class="form-control" name="role" id="role" required>
                    <option value="">--Pilih Role--</option>
                    <option value="Manajemen">Manajemen</option>
                    <option value="Karyawan">Karyawan</option>
                  </select>
                </td>
              </tr>
          
              <tr>
                <td>Password</td>
                <td><input class="form-control" type="password" name="password" size="20" required></td>
              </tr>

              <tr>
              <td>Status</td>
              <td>
                <select class="form-control" name="status" id="status" required>
                  <option value="Pending" selected>Pending</option>
                  <option value="Aktif">Aktif</option>
                  <option value="Tidak Aktif">Tidak Aktif</option>
                </select>
              </td>
            </tr>

              </tr>

              <tr>
                <td>
                  <input class="btn btn-primary" type="submit" name="proses" value="Simpan">
                  <input class="btn btn-danger" type="reset" name="batal" value="Bersihkan"></td>
              </tr>

            </table>
          </form>
        </div>
      </div>
    </div>
</section>

<?php
require_once '../layout/_bottom.php';
?>