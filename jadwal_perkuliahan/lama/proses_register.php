<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $phone    = $_POST['phone'];

    $query = mysqli_query($koneksi,
        "INSERT INTO users (username,email,password,phone)
         VALUES ('$username','$email','$password','$phone')"
    );

    if (!$query) {
        die("Gagal insert: " . mysqli_error($koneksi));
    }

    echo "<script>
        alert('Registrasi berhasil');
        window.location='login.php';
    </script>";
}
?>
