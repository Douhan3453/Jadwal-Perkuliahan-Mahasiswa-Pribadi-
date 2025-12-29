<?php
session_start();
include 'koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM users 
     WHERE username='$username' AND password='$password'"
);

if (mysqli_num_rows($query) > 0) {
    $_SESSION['username'] = $username;
    header("Location: index.php, jadwal.php, informasi.php, catatan.php, biodata.php");
} else {
    echo "<script>
        alert('Username atau Password salah!');
        window.location='login.php';
    </script>";
}
$_SESSION['login'] = true;
$_SESSION['nama']  = $row['nama']; // dari database
$_SESSION['level'] = $row['level']; // opsional
