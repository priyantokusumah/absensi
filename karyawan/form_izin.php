<?php 
require_once '../layout/_topkaryawan.php';
require_once '../helper/connection.php';
require_once '../helper/log.php';

$id_pegawai = $_SESSION['id_pegawai'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $alasan = $_POST['alasan'];
    $file_izin = null;

    $upload_dir = '../uploads/izin/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    if (!is_writable($upload_dir)) die("<script>Swal.fire('Error', 'Folder tidak bisa ditulis!', 'error');</script>");

    if (!empty($_FILES['file_izin']['name'])) {
        $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        $file_extension = strtolower(pathinfo($_FILES['file_izin']['name'], PATHINFO_EXTENSION));

        if (!in_array($file_extension, $allowed_extensions)) {
            echo "<script>Swal.fire('Error', 'Format file tidak didukung!', 'error');</script>";
        } else {
            $file_name = time() . '_' . basename($_FILES['file_izin']['name']);
            $file_path = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['file_izin']['tmp_name'], $file_path)) {
                $file_izin = $file_name;
            } else {
                die("<script>Swal.fire('Error', 'Gagal mengunggah file!', 'error');</script>");
            }
        }
    }

    $query = "INSERT INTO izin (id_pegawai, tanggal_mulai, tanggal_selesai, alasan, file_izin) VALUES (?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($query);
    $file_izin = $file_izin ?? '';
    $stmt->bind_param("issss", $id_pegawai, $tanggal_mulai, $tanggal_selesai, $alasan, $file_izin);
  // ✅ LOG AKTIVITAS
        logActivity("Melakukan Pengajuan izin", "Izin");
    if ($stmt->execute()) {
        echo "<script>
            Swal.fire('Sukses', 'Izin berhasil diajukan', 'success').then(() => {
                window.location.href = 'izin.php';
            });
        </script>";
    } else {
        echo "<script>Swal.fire('Error', 'Gagal mengajukan izin!', 'error');</script>";
    }
    $stmt->close();
}
?>

<section class="section">
    <div class="section-header">
        <h1>Form Izin Pegawai</h1>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="tanggal_mulai">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="tanggal_mulai" required>
                        </div>
                        <div class="form-group">
                            <label for="tanggal_selesai">Tanggal Selesai</label>
                            <input type="date" class="form-control" name="tanggal_selesai" required>
                        </div>
                        <div class="form-group">
                            <label for="alasan">Alasan Izin</label>
                            <textarea class="form-control" name="alasan" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="file_izin" class="font-weight-bold">
                                Upload Bukti Izin <small class="text-muted">(Format: PDF, JPG, PNG, DOCX)</small>
                            </label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file_izin" name="file_izin">
                                <label class="custom-file-label" for="file_izin">Pilih file...</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Ajukan Izin</button>
                        <button type="reset" class="btn btn-danger">Batal</button>
                        <button type="button" class="btn btn-light float-right" onclick="window.location.href = 'izin.php'">Kembali</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.querySelector("#file_izin").addEventListener("change", function(event) {
        let file = event.target.files[0];
        if (file) {
            let allowedExtensions = ["pdf", "jpg", "jpeg", "png", "doc", "docx"];
            let fileExtension = file.name.split('.').pop().toLowerCase();
            if (!allowedExtensions.includes(fileExtension)) {
                alert("Format file tidak didukung!");
                event.target.value = "";
            } else {
                this.nextElementSibling.innerText = file.name;
            }
        }
    });
</script>

<?php require_once '../layout/_bottom.php'; ?>
