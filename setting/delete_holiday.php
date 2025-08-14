<?php
session_start();
require_once '../helper/connection.php';

$conn = $connection ?? null;

if ($_SESSION['role'] !== 'Manajemen') {
    header('Location: ../login.php');
    exit();
}

$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => 'ID tidak valid.'
    ];
    header('Location: holiday-management.php');
    exit();
}

try {
    $stmt = $conn->prepare("DELETE FROM holiday WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $_SESSION['info'] = [
        'status' => 'success',
        'message' => 'Data berhasil dihapus.'
    ];
} catch (Exception $e) {
    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => 'Gagal hapus: ' . $e->getMessage()
    ];
}

header('Location: holiday_management.php');
exit();
