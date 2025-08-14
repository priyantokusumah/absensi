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

$id_pegawai = $_SESSION['id_pegawai'];

// Ambil daftar Onsite karyawan yang sedang login
$query = "SELECT id_onsite, tanggal_mulai, tanggal_selesai, alasan FROM onsite WHERE id_pegawai = ? ORDER BY tanggal_mulai DESC";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $id_pegawai);
$stmt->execute();
$result = $stmt->get_result();
?>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>Riwayat Onsite Karyawan</h1>
        <a href="./form_onsite.php" class="btn btn-primary">Tambah Onsite</a>    
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped w-100" id="table-1">
                            <thead>
                                <tr>
                                    <th>Tanggal Mulai Onsite</th>
                                    <th>Tanggal Selesai Onsite</th>
                                    <th>Alasan Onsite</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo formatTanggal($row['tanggal_mulai']); ?></td>
                                        <td><?php echo formatTanggal($row['tanggal_selesai']); ?></td>
                                        <td><?php echo $row['alasan']; ?></td>
                                         <td>
                                            <a href="../karyawan/edit_onsite.php?id=<?php echo $row['id_onsite']; ?>" 
                                            class="btn btn-warning btn-sm">Edit</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php if ($result->num_rows === 0) { ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada Onsite</td>
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
