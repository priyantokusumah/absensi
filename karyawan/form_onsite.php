<?php
require_once '../layout/_topkaryawan.php';
require_once '../helper/connection.php';

$id_pegawai = $_SESSION['id_pegawai'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $alasan = $_POST['alasan'];

    // Simpan langsung ke tabel onsite
    $query = "INSERT INTO onsite (id_pegawai, tanggal_mulai, tanggal_selesai, alasan) VALUES (?, ?, ?, ?)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("isss", $id_pegawai, $tanggal_mulai, $tanggal_selesai, $alasan);

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Pengajuan Onsite Berhasil!',
                text: 'Data onsite Anda berhasil disimpan.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'onsite.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan!',
                text: 'Gagal menyimpan data onsite. Coba lagi.',
                confirmButtonText: 'OK'
            });
        </script>";
    }
}
?>

<section class="section">
    <div class="section-header">
        <h1>Form Pengajuan Onsite Pegawai</h1>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label for="tanggal_mulai">Tanggal Mulai Onsite</label>
                            <input type="date" class="form-control" name="tanggal_mulai" required>
                        </div>
                        <div class="form-group">
                            <label for="tanggal_selesai">Tanggal Selesai Onsite</label>
                            <input type="date" class="form-control" name="tanggal_selesai" required>
                        </div>
                        <div class="form-group">
                            <label for="alasan">Alasan Onsite</label>
                            <textarea class="form-control" name="alasan" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Ajukan Onsite</button>
                        <button type="reset" class="btn btn-danger">Batal</button>
                        <button style="float: right" type="button" class="btn btn-light" onclick="window.location.href = 'onsite.php'">Kembali</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>
