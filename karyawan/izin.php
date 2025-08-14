<?php
require_once '../layout/_topkaryawan.php';
require_once '../helper/connection.php';

// Fungsi format tanggal ke dalam format Indonesia: dd-NamaBulan-YYYY
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

$id_pegawai = $_SESSION['id_pegawai'];

// Ambil data izin pegawai yang sedang login
$query = "SELECT tanggal_mulai, tanggal_selesai, alasan, status, file_izin 
          FROM izin 
          WHERE id_pegawai = ? 
          ORDER BY tanggal_mulai DESC";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $id_pegawai);
$stmt->execute();
$result = $stmt->get_result();
$res = mysqli_query($connection, "SELECT setting_value FROM settings WHERE setting_key = 'judul_izin_karyawan'");
$row = mysqli_fetch_assoc($res);
$judul = $row['setting_value'] ?? 'Judul Izin Karyawan';
?>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1><?= htmlspecialchars($judul) ?></h1>
        <a href="./form_izin.php" class="btn btn-primary">Tambah Data</a>    
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped w-100" id="table-1">
                            <thead>
                                <tr>
                                    <th>Tanggal Mulai Izin</th>
                                    <th>Tanggal Selesai Izin</th>
                                    <th>Alasan</th>
                                    <th>Bukti Izin</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()) { ?>
                                    <tr>
                                        <td>
                                            <?= formatTanggal($row['tanggal_mulai']) ?> 
                                        </td>
                                        <td>
                                            <?= formatTanggal($row['tanggal_selesai']) ?>
                                        </td>
                                        <td><?= htmlspecialchars($row['alasan']) ?></td>
                                        <td>
                                            <?php if (!empty($row['file_izin'])) { ?>
                                                <a href="../uploads/izin/<?= htmlspecialchars($row['file_izin']) ?>" target="_blank" class="btn btn-sm btn-info">
                                                    Lihat Bukti
                                                </a>
                                            <?php } else { ?>
                                                <span class="text-muted">Tidak ada</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($row['status'] == 'Disetujui') {
                                                echo '<span class="badge badge-success">Disetujui</span>';
                                            } elseif ($row['status'] == 'Ditolak') {
                                                echo '<span class="badge badge-danger">Ditolak</span>';
                                            } else {
                                                echo '<span class="badge badge-warning">Menunggu</span>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php if ($result->num_rows === 0) { ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada izin</td>
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
