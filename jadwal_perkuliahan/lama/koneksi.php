<?php
$koneksi = mysqli_connect(
    "localhost",
    "root",
    "",
    "jadwalperkuliahan" // ← HARUS SAMA DENGAN phpMyAdmin
);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
