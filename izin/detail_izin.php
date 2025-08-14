<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$id_izin = $_GET['id'] ?? 0;

// Ambil detail izin + pegawai
$query = "SELECT izin.*, pegawai.username FROM izin 
          JOIN pegawai ON izin.id_pegawai = pegawai.id_pegawai 
          WHERE id_izin = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $id_izin);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "<h3>Data tidak ditemukan.</h3>";
    exit;
}

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
?>

<section class="section">
    <div class="section-header">
        <h1>Detail Izin Karyawan</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <p><strong>Nama Karyawan:</strong> <?= htmlspecialchars($data['username']) ?></p>
            <p><strong>Tanggal Izin:</strong> <?= formatTanggal($data['tanggal_mulai']) ?> sampai <?= formatTanggal($data['tanggal_selesai']) ?></p>
            <p><strong>Alasan:</strong> <?= nl2br(htmlspecialchars($data['alasan'])) ?></p>
            <p><strong>Status:</strong>
                <?php if ($data['status'] == 'Pending') { ?>
                    <span class="badge badge-warning">Pending</span>
                <?php } elseif ($data['status'] == 'Disetujui') { ?>
                    <span class="badge badge-success">Disetujui</span>
                <?php } else { ?>
                    <span class="badge badge-danger">Ditolak</span>
                <?php } ?>
            </p>
            <p><strong>Bukti Izin:</strong><br>
                <?php if (!empty($data['file_izin'])) { ?>
                    <a href="../uploads/izin/<?= htmlspecialchars($data['file_izin']) ?>" target="_blank" class="btn btn-sm btn-primary">Lihat Bukti</a>
                <?php } else { ?>
                    <span class="text-muted">Tidak Ada</span>
                <?php } ?>
            </p>

            <?php if ($data['status'] == 'Pending') { ?>
                <button class="btn btn-success" onclick="updateStatus(<?= $id_izin ?>, 'Disetujui')">Approve</button>
                <button class="btn btn-danger" onclick="updateStatus(<?= $id_izin ?>, 'Ditolak')">Tolak</button>
            <?php } ?>
            <a href="./index.php" class="btn btn-primary float-right">Kembali</a>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function updateStatus(id_izin, status) {
        Swal.fire({
            title: 'Konfirmasi',
            text: "Yakin ingin mengubah status menjadi " + status + "?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('update_izin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `id_izin=${id_izin}&status=${status}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Berhasil!', 'Status diperbarui.', 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                });
            }
        });
    }
</script>

<?php require_once '../layout/_bottom.php'; ?>
