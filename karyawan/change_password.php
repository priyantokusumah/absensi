<?php
session_start();
require_once '../helper/connection.php'; // Menggunakan koneksi yang benar

if (!isset($_SESSION['login'])) {
    echo 'Session berakhir, silakan login lagi!';
    exit();
}

$username = $_SESSION['username'];
$new_password = $_POST['new_password'];

// Hash password baru sebelum menyimpannya ke database
$new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update password di tabel pegawai
$query = $connection->prepare("UPDATE pegawai SET password = ? WHERE username = ?");
$query->bind_param("ss", $new_hashed_password, $username);

if ($query->execute()) {
    echo 'success';
} else {
    echo 'Terjadi kesalahan, coba lagi!';
}

$query->close();
$connection->close();
?>
