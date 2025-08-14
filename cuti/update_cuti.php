<?php
require_once '../helper/connection.php';
require_once '../helper/log.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_cuti']) && isset($_POST['status'])) {
        $id_cuti = $_POST['id_cuti'];
        $status  = $_POST['status'];

        // Ambil data lama: status & username
        $query_old = "SELECT c.status AS status_lama, p.username 
                      FROM cuti c
                      JOIN pegawai p ON c.id_pegawai = p.id_pegawai
                      WHERE c.id_cuti = ?";
        $stmt_old = $connection->prepare($query_old);
        $stmt_old->bind_param("i", $id_cuti);
        $stmt_old->execute();
        $result_old = $stmt_old->get_result();
        $data_old = $result_old->fetch_assoc();

        $status_lama = $data_old['status_lama'] ?? '-';
        $username    = $data_old['username'] ?? 'Unknown';

        // Update status cuti
        $query = "UPDATE cuti SET status = ? WHERE id_cuti = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("si", $status, $id_cuti);

        if ($stmt->execute()) {
            // ✅ Log aktivitas
            logActivity("Mengubah status cuti user <b>$username</b> dari <b>$status_lama</b> ke <b>$status</b>", "Cuti");
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
