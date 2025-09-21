<?php

$host = 'mariadb';
$db_name = 'db_media';
$username = 'root';
$password = 'aldorino04';

$conn = new mysqli($host, $username, $password, $db_name);

// Cek koneksi
if ($conn->connect_error) {

    die("Koneksi Gagal: " . $conn->connect_error);
}

?>