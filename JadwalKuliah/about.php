<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$pageTitle = "Tentang Pembuat";
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="stylesheet" href="stylee.css">
  <link rel="stylesheet" href="about.css">
</head>
<body>

<header class="navbar">
    <div class="logo">
        <img src="image/polteki.png" alt="Logo">
        <span>Politeknik Negeri Batam</span>
    </div>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="biodata.php">Biodata</a></li>
            <li><a href="informasi.php">Informasi</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="logout.php" class="logout">Logout</a></li>
        </ul>
    </nav>
</header>

<div class="about-profiles">

  <div class="about-profiles">

  <div class="profile-card">
    <img src="image/douham.jpg" alt="Profil 1">
    <div class="profile-content">
      <h2>M Ferdy Douhan M</h2>
      <p class="profile-role">Pengembang Web • Mahasiswa</p>
      <p class="profile-desc">Deskripsi singkat pembuat.</p>
      <div class="profile-links">
        <a href="#">Email</a>
        <a href="#">GitHub</a>
      </div>
    </div>
  </div>
  <div class="profile-card">
    <img src="image/bunga.jpg" alt="Profil 2">
    <div class="profile-content">
      <h2>Bunga Rasmi Marsinta </h2>
      <p class="profile-role">Pengembang Web • Mahasiswa</p>
      <p class="profile-desc">Deskripsi singkat pembuat.</p>
      <div class="profile-links">
        <a href="#">Email</a>
        <a href="#">GitHub</a>
      </div>
    </div>

</div>



<footer style="text-align:center; margin:28px 0; color:#666;">
  &copy; <?= date('Y') ?> Tim Pengembang — Semua hak cipta.
</footer>

</body>
</html>