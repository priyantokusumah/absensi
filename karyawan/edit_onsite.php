<?php
require_once '../layout/_topkaryawan.php';
require_once '../helper/connection.php';

$id_onsite = $_GET['id'] ?? 0;

// Ambil data onsite
$query = "SELECT * FROM onsite WHERE id_onsite = ? AND id_pegawai = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("ii", $id_onsite, $_SESSION['id_pegawai']);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "<h3>Data tidak ditemukan</h3>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $alasan = $_POST['alasan'];

    $update = "UPDATE onsite SET tanggal_mulai = ?, tanggal_selesai = ?, alasan = ? 
               WHERE id_onsite = ? AND id_pegawai = ?";
    $stmt = $connection->prepare($update);
    $stmt->bind_param("sssii", $tanggal_mulai, $tanggal_selesai, $alasan, $id_onsite, $_SESSION['id_pegawai']);
    if ($stmt->execute()) {
        echo "<script>
            Swal.fire('Sukses', 'Data onsite berhasil diperbarui', 'success').then(() => {
                window.location.href = '../karyawan/onsite.php';
            });
        </script>";
    } else {
        echo "<script>Swal.fire('Error', 'Gagal update data', 'error');</script>";
    }
}
?>

<section class="section">
    <div class="section-header">
        <h1>Edit Onsite</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label>Tanggal Mulai Onsite</label>
                    <input type="date" name="tanggal_mulai" value="<?= $data['tanggal_mulai'] ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Selesai Onsite</label>
                    <input type="date" name="tanggal_selesai" value="<?= $data['tanggal_selesai'] ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Alasan Onsite</label>
                    <textarea name="alasan" class="form-control" required><?= $data['alasan'] ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="../karyawan/onsite.php" class="btn btn-light">Batal</a>
            </form>
        </div>
    </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>
