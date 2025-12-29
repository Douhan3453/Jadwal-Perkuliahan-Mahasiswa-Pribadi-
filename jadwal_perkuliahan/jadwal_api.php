<?php
$conn = new mysqli("localhost", "root", "", "jadwalmahasiswa");
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'msg' => 'DB error']));
}

/* ===== AMBIL JADWAL ===== */
if ($_GET['aksi'] ?? '' === 'ambil') {
    $res = $conn->query("SELECT * FROM jadwal ORDER BY tanggal, jam");
    echo json_encode($res->fetch_all(MYSQLI_ASSOC));
    exit;
}

/* ===== SIMPAN JADWAL ===== */
if ($_POST['aksi'] ?? '' === 'simpan') {
    $stmt = $conn->prepare(
        "INSERT INTO jadwal (tanggal, hari, jam, mata_kuliah, semester, ruang)
     VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "ssssis",
        $_POST['tanggal'],
        $_POST['hari'],
        $_POST['jam'],
        $_POST['mata_kuliah'],
        $_POST['semester'],
        $_POST['ruang']
    );
    $stmt->execute();

    echo json_encode(['status' => 'ok']);
    exit;
}

/* ===== HAPUS JADWAL ===== */
if ($_POST['aksi'] ?? '' === 'hapus') {
    $id = (int)$_POST['id'];
    $conn->query("DELETE FROM jadwal WHERE id=$id");
    echo json_encode(['status' => 'ok']);
    exit;
}
