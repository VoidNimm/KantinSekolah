<?php
$host = "localhost";     // host database
$user = "root";          // username database
$pass = "";              // password database
$db   = "kantin_sekolah"; // nama database

$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
