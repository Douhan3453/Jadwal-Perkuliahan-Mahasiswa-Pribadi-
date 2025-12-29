<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <header>
        <h2 class="logo"><img src="image/polteki.png" align="left" width="100" height="100"></h2>
    </header>

    <div class="wrapper">
        <form id="registerForm" action="proses_register.php" method="POST" onsubmit="handleRegister(event)">
            <h1>Registrasi</h1>

            <div class="input-box">
                <input type="text" id="username" name="username" placeholder="Username" required>
                <i class='bx bx-user'></i>
            </div>

            <div class="input-box">
                <input type="email" id="email" name="email" placeholder="Email" required>
                <i class='bx bx-envelope'></i>
            </div>

            <div class="input-box">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <i class='bx bx-lock'></i>
            </div>

            <div class="input-box">
                <input type="text" id="phone" name="phone" placeholder="No. Handphone" required>
                <i class='bx bx-phone'></i>
            </div>

            <button type="submit" class="btn">Daftar</button>
            <div class="register-link">
                <p>Sudah punya akun? <a href="login.php">Login</a></p>
            </div>
        </form>
    </div>

    <script>
        function handleRegister(event) {
            event.preventDefault();

            const form = document.getElementById('registerForm');
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const phone = document.getElementById('phone').value.trim();

            if (!username || !email || !password || !phone) {
                alert('Semua field wajib diisi!');
                return;
            }

            const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRe.test(email)) {
                alert('Masukkan alamat email yang valid.');
                return;
            }

            // Jika valid, submit form ke proses_register.php
            form.submit();
        }
    </script>
</body>
</html>
