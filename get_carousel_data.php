<?php
// Panggil file koneksi. Penting: koneksi.php tidak boleh ditutup di file aslinya.
require_once 'koneksi.php';

// Siapkan array untuk menampung hasil
$data = [];

// Query untuk mengambil data carousel, termasuk kolom 'judul' yang baru
// Diurutkan berdasarkan tanggal, lalu ID untuk konsistensi
$sql = "SELECT id, judul, tanggal, deskripsi, jenis, path FROM carousel ORDER BY tanggal ASC, id ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Format tanggal agar lebih mudah dibaca (opsional)
        if ($row['tanggal']) {
            $date = new DateTime($row['tanggal']);
            $row['tanggal_formatted'] = $date->format('d F Y'); // Contoh: 18 September 2025
        }
        $data[] = $row;
    }
}

$conn->close();

// Atur header agar browser tahu bahwa ini adalah file JSON
header('Content-Type: application/json');

// Ubah array PHP menjadi format JSON dan tampilkan
echo json_encode($data);
?>