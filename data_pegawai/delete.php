<?php
session_start();
require_once '../helper/connection.php';

$id_pegawai = $_GET['id_pegawai'];

// Mulai transaksi
mysqli_begin_transaction($connection);

try {

    // Hapus data dari tabel absensi
     $query_absensi = "DELETE FROM absensi WHERE id_pegawai='$id_pegawai'";
    mysqli_query($connection, $query_absensi);
    
    // Hapus data dari tabel login
    $query_user = "DELETE FROM user WHERE id_pegawai='$id_pegawai'";
    mysqli_query($connection, $query_user);

    // Hapus data dari tabel pegawai
    $query_pegawai = "DELETE FROM pegawai WHERE id_pegawai='$id_pegawai'";
    mysqli_query($connection, $query_pegawai);

    // Commit transaksi
    mysqli_commit($connection);

    $_SESSION['info'] = [
        'status' => 'success',
        'message' => 'Berhasil menghapus data'
    ];
    header('Location: ./index.php');
} catch (Exception $e) {
    // Rollback transaksi jika terjadi kesalahan
    mysqli_rollback($connection);

    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => mysqli_error($connection)
    ];
    header('Location: ./index.php');
}
?>