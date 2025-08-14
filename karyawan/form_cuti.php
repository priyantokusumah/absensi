<?php
require_once '../layout/_topkaryawan.php';
require_once '../helper/connection.php';
require_once '../helper/log.php';

$id_pegawai = $_SESSION['id_pegawai'];
$jatah_cuti = 12;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $alasan = $_POST['alasan'];

    // Hitung hari cuti yang diajukan
    $start = new DateTime($tanggal_mulai);
    $end = new DateTime($tanggal_selesai);
    $diff = $start->diff($end);
    $hari_diajukan = $diff->days + 1;

    // Hitung total cuti yang sudah disetujui (status sama dengan yang di dashboard)
    $query = "SELECT SUM(DATEDIFF(tanggal_selesai, tanggal_mulai) + 1) AS total_cuti FROM cuti WHERE id_pegawai = ? AND status = 'Disetujui'";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $id_pegawai);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $total_cuti_diambil = $row['total_cuti'] ?? 0;

    $sisa_cuti = max($jatah_cuti - $total_cuti_diambil, 0);

    // Validasi sisa cuti
    if ($hari_diajukan > $sisa_cuti) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Pengajuan Gagal',
                text: 'Jatah cuti Anda tidak cukup untuk pengajuan ini. Sisa cuti: {$sisa_cuti} hari.',
                confirmButtonText: 'OK'
            });
        </script>";
    } else {
        // Proses simpan cuti dengan status Pending
        $query = "INSERT INTO cuti (id_pegawai, tanggal_mulai, tanggal_selesai, alasan, status) VALUES (?, ?, ?, ?, 'Pending')";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("isss", $id_pegawai, $tanggal_mulai, $tanggal_selesai, $alasan);
// ✅ LOG AKTIVITAS
        logActivity("Melakukan Pengajuan Cuti", "Cuti");
        if ($stmt->execute()) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Pengajuan Cuti Berhasil!',
                    text: 'Harap tunggu persetujuan dari manajemen.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'cuti.php';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    text: 'Gagal mengajukan cuti. Coba lagi.',
                    confirmButtonText: 'OK'
                });
            </script>";
        }
    }
}
?>



<section class="section">
    <div class="section-header">
        <h1>From Pengajuan Cuti Pegawai</h1>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label for="tanggal_mulai">Tanggal Mulai Cuti</label>
                            <input type="date" class="form-control" name="tanggal_mulai" required>
                        </div>
                        <div class="form-group">
                            <label for="tanggal_selesai">Tanggal Selesai Cuti</label>
                            <input type="date" class="form-control" name="tanggal_selesai" required>
                        </div>
                        <div class="form-group">
                            <label for="alasan">Alasan Cuti</label>
                            <textarea class="form-control" name="alasan" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Ajukan Cuti</button>
                        <button type="reset" class="btn btn-danger">Batal</button>
                        <button style="float: right" type="button" class="btn btn-light" onclick="window.location.href = 'cuti.php'">Kembali</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>
