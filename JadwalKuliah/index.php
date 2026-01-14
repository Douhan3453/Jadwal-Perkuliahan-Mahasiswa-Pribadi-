<?php
/* ================= SESSION & KEAMANAN ================= */
session_start();
include 'koneksi.php';

// Jika belum login, tendang ke login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

/* ================= AMBIL DATA JADWAL =================
   Kita ambil SEMUA jadwal dari database
   lalu dikelompokkan berdasarkan TANGGAL
*/
$jadwal = [];
$q = mysqli_query($koneksi, "SELECT * FROM jadwal");

while ($row = mysqli_fetch_assoc($q)) {
    // Contoh:
    // $jadwal['2026-01-15'][] = data jadwal
    $jadwal[$row['tanggal']][] = $row;
}

/* ================= BULAN & TAHUN =================
   Kalau URL punya ?bulan= & ?tahun= → pakai itu
   Kalau tidak → pakai bulan & tahun sekarang
*/
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

// Hari pertama jatuh di hari apa (0 = Minggu)
$firstDay = date('w', strtotime("$tahun-$bulan-01"));

// Jumlah hari dalam bulan
$totalHari = date('t', strtotime("$tahun-$bulan-01"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Perkuliahan Mahasiswa</title>
    <link rel="stylesheet" href="stylee.css">
</head>

<body>

<!-- ================= NAVBAR ================= -->
<header class="navbar">
    <div class="logo">
        <img src="image/polteki.png">
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

<main class="main-center">
<div class="table-responsive" style="margin-top:30px; text-align:center;">
    <h2 class="title-main">JADWAL PERKULIAHAN MAHASISWA</h2>
    <p class="subtitle">Kalender Akademik</p>

    <!-- ================= NAVIGASI BULAN ================= -->
    <?php
    // Tombol bulan sebelumnya & berikutnya
    $prevBulan = date('m', strtotime("-1 month", strtotime("$tahun-$bulan-01")));
    $prevTahun = date('Y', strtotime("-1 month", strtotime("$tahun-$bulan-01")));
    $nextBulan = date('m', strtotime("+1 month", strtotime("$tahun-$bulan-01")));
    $nextTahun = date('Y', strtotime("+1 month", strtotime("$tahun-$bulan-01")));
    ?>

    <div class="calendar-header">
        <a href="?bulan=<?= $prevBulan ?>&tahun=<?= $prevTahun ?>">‹</a>
        <strong><?= date('F Y', strtotime("$tahun-$bulan-01")) ?></strong>
        <a href="?bulan=<?= $nextBulan ?>&tahun=<?= $nextTahun ?>">›</a>
    </div>

    <!-- ================= KALENDER ================= -->
    <div class="calendar-grid">
        <?php
        /* ===== HEADER HARI ===== */
        $hari = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
        foreach ($hari as $h) {
            echo "<div class='cal-cell head'>$h</div>";
        }

        /* ===== KOTAK KOSONG SEBELUM TGL 1 ===== */
        for ($i = 0; $i < $firstDay; $i++) {
            echo "<div class='cal-cell empty'></div>";
        }

        /* ===== ISI TANGGAL ===== */
        for ($tgl = 1; $tgl <= $totalHari; $tgl++) {

            // Format tanggal harus sama dengan database
            $tanggalFull = "$tahun-$bulan-" . str_pad($tgl, 2, '0', STR_PAD_LEFT);

            echo "<div class='cal-cell day'>";

            /* JIKA TANGGAL ADA DI DATABASE */
            if (isset($jadwal[$tanggalFull])) {

                // Tanggal jadi link
                echo "<a href='?bulan=$bulan&tahun=$tahun&tanggal=$tanggalFull'>$tgl</a>";

                // Titik penanda
                echo "<div class='marker'></div>";

            } else {
                // Kalau tidak ada jadwal
                echo $tgl;
            }

            echo "</div>";
        }
        ?>
    </div>

    <!-- ================= ISI PENANDA ================= -->
    <?php if (isset($_GET['tanggal'])): ?>
        <h3 style="margin-top:30px;">
            Jadwal Tanggal: <?= $_GET['tanggal']; ?>
        </h3>

        <?php
        $tgl = $_GET['tanggal'];

        // Tampilkan semua jadwal di tanggal tersebut
        if (isset($jadwal[$tgl])) {
            foreach ($jadwal[$tgl] as $j) {
                echo "
                <div class='jadwal-item'>
                    <strong>{$j['mata_kuliah']}</strong><br>
                    Jam: {$j['jam']}<br>
                    Ruang: {$j['ruang']}
                </div><hr>";
            }
        } else {
            echo "<p>Tidak ada jadwal.</p>";
        }
        ?>
    <?php endif; ?>

</div>
</main>

</body>
</html>
