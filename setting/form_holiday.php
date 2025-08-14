<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$conn = $connection ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama_libur'];
    $libur = $_POST['tanggal_libur'];
    $jenis = $_POST['jenis_libur'];

    $query = "INSERT INTO holiday (nama_libur, tanggal_libur, jenis_libur)
              VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $nama, $libur, $jenis);

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Libur Berhasil Ditambahkan!',
                text: 'Hari libur berhasil disimpan.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'holiday_management.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Data libur gagal disimpan.',
                confirmButtonText: 'OK'
            });
        </script>";
    }
}
?>

<section class="section">
    <div class="section-header">
        <h1>Form Tambah Hari Libur</h1>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label for="nama_libur">Nama Libur</label>
                            <input type="text" class="form-control" name="nama_libur" required>
                        </div>
                        <div class="form-group">
                            <label for="tanggal_libur">Tanggal Libur</label>
                            <input type="date" class="form-control" name="tanggal_libur" required>
                        </div>
                        <div class="form-group">
                            <label for="jenis_libur">Jenis Libur</label>
                            <select class="form-control" name="jenis_libur" required>
                                <option value="Nasional">Nasional</option>
                                <option value="Cuti Bersama">Cuti Bersama</option>
                                <option value="Custom">Custom</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="reset" class="btn btn-danger">Reset</button>
                        <button type="button" class="btn btn-light float-right" onclick="window.location.href = 'holiday_management.php'">Kembali</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>
