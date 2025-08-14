<?php
session_start();
if (isset($_SESSION['login'])) {
    $role = $_SESSION['role'];
    if ($role == 'Manajemen') {
        header('Location: admin/index.php'); // Mengarahkan ke dashboard admin
    } else if ($role == 'Karyawan') {
        header('Location: karyawan/index.php'); // Mengarahkan ke dashboard karyawan
    }
    exit();
} else {
    header('Location: login.php');
    exit();
}
?>