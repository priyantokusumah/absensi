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

// Ambil data onsite dari database
$query = "SELECT onsite.id_onsite, pegawai.username, onsite.tanggal_mulai, onsite.tanggal_selesai, onsite.alasan
          FROM onsite
          JOIN pegawai ON onsite.id_pegawai = pegawai.id_pegawai 
          ORDER BY onsite.tanggal_mulai DESC";
$result = mysqli_query($connection, $query);

$res = mysqli_query($connection, "SELECT setting_value FROM settings WHERE setting_key = 'judul_onsite'");
$row = mysqli_fetch_assoc($res);
$judul = $row ? $row['setting_value'] : 'Data Onsite Pegawai';
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
                                    <th>Tanggal Mulai Onsite</th>
                                    <th>Tanggal Selesai Onsite</th>
                                    <th>Alasan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td><?php echo formatTanggal($row['tanggal_mulai']); ?></td>
                                        <td><?php echo formatTanggal($row['tanggal_selesai']); ?></td>
                                        <td><?php echo htmlspecialchars($row['alasan']); ?></td>
                                    </tr>
                                <?php } ?>
                                <?php if (mysqli_num_rows($result) === 0) { ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada data Onsite</td>
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
