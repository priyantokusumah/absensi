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

// Ambil data izin dari database
$query = "SELECT cuti.id_cuti, pegawai.username, cuti.tanggal_mulai, cuti.tanggal_selesai, cuti.alasan, cuti.status 
          FROM cuti 
          JOIN pegawai ON cuti.id_pegawai = pegawai.id_pegawai 
          ORDER BY cuti.tanggal_mulai DESC";
$result = mysqli_query($connection, $query);
$res = mysqli_query($connection, "SELECT setting_value FROM settings WHERE setting_key = 'judul_cuti'");
$row = mysqli_fetch_assoc($res);
$judul = $row ? $row['setting_value'] : 'Judul Cuti';
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
                                    <th width="10%">Nama Karyawan</th>
                                    <th width="20%">Tanggal Mulai Cuti</th>
                                    <th width="20%">Tanggal Selesai Cuti</th>
                                    <th>Alasan Cuti</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td><?php echo formatTanggal($row['tanggal_mulai']); ?></td>
                                        <td><?php echo formatTanggal($row['tanggal_selesai']); ?></td>
                                        <td><?php echo htmlspecialchars($row['alasan']); ?></td>
                                        <td>
                                            <?php if ($row['status'] == 'Pending') { ?>
                                                <span class="badge badge-warning">Pending</span>
                                            <?php } elseif ($row['status'] == 'Disetujui') { ?>
                                                <span class="badge badge-success">Disetujui</span>
                                            <?php } else { ?>
                                                <span class="badge badge-danger">Ditolak</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                        <div style="white-space: nowrap;">
                                            <?php if ($row['status'] == 'Pending') { ?>
                                                <button class="btn btn-sm btn-success" onclick="updateStatus(<?= $row['id_cuti'] ?>, 'Disetujui')">
                                                    Approve 
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="updateStatus(<?= $row['id_cuti'] ?>, 'Ditolak')">
                                                    Tolak
                                                </button>
                                            <?php } else { ?>
                                                <span class="text-muted">Selesai</span>
                                            <?php } ?>
                                        </div>    
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php if (mysqli_num_rows($result) === 0) { ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada permintaan Cuti</td>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function updateStatus(id_cuti, status) {
        Swal.fire({
            title: 'Konfirmasi',
            text: "Apakah Anda yakin ingin mengubah status Cuti ini menjadi " + status + "?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Ubah',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('update_cuti.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `id_cuti=${id_cuti}&status=${status}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Status izin berhasil diperbarui!',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Terjadi kesalahan jaringan.', 'error');
                });
            }
        });
    }
</script>


<?php require_once '../layout/_bottom.php'; ?>
<script src="../assets/js/page/modules-datatables.js"></script>