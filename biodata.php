<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Ambil ID user dari session login
$id = $_SESSION['id'];

// Query ambil data user
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$id'");
$user = mysqli_fetch_assoc($query);

if (!$user) {
    echo "Data biodata tidak ditemukan!";
    exit;
}

// Proses CRUD
$message = "";
$error = "";

// CREATE - Tambah data baru (opsional)
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $nim = mysqli_real_escape_string($koneksi, $_POST['nim']);
    $jurusan = mysqli_real_escape_string($koneksi, $_POST['jurusan']);
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    
    $sql = "INSERT INTO users (nama, nim, jurusan, no_hp, email) 
            VALUES ('$nama', '$nim', '$jurusan', '$no_hp', '$email')";
    
    if (mysqli_query($koneksi, $sql)) {
        $message = "Data berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan data: " . mysqli_error($koneksi);
    }
}

// UPDATE - Edit data
if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $nim = mysqli_real_escape_string($koneksi, $_POST['nim']);
    $jurusan = mysqli_real_escape_string($koneksi, $_POST['jurusan']);
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    
    // Inisialisasi variabel foto
    $foto = $user['foto'];
    
    // Jika ada upload foto
    if (!empty($_FILES['foto']['name'])) {
        $target_dir = "uploads/";
        
        // Buat nama file unik untuk menghindari konflik
        $file_ext = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '_' . time() . '.' . $file_ext;
        $target_file = $target_dir . $new_filename;
        
        // Cek apakah folder uploads ada, jika tidak buat folder
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // Cek apakah file adalah gambar
        $check = getimagesize($_FILES["foto"]["tmp_name"]);
        if ($check !== false) {
            // Tentukan tipe file yang diizinkan
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            $imageFileType = strtolower($file_ext);
            
            // Cek ukuran file (maksimal 2MB)
            if ($_FILES["foto"]["size"] > 2000000) {
                $error = "Ukuran file terlalu besar. Maksimal 2MB.";
            }
            // Cek tipe file
            elseif (!in_array($imageFileType, $allowed_types)) {
                $error = "Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
            }
            // Coba upload file
            elseif (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                // Hapus foto lama jika ada dan bukan foto default
                if (!empty($foto) && $foto != 'bunga.jpg' && file_exists($target_dir . $foto)) {
                    unlink($target_dir . $foto);
                }
                $foto = $new_filename;
                $message .= " Foto berhasil diupload.";
            } else {
                $error = "Maaf, terjadi kesalahan saat mengupload foto.";
            }
        } else {
            $error = "File yang diupload bukan gambar.";
        }
    }
    
    // Jika tidak ada error, update data
    if (empty($error)) {
        $sql = "UPDATE users SET 
                nama = '$nama',
                nim = '$nim',
                jurusan = '$jurusan',
                no_hp = '$no_hp',
                email = '$email',
                foto = '$foto'
                WHERE id = '$id'";
        
        if (mysqli_query($koneksi, $sql)) {
            $message = "Data berhasil diperbarui!" . (isset($message) ? $message : "");
            // Refresh data user
            $query = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$id'");
            $user = mysqli_fetch_assoc($query);
        } else {
            $error = "Gagal memperbarui data: " . mysqli_error($koneksi);
        }
    }
}

