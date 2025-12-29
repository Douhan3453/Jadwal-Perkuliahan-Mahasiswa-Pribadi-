<?php
session_start();
include 'koneksi.php';

$error = ""; // Inisialisasi variabel error agar tidak muncul peringatan undefined

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    // Catatan: Sangat disarankan menggunakan password_hash & password_verify di masa depan
    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM users 
         WHERE username='$username' 
         AND password='$password'"
    );

    if (mysqli_num_rows($query) === 1) {
        $user = mysqli_fetch_assoc($query);

        $_SESSION['login'] = true;
        $_SESSION['id']    = $user['id'];
        $_SESSION['nama']  = $user['nama'];

        // Menampilkan notifikasi JavaScript sebelum pindah halaman
        echo "<script>
                alert('Login Berhasil! Selamat datang, " . $user['nama'] . "');
                window.location.href = 'index.php';
              </script>";
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Politeknik Negeri Batam</title>
    <link rel="stylesheet" href="style-auth.css">
</head>
<body>
    <div class="background-overlay"></div>
    
    <div class="auth-container">
        <div class="auth-box">
            <div class="logo">
                <img src="image/polteki.png" alt="Logo Polibatam">
            </div>
            <h2>Login</h2>

            <?php if ($error): ?>
                <p style="color: #ff4d4d; margin-bottom: 15px; font-size: 0.9rem; font-weight: bold;">
                    <?= $error; ?>
                </p>
            <?php endif; ?>
            
            <form action="" method="POST">
                <div class="input-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" name="login" class="btn-login">Login</button>
            </form>
            <p class="footer-text">Belum punya akun? <a href="register.php">Register</a></p>
        </div>
    </div>
</body>
</html>