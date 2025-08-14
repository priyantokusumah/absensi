<?php
require_once 'connection.php';

function logActivity($aksi, $menu) {
    if (session_status() === PHP_SESSION_NONE) session_start();

    $id_pegawai = $_SESSION['id_pegawai'] ?? null;
    if (!$id_pegawai) return;

    global $connection;

    $stmt = $connection->prepare("SELECT username, role FROM pegawai WHERE id_pegawai = ?");
    $stmt->bind_param("i", $id_pegawai);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $username = $user['username'] ?? 'unknown';
    $role = $user['role'] ?? 'unknown';

    // Simpan log ke DB (dengan tanggal & waktu terpisah)
    $stmtLog = $connection->prepare("INSERT INTO log_activity (username, role, menu, aksi) VALUES (?, ?, ?, ?)");
    $stmtLog->bind_param("ssss", $username, $role, $menu, $aksi);
    $stmtLog->execute();
}