// DELETE - Hapus data
if (isset($_GET['hapus'])) {
    // Hapus foto jika ada
    if (!empty($user['foto']) && $user['foto'] != 'bunga.jpg') {
        $target_dir = "uploads/";
        if (file_exists($target_dir . $user['foto'])) {
            unlink($target_dir . $user['foto']);
        }
    }
    
    $sql = "DELETE FROM users WHERE id = '$id'";
    if (mysqli_query($koneksi, $sql)) {
        session_destroy();
        header("Location: login.php");
        exit;
    } else {
        $error = "Gagal menghapus data: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biodata</title>
    <link rel="stylesheet" href="stylee.css">
    <style>
        .form-group {
            margin-bottom: 50px;
        }
        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
            border: 1px #ddd;
            border-radius: 4px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            overflow: auto;
        }
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        .close {
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #aaa;
        }
        .close:hover {
            color: #000;
        }
        .crud-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .avatar-preview {
            margin: 0;
            flex: 0 0 160px;
        }
        .avatar-preview img {
            width: 200px;
            height: 200px;
            border-radius: 6px;
            object-fit: cover;
            display: block;
        }
        .current-foto {
            font-size: 12px;
            color: #666;
            margin-top: 8px;
            text-align: center;
        }
        .file-info {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        /* Perlebar card biodata dan tata letak: foto di kiri, isi di kanan */
        .biodata-card {
            max-width: 900px;
            width: 200%;
            margin: 40px auto;
            display: flex;
            align-items: flex-start;
            gap: 24px;
            text-align: left;
            padding: 28px 30px;
            border: 1px #ddd;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
            box-sizing: border-box;
        }

        /* Kontainer untuk teks biodata (tumpuk vertikal) */
        .biodata-info {
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex: 1 1 auto;
            justify-content: center;
        }

        .biodata-info h3,
        .biodata-info p,
        .biodata-info span {
            margin: 10px 0 0;
        }

        @media (max-width: 768px) {
            .biodata-card {
                flex-direction: column;
                text-align: center;
                padding: 20px;
            }
            .avatar-preview {
                flex: none;
                margin: 0 auto 30px;
            }
            .biodata-info {
                align-items: center;
            }
        }
    </style>
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
                <li><a href="logout.php" class="logout">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <?php if ($message): ?>
            <div class="message success"><?= $message ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>

        <h1 class="biodata-title">
            Selamat Datang, <?= htmlspecialchars($user['nama']); ?>
        </h1>

        <div class="biodata-card highlight">
            <div class="avatar-preview">
                <img src="<?= !empty($user['foto']) ? 'uploads/' . htmlspecialchars($user['foto']) : 'image/bunga.jpg' ?>" 
                     id="currentAvatar" 
                     alt="Foto Profil">
            </div>

            <div class="biodata-info">
                <h3>Nama: <?= htmlspecialchars($user['nama']); ?></h3>
                <p>NIM: <?= htmlspecialchars($user['nim']); ?> </p>
                <p>Jurusan: <?= htmlspecialchars($user['jurusan']); ?></p>
                <p>No. Handphone: <?= htmlspecialchars($user['no_hp']); ?></p>
                <span>Email: <?= htmlspecialchars($user['email']); ?></span>
            </div>
        </div>

        <!-- Tombol CRUD -->
        <div class="crud-buttons">
            <button class="btn btn-primary" onclick="openEditModal()">Edit Biodata</button>
        </div>

        <!-- Modal untuk Edit -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeEditModal()">&times;</span>
                <h2>Edit Biodata</h2>
                <form method="POST" action="" enctype="multipart/form-data" id="editForm">
                    <div class="avatar-preview">
                        <img src="<?= !empty($user['foto']) ? 'uploads/' . htmlspecialchars($user['foto']) : 'image/bunga.jpg' ?>" 
                             id="previewAvatar" 
                             alt="Preview Foto">
                        <div class="current-foto">
                            Foto saat ini: <?= !empty($user['foto']) ? htmlspecialchars($user['foto']) : 'Default' ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>NIM</label>
                        <input type="text" name="nim" value="<?= htmlspecialchars($user['nim']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Jurusan</label>
                        <select name="jurusan" required>
                            <option value="Teknik Informatika" <?= $user['jurusan'] == 'Teknik Informatika' ? 'selected' : '' ?>>Teknik Informatika</option>
                            <option value="Sistem Informasi" <?= $user['jurusan'] == 'Sistem Informasi' ? 'selected' : '' ?>>Sistem Informasi</option>
                            <option value="Teknik Elektro" <?= $user['jurusan'] == 'Teknik Elektro' ? 'selected' : '' ?>>Teknik Elektro</option>
                            <option value="Teknik Mesin" <?= $user['jurusan'] == 'Teknik Mesin' ? 'selected' : '' ?>>Teknik Mesin</option>
                            <option value="Akuntansi" <?= $user['jurusan'] == 'Akuntansi' ? 'selected' : '' ?>>Akuntansi</option>
                            <option value="Manajemen" <?= $user['jurusan'] == 'Manajemen' ? 'selected' : '' ?>>Manajemen</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>No. Handphone</label>
                        <input type="text" name="no_hp" value="<?= htmlspecialchars($user['no_hp']) ?>" required 
                               pattern="[0-9]+" title="Hanya angka yang diperbolehkan">
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Foto Profil</label>
                        <input type="file" name="foto" id="fotoInput" accept="image/*" 
                               onchange="previewImage(event)">
                        <div class="file-info">Maksimal 2MB. Format: JPG, JPEG, PNG, GIF</div>
                        <small>Kosongkan jika tidak ingin mengubah foto</small>
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button type="submit" name="update" class="btn btn-success">Simpan Perubahan</button>
                        <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Modal functions
        function openEditModal() {
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
            // Reset preview gambar
            document.getElementById('previewAvatar').src = '<?= !empty($user['foto']) ? 'uploads/' . htmlspecialchars($user['foto']) : 'image/bunga.jpg' ?>';
            document.getElementById('fotoInput').value = '';
        }
        
        function confirmDelete() {
            if (confirm('Apakah Anda yakin ingin menghapus akun? Semua data termasuk foto akan dihapus permanen! Tindakan ini tidak dapat dibatalkan.')) {
                window.location.href = 'biodata.php?hapus=true';
            }
        }
        
        // Preview image before upload
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('previewAvatar');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                
                reader.readAsDataURL(input.files[0]);
                
                // Validasi ukuran file (maksimal 2MB)
                const fileSize = input.files[0].size / 1024 / 1024; // dalam MB
                if (fileSize > 2) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    input.value = '';
                    preview.src = '<?= !empty($user['foto']) ? 'uploads/' . htmlspecialchars($user['foto']) : 'image/bunga.jpg' ?>';
                }
            }
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            var modal = document.getElementById('editModal');
            if (event.target == modal) {
                closeEditModal();
            }
        }
        
        // Validasi form sebelum submit
        document.getElementById('editForm').addEventListener('submit', function(e) {
            const fotoInput = document.getElementById('fotoInput');
            if (fotoInput.files.length > 0) {
                const file = fotoInput.files[0];
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                
                if (!validTypes.includes(file.type)) {
                    e.preventDefault();
                    alert('Format file tidak didukung. Gunakan JPG, JPEG, PNG, atau GIF.');
                    return false;
                }
            }
            return true;
        });
    </script>

</body>
</html>