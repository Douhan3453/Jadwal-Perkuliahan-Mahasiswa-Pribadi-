<?php
include 'koneksi.php';

$error = "";

if (isset($_POST['register'])) {

  $name     = trim($_POST['nama'] ?? '');
$nim      = trim($_POST['nim'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$no_hp    = trim($_POST['no_handphone'] ?? '');
$email    = trim($_POST['email'] ?? '');


    // Validasi semua field wajib diisi
    if (
        $name === "" ||
        $nim === "" ||
        $username === "" ||
        $password === "" ||
        $no_hp === "" ||
        $email === ""
    ) {
        $error = "Semua field wajib diisi!";
    } else {

        // Cek username, email, atau no handphone sudah terdaftar
        $stmt = $koneksi->prepare(
            "SELECT username 
             FROM users 
             WHERE username = ? OR email = ? OR no_hp = ?"
        );
        $stmt->bind_param("sss", $username, $email, $no_hp);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username, email, atau nomor HP sudah terdaftar!";
        } else {

            // Insert data ke database
            $insert = $koneksi->prepare(
                "INSERT INTO users (nim, nama, username, password, no_hp, email)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );

            if ($insert === false) {
                $error = "Error prepare statement: " . $koneksi->error;
            } else {

                $insert->bind_param(
                    "ssssss",
                    $nim,
                    $name,
                    $username,
                    $password,
                    $no_hp,
                    $email
                );

                try {
                    if ($insert->execute()) {
                        header("Location: login.php?pesan=registrasi_berhasil");
                        exit;
                    }
                } catch (mysqli_sql_exception $e) {
                    if ($e->getCode() == 1062) {
                        $error = "Data sudah terdaftar!";
                    } else {
                        $error = "Error database: " . $e->getMessage();
                    }
                }

                $insert->close();
            }
        }

        $stmt->close();
    }
}
?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <link rel="stylesheet" href="regis.css">

    
</head>

<body>

    <div class="auth-container">
        <div class="auth-box">
            <h2>Register</h2>

            <?php if ($error): ?>
                <p style="color:red; margin-bottom:10px;">
                    <?= htmlspecialchars($error); ?>
                </p>
            <?php endif; ?>

            <form method="POST">
                <div class="input-group">
                    <input type="text" name="nama" placeholder="Nama Lengkap" required>
                </div>

                <div class="input-group">
                    <input type="text" name="nim" placeholder="NIM" required>
                </div>

                <div class="input-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>

                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <div class="input-group">
                    <input type="tel" name="no_handphone" placeholder="No.handphone" required>
                </div>

                <div class="input-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>

                <button type="submit" name="register">Register</button>
            </form>

            <p>
                Sudah punya akun? <a href="login.php">Login</a>
            </p>
        </div>
    </div>

</body>

</html>