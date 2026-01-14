<?php
$conn = new mysqli("localhost", "root", "", "jadwalmahasiswa (2)");
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
// Pastikan pengecekan kondisi benar dengan tanda kurung
if (($_POST['aksi'] ?? '') === 'hapus') {
    
    // Periksa apakah ID ada di dalam request
    if (isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        
        // Gunakan prepared statement untuk keamanan
        $stmt = $conn->prepare("DELETE FROM jadwal WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'ok', 'msg' => 'Data berhasil dihapus']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Gagal menghapus data']);
        }
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'ID tidak ditemukan']);
    }
    exit;
}

/* ===== HAPUS SEMUA JADWAL ===== */
if (($_POST['aksi'] ?? '') === 'hapus_semua') {
    if ($conn->query("TRUNCATE TABLE jadwal")) {
        echo json_encode(['status' => 'ok', 'msg' => 'Semua data dikosongkan']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}