<?php
session_start();
require_once '../helper/connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Manajemen') {
    header('Location: ../login.php');
    exit();
}

// Simpan perubahan
if (isset($_POST['save'])) {
    $judul_dashboard = mysqli_real_escape_string($connection, $_POST['judul_dashboard']);
    $judul_data = mysqli_real_escape_string($connection, $_POST['judul_data']);
    $judul_izin = mysqli_real_escape_string($connection, $_POST['judul_izin']);
    $judul_cuti = mysqli_real_escape_string($connection, $_POST['judul_cuti']);
    $judul_report = mysqli_real_escape_string($connection, $_POST['judul_report']);
    $judul_dashboard_karyawan = mysqli_real_escape_string($connection, $_POST['judul_dashboard_karyawan']);
    $judul_izin_karyawan = mysqli_real_escape_string($connection, $_POST['judul_izin_karyawan']);
    $judul_cuti_karyawan = mysqli_real_escape_string($connection, $_POST['judul_cuti_karyawan']);

    $sqls = [
        "UPDATE settings SET setting_value = '$judul_dashboard' WHERE setting_key = 'judul_dashboard'",
        "UPDATE settings SET setting_value = '$judul_data' WHERE setting_key = 'judul_data'",
        "UPDATE settings SET setting_value = '$judul_izin' WHERE setting_key = 'judul_izin'",
        "UPDATE settings SET setting_value = '$judul_cuti' WHERE setting_key = 'judul_cuti'",
        "UPDATE settings SET setting_value = '$judul_report' WHERE setting_key = 'judul_report'",
        "UPDATE settings SET setting_value = '$judul_dashboard_karyawan' WHERE setting_key = 'judul_dashboard_karyawan'",
        "UPDATE settings SET setting_value = '$judul_izin_karyawan' WHERE setting_key = 'judul_izin_karyawan'",
        "UPDATE settings SET setting_value = '$judul_cuti_karyawan' WHERE setting_key = 'judul_cuti_karyawan'",
    ];

    foreach ($sqls as $sql) {
        mysqli_query($connection, $sql);
    }

    $_SESSION['success'] = 'Header berhasil diubah!';
    header('Location: header.php');
    exit();
}

// Ambil data dari DB
$result = mysqli_query($connection, "SELECT setting_key, setting_value FROM settings WHERE setting_key IN (
    'judul_dashboard', 'judul_data', 'judul_izin', 'judul_cuti', 'judul_report', 'judul_dashboard_karyawan', 'judul_izin_karyawan', 'judul_cuti_karyawan')");

$judul_dashboard = $judul_data = $judul_izin = $judul_cuti = $judul_report = $judul_dashboard_karyawan = $judul_izin_karyawan = $judul_cuti_karyawan = '';

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        ${$row['setting_key']} = $row['setting_value'];
    }
}

require_once '../layout/_top.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pengaturan Header</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/datatables.css">
    <style>
        .form-wrapper {
            max-width: 800px;
            margin: 30px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .form-wrapper h5 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 25px;
            color: #4e73df;
        }
        .form-group label {
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
        }
        .form-control {
            border: 1px solid #ccc;
            padding: 10px 12px;
            border-radius: 6px;
            width: 100%;
            font-size: 1rem;
        }
        .btn-primary {
            background-color: #4e73df;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-primary:hover {
            background-color: #2e59d9;
        }

        /* Toast Style */
        #toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #28a745;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            display: none;
        }
    </style>
</head>
<body>

<?php if (isset($_SESSION['success'])): ?>
    <div id="toast"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<section class="section">
    <div class="section-header">
        <h1>Pengaturan Header</h1>
    </div>
    <div class="section-body">
        <div class="form-wrapper">
            <h5>Form Pengaturan Judul Halaman</h5>

            <form method="POST">
                <div class="form-group">
                    <label for="judul_dashboard">Header Dashboard Manajemen</label>
                    <input type="text" class="form-control" id="judul_dashboard" name="judul_dashboard" value="<?= htmlspecialchars($judul_dashboard) ?>" required>
                </div>
                <div class="form-group">
                    <label for="judul_data">Header Data Manajemen</label>
                    <input type="text" class="form-control" id="judul_data" name="judul_data" value="<?= htmlspecialchars($judul_data) ?>" required>
                </div>
                <div class="form-group">
                    <label for="judul_izin">Header Izin Manajemen</label>
                    <input type="text" class="form-control" id="judul_izin" name="judul_izin" value="<?= htmlspecialchars($judul_izin) ?>" required>
                </div>
                <div class="form-group">
                    <label for="judul_cuti">Header Cuti Manajemen</label>
                    <input type="text" class="form-control" id="judul_cuti" name="judul_cuti" value="<?= htmlspecialchars($judul_cuti) ?>" required>
                </div>
                <div class="form-group">
                    <label for="judul_report">Header Report Manajemen</label>
                    <input type="text" class="form-control" id="judul_report" name="judul_report" value="<?= htmlspecialchars($judul_report) ?>" required>
                </div>
                <div class="form-group">
                    <label for="judul_dashboard_karyawan">Header Dashboard Karyawan</label>
                    <input type="text" class="form-control" id="judul_dashboard_karyawan" name="judul_dashboard_karyawan" value="<?= htmlspecialchars($judul_dashboard_karyawan) ?>" required>
                </div>
                <div class="form-group">
                    <label for="judul_izin_karyawan">Header Izin Karyawan</label>
                    <input type="text" class="form-control" id="judul_izin_karyawan" name="judul_izin_karyawan" value="<?= htmlspecialchars($judul_izin_karyawan) ?>" required>
                </div>
                <div class="form-group">
                    <label for="judul_cuti_karyawan">Header Cuti Karyawan</label>
                    <input type="text" class="form-control" id="judul_cuti_karyawan" name="judul_cuti_karyawan" value="<?= htmlspecialchars($judul_cuti_karyawan) ?>" required>
                </div>
                <div class="form-group text-end mt-4">
                    <button type="submit" class="btn btn-primary" name="save">💾 Simpan</button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Toast JavaScript -->
<script>
    const toast = document.getElementById('toast');
    if (toast) {
        toast.style.display = 'block';
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.5s ease';
            setTimeout(() => toast.remove(), 500);
        }, 1000);
    }
</script>

</body>
</html>

<?php require_once '../layout/_bottom.php'; ?>
