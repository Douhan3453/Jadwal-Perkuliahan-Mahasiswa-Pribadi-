<?php
include 'koneksi.php';

$error = "";

if (isset($_POST['register'])) {
    $nama     = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 1. Cek apakah username sudah ada
    $stmt = $koneksi->prepare("SELECT username FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Username sudah terdaftar!";
   } else {
        // 2. LANGSUNG SIMPAN (Tanpa password_hash)
        // Variabel $password langsung digunakan tanpa diolah terlebih dahulu

        // 3. Simpan ke database menggunakan Prepared Statement
        $insert = $koneksi->prepare("INSERT INTO users (nama, username, password) VALUES (?, ?, ?)");
        
        // Gunakan variabel $password asli di sini
        $insert->bind_param("sss", $nama, $username, $password); 
        
        if ($insert->execute()) {
            header("Location: login.php?pesan=registrasi_berhasil");
            exit;
        } else {
            $error = "Gagal mendaftar, coba lagi.";
        }
    }
}
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register - Politeknik Negeri Batam</title>
    <link rel="stylesheet" href="style-auth.css">
</head>
<body>
    <div class="background-overlay"></div>
    
    <div class="auth-container">
        <div class="auth-box">
            <div class="logo">
                <img src="image/polteki.png" alt="Logo Polibatam">
            </div>
            <h2>Register</h2>

            <?php if ($error): ?>
                <p style="color: #ff4d4d; margin-bottom: 15px; font-size: 0.9rem; font-weight: bold;">
                    <?= htmlspecialchars($error); ?>
                </p>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="input-group">
                    <input type="text" name="nama" placeholder="Nama Lengkap" required>
                </div>
                <div class="input-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" name="register" class="btn-login">Register</button>
            </form>

            <p class="footer-text">Sudah punya akun? <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>