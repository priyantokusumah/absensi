<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SESSION['role'] !== 'Manajemen') {
    header('Location: ../login.php');
    exit();
}

$success = false;
$error = '';

$logo_path = '../img/logo.png';
$logo_version = file_exists($logo_path) ? filemtime($logo_path) : time();
$logo_url = $logo_path . '?v=' . $logo_version;

// Proses upload
if (isset($_POST['upload_logo']) && isset($_FILES['logo'])) {
    $file = $_FILES['logo'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array(strtolower($ext), $allowed)) {
            move_uploaded_file($file['tmp_name'], $logo_path);
            // Update versi file logo untuk preview real-time
            $logo_version = time();
            $logo_url = $logo_path . '?v=' . $logo_version;
            $success = true;
        } else {
            $error = "Format file tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP.";
        }
    } else {
        $error = "Gagal mengupload gambar.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pengaturan Logo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<section class="section">
    <div class="section-header">
        <h1>Pengaturan Logo</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success">Logo berhasil diubah!</div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="logo">Upload Logo Baru</label>
                        <input type="file" class="form-control" name="logo" required>
                    </div>
                    <button type="submit" name="upload_logo" class="btn btn-primary mt-3">Simpan</button>
                </form>

                <hr>
                <h5>Preview Logo Saat Ini:</h5>
                <img src="<?= $logo_url ?>" alt="Logo Sekarang" style="max-width: 200px; height: auto;" id="previewLogo">
            </div>
        </div>
    </div>
</section>
</body>
</html>
<?php require_once '../layout/_bottom.php'; ?>
