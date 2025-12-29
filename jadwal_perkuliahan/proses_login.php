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
    header("Location: index.php");
} else {
    echo "<script>
        alert('Username atau Password salah!');
        window.location='login.php';
    </script>";
}
