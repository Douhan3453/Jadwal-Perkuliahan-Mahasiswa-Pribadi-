<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Perkuliahan Mahasiswa</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header>
        <h2 class="logo"><img src="image/polteki.png" align="left" width="100" height="100"></h2>
    </header>
</body>

<body>

    <div class="wrapper">
        <form id="loginForm" onsubmit="handleLogin(event)">
            <h1>Login</h1>
            <div class="input-box">
                <input type="text" id="username" placeholder="Username" required>
                <i class='bx  bx-user'></i>
            </div>
            <div class="input-box">
                <input type="password" id="password" placeholder="Password" required>
                <i class='bx  bx-lock'></i>
            </div>

            <button type="submit" class="btn">Login</button>
            <div class="register-link">
                <p>Tidak Memiliki Akun? <a href="register.php">Daftar</a></p>
            </div>
        </form>
    </div>

    <script>
        function handleLogin(event) {
            event.preventDefault();

            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            // Validasi input tidak kosong
            if (username.trim() !== '' && password.trim() !== '') {
                // Redirect ke halaman home (index.html)
                window.location.href = 'index.php';
            } else {
                alert('Username dan Password tidak boleh kosong!');
            }
        }
    </script>
</body>

</html>