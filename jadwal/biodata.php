<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biodata</title>
    <link rel="stylesheet" href="stylee.css">
</head>
<body>

   <header class="navbar">
        <div class="logo">
            <img src="logo-polibatam.png" alt="Logo">
            <span>Politeknik Negeri Batam</span>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="biodata.php">Biodata</a></li>
                <li><a href="informasi.php">Informasi</a></li>
                <li><a href="logout.php" class="logout">Logout</a></li>
            </ul>
        </nav>
    </header>

</body>
</html>

 <div class="biodata-container">

    <h1 class="biodata-title">
        Daftar Biodata Mahasiswa Pembuat<br>
        Web Jadwal Perkuliahan Mahasiswa (Pribadi)
    </h1>

    <div class="biodata-grid">

        <div class="biodata-card highlight">
            <img src="image/douham.jpg" class="avatar">
            <h3>M Ferdy Douhan Mahendra</h3>
            <p>NIM: 3312511051 – Teknik Informatika</p>
            <span>douhanmahendraaa20@gmail.com</span>
        </div>

        <div class="biodata-card">
            <img src="image/bunga.jpg" class="avatar">
            <h3>Bunga Rasmi Marsinta Br Hutagalung</h3>
            <p>NIM: 3312511049 – Teknik Informatika</p>
            <span>bngrasamii@gmail.com</span>
        </div>

        <div class="biodata-card">
            <img src="image/rifka.jpg" class="avatar">
            <h3>Rifka Yulfani Simanjuntak</h3>
            <p>NIM: 3312511050 – Teknik Informatika</p>
            <span>rifkayulfani20@gmail.com</span>
        </div>

    </div>
</div>

</body>

</html>