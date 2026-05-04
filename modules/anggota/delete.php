<?php
require_once '../../config/database.php';

// Validasi ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?error=ID anggota tidak valid");
    exit();
}

$id_anggota = (int)$_GET['id'];

// Ambil data anggota 
$stmt = $conn->prepare("SELECT nama, foto FROM anggota WHERE id_anggota = ?");
$stmt->bind_param("i", $id_anggota);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $stmt->close();
    closeConnection();
    header("Location: index.php?error=Data anggota tidak ditemukan");
    exit();
}

$data = $result->fetch_assoc();
$nama = $data['nama'];
$foto = $data['foto'];
$stmt->close();

// Delete Data Anggota
$stmt = $conn->prepare("DELETE FROM anggota WHERE id_anggota = ?");
$stmt->bind_param("i", $id_anggota);

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {

        $stmt->close();

        // Hapus foto SETELAH DB berhasil dihapus
        if (!empty($foto)) {
            $file_path = "uploads/" . $foto;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        closeConnection();
        header("Location: index.php?success=" . urlencode("Data anggota '$nama' berhasil dihapus"));
        exit();
    } else {
        $stmt->close();
        closeConnection();
        header("Location: index.php?error=Gagal menghapus data");
        exit();
    }
} else {
    $error = $stmt->error;
    $stmt->close();
    closeConnection();
    header("Location: index.php?error=" . urlencode("Error database: $error"));
    exit();
}
