<?php
require_once '../helper/connection.php';

$conn = $connection ?? null;

session_start();

// Cek role
if ($_SESSION['role'] !== 'Manajemen') {
    header('Location: ../login.php');
    exit();
}

// Cek ID
$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id) {
    $_SESSION['info'] = ['status' => 'failed', 'message' => 'ID tidak ditemukan.'];
    header('Location: holiday_management.php');
    exit();
}

// Handle POST (update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama_libur'];
    $tanggal = $_POST['tanggal_libur'];
    $jenis = $_POST['jenis_libur'];

    $stmt = $conn->prepare("UPDATE holiday SET nama_libur = ?, tanggal_libur = ?, jenis_libur = ? WHERE id = ?");
    $stmt->bind_param("sssi", $nama, $tanggal, $jenis, $id);

    if ($stmt->execute()) {
        $_SESSION['info'] = ['status' => 'success', 'message' => 'Data berhasil diupdate.'];
    } else {
        $_SESSION['info'] = ['status' => 'failed', 'message' => 'Gagal update data.'];
    }

    header('Location: holiday_management.php');
    exit();
}

// Ambil data untuk form
$stmt = $conn->prepare("SELECT * FROM holiday WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    $_SESSION['info'] = ['status' => 'failed', 'message' => 'Data tidak ditemukan.'];
    header('Location: holiday_management.php');
    exit();
}
?>

<?php require_once '../layout/_top.php'; ?>

<section class="section">
    <div class="section-header">
        <h1>Edit Hari Libur</h1>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $data['id']; ?>">

                        <div class="form-group">
                            <label for="nama_libur">Nama Libur</label>
                            <input type="text" class="form-control" name="nama_libur" required value="<?= htmlspecialchars($data['nama_libur']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="tanggal_libur">Tanggal Libur</label>
                            <input type="date" class="form-control" name="tanggal_libur" required value="<?= $data['tanggal_libur']; ?>">
                        </div>

                        <div class="form-group">
                            <label for="jenis_libur">Jenis Libur</label>
                            <select class="form-control" name="jenis_libur" required>
                                <option value="Nasional" <?= $data['jenis_libur'] === 'Nasional' ? 'selected' : '' ?>>Nasional</option>
                                <option value="Cuti Bersama" <?= $data['jenis_libur'] === 'Cuti Bersama' ? 'selected' : '' ?>>Cuti Bersama</option>
                                <option value="Custom" <?= $data['jenis_libur'] === 'Custom' ? 'selected' : '' ?>>Custom</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="holiday_management.php" class="btn btn-light float-right">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>
