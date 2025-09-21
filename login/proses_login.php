<?php
// Selalu mulai session di awal
session_start();

// Panggil file koneksi
require_once '../koneksi.php';

// Ambil data dari form
$username = $_POST['username'];
$password = $_POST['password'];

// Cek apakah data yang dikirim kosong
if (empty($username) || empty($password)) {
    // Arahkan kembali ke halaman login dengan pesan error
    header("Location: index.html?error=Username atau Password tidak boleh kosong");
    exit();
}

// Siapkan query untuk mencari user berdasarkan username
$sql = "SELECT * FROM user WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Cek apakah user ditemukan (ada 1 baris hasil)
if ($result->num_rows === 1) {
    // Ambil data user dari hasil query
    $user = $result->fetch_assoc();

    // Bandingkan password dari form dengan password di database (TANPA ENKRIPSI)
    if ($password === $user['password']) {
        // Jika password cocok, login berhasil
        // Simpan informasi pengguna ke dalam session
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['logged_in'] = true;

        // Arahkan pengguna ke halaman utama
        header("Location: ../");
        exit();
    } else {
        // Jika password salah
        header("Location: index.html?error=Password salah");
        exit();
    }
} else {
    // Jika username tidak ditemukan
    header("Location: index.html?error=Username tidak ditemukan");
    exit();
}

$stmt->close();
$conn->close();
?>