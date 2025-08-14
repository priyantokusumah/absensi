<?php
require_once '../helper/connection.php';
require_once '../helper/log.php';
session_start();

$username = $_POST['username'] ?? '';
$role     = $_POST['role'] ?? '';
$status   = $_POST['status'] ?? '';
$password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);

if ($username && $role && $status) {
    mysqli_begin_transaction($connection);
    try {
        $query = "INSERT INTO pegawai (username, role, password, status)
                  VALUES ('$username', '$role', '$password', '$status')";
        mysqli_query($connection, $query);
        mysqli_commit($connection);

        // ✅ Tambahkan Log Aktivitas Di Sini
        logActivity("Menambahkan pegawai baru: $username", "Pegawai");

        $_SESSION['info'] = [
            'status' => 'success',
            'message' => 'Data berhasil ditambahkan.'
        ];
    } catch (Exception $e) {
        mysqli_rollback($connection);
        $_SESSION['info'] = [
            'status' => 'error',
            'message' => 'Gagal menambahkan data: ' . mysqli_error($connection)
        ];
    }
} else {
    $_SESSION['info'] = [
        'status' => 'error',
        'message' => 'Data tidak lengkap.'
    ];
}

header('Location: index.php');
exit;
