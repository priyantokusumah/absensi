<?php
session_start();
require_once '../helper/connection.php';
require_once '../helper/log.php';

$id_pegawai = mysqli_real_escape_string($connection, $_POST['id_pegawai']);
$username = mysqli_real_escape_string($connection, $_POST['username']);
$role = mysqli_real_escape_string($connection, $_POST['role']);
$status = mysqli_real_escape_string($connection, $_POST['status']);
$password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

// Ambil data lama
$old = mysqli_query($connection, "SELECT * FROM pegawai WHERE id_pegawai = '$id_pegawai'");
$oldData = mysqli_fetch_assoc($old);

$changes = [];

// Bandingkan setiap field
if ($username !== $oldData['username']) {
    $changes[] = "Username dari '{$oldData['username']}' ke '$username'";
}
if ($role !== $oldData['role']) {
    $changes[] = "Role dari '{$oldData['role']}' menjadi '$role'";
}
if ($status !== $oldData['status']) {
    $changes[] = "Status dari '<b>{$oldData['status']}</b>' menjadi '<b>$status</b>'";
}
if ($password) {
    $changes[] = "Password diubah";
}

// Gabungkan log deskripsi
$logText = !empty($changes)
    ? "Mengubah data pegawai username : " . implode(', ', $changes)
    : "Menyimpan data pegawai tanpa perubahan";

mysqli_begin_transaction($connection);

try {
    // Simpan log sebelum query
    logActivity($logText, "Pegawai");

    if ($password) {
        $query = "UPDATE pegawai SET username = '$username', role = '$role', password = '$password', status = '$status' WHERE id_pegawai = '$id_pegawai'";
    } else {
        $query = "UPDATE pegawai SET username = '$username', role = '$role', status = '$status' WHERE id_pegawai = '$id_pegawai'";
    }

    mysqli_query($connection, $query);
    mysqli_commit($connection);

    $_SESSION['info'] = [
        'status' => 'success',
        'message' => 'Berhasil mengubah data'
    ];
    header('Location: ./index.php');
} catch (Exception $e) {
    mysqli_rollback($connection);

    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => mysqli_error($connection)
    ];
    header('Location: ./index.php');
}
