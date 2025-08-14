<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$query = "SELECT izin.id_izin, pegawai.username, izin.alasan
          FROM izin 
          JOIN pegawai ON izin.id_pegawai = pegawai.id_pegawai 
          ORDER BY izin.id_izin DESC";
$result = mysqli_query($connection, $query);

$res = mysqli_query($connection, "SELECT setting_value FROM settings WHERE setting_key = 'judul_izin'");
$row = mysqli_fetch_assoc($res);
$judul = $row ? $row['setting_value'] : 'Daftar Izin';
?>

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
                                <tr>
                                    <th>Nama Karyawan</th>
                                    <th>Alasan Izin</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['username']) ?></td>
                                        <td><?= htmlspecialchars($row['alasan']) ?></td>
                                        <td>
                                            <a href="detail_izin.php?id=<?= $row['id_izin'] ?>" class="btn btn-sm btn-info">Detail</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php if (mysqli_num_rows($result) === 0) { ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Belum ada permintaan izin</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>
<script src="../assets/js/page/modules-datatables.js"></script>
