<?php
session_start();
require_once '../helper/connection.php';
require_once '../helper/log.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_izin']) && isset($_POST['status'])) {
        $id_izin = $_POST['id_izin'];
        $status = $_POST['status'];

        // Ambil status lama + username
        $query_old = "SELECT i.status AS status_lama, p.username
                      FROM izin i
                      JOIN pegawai p ON i.id_pegawai = p.id_pegawai
                      WHERE i.id_izin = ?";
        $stmt_old = $connection->prepare($query_old);
        $stmt_old->bind_param("i", $id_izin);
        $stmt_old->execute();
        $result_old = $stmt_old->get_result();
        $data_old = $result_old->fetch_assoc();

        $status_lama = $data_old['status_lama'] ?? '-';
        $username    = $data_old['username'] ?? 'Unknown';

        // Update status izin
        $query = "UPDATE izin SET status = ? WHERE id_izin = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("si", $status, $id_izin);

        if ($stmt->execute()) {
            // ✅ Logging
            logActivity("Mengubah status izin user <b>$username</b> dari <b>$status_lama</b> ke <b>$status</b>", "Izin");

            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Gagal mengupdate status"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Metode tidak diizinkan"]);
}
?>