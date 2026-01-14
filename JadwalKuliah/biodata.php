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

    // Cek apakah user ingin menghapus foto
    if (isset($_POST['hapus_foto']) && $_POST['hapus_foto'] == 1) {
        // Hapus foto lama jika ada
        if (!empty($foto) && file_exists('uploads/' . $foto)) {
            unlink('uploads/' . $foto);
        }
        $foto = ''; // Set kosong
    }
    // Jika ada upload foto baru
    elseif (!empty($_FILES['foto']['name'])) {
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
                // Hapus foto lama jika ada
                if (!empty($foto) && file_exists('uploads/' . $foto)) {
                    unlink('uploads/' . $foto);
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
    <link rel="stylesheet" href="biodata.css">
   
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
                <div class="avatar-card">
                    <?php if (!empty($user['foto']) && file_exists('uploads/' . $user['foto'])): ?>
                        <img src="uploads/<?= htmlspecialchars($user['foto']); ?>"
                            id="currentAvatar"
                            alt="Foto Profil">
                    <?php else: ?>
                        <div class="avatar-icon">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    <?php endif; ?>
                </div>
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
                        <?php if (!empty($user['foto']) && file_exists('uploads/' . $user['foto'])): ?>
                            <img src="uploads/<?= htmlspecialchars($user['foto']); ?>"
                                id="previewAvatar"
                                alt="Preview Foto">
                            <div class="current-foto">
                                Foto saat ini: <?= htmlspecialchars($user['foto']) ?>
                            </div>
                        <?php else: ?>
                            <div class="avatar-icon" id="avatarIconPreview">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <div class="no-photo-message" id="noPhotoMessage">Tidak ada foto</div>
                        <?php endif; ?>
                    </div>

                    <!-- ... form fields tetap sama ... -->

                    <div class="form-group">
                        <label>Foto Profil</label>
                        <input type="file" name="foto" id="fotoInput" accept="image/*"
                            onchange="previewImage(event)">
                        <div class="file-info">Maksimal 2MB. Format: JPG, JPEG, PNG, GIF</div>
                        <small>Kosongkan jika tidak ingin mengubah foto</small>
                        <div style="margin-top: 10px;">
                            <label>
                                <input type="checkbox" name="hapus_foto" id="hapusFotoCheckbox" value="1">
                                Hapus foto saat ini
                            </label>
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
            // Reset preview
            resetPreview();
            document.getElementById('fotoInput').value = '';
            document.getElementById('hapusFotoCheckbox').checked = false;
        }

        function resetPreview() {
            const previewAvatar = document.getElementById('previewAvatar');
            const avatarIconPreview = document.getElementById('avatarIconPreview');
            const noPhotoMessage = document.getElementById('noPhotoMessage');
            
            <?php if (!empty($user['foto']) && file_exists('uploads/' . $user['foto'])): ?>
                // Jika ada foto, tampilkan foto
                if (previewAvatar) {
                    previewAvatar.src = 'uploads/<?= htmlspecialchars($user['foto']) ?>';
                    previewAvatar.style.display = 'block';
                }
                if (avatarIconPreview) avatarIconPreview.style.display = 'none';
                if (noPhotoMessage) noPhotoMessage.style.display = 'none';
            <?php else: ?>
                // Jika tidak ada foto, tampilkan icon
                if (previewAvatar) previewAvatar.style.display = 'none';
                if (avatarIconPreview) avatarIconPreview.style.display = 'flex';
                if (noPhotoMessage) noPhotoMessage.style.display = 'block';
            <?php endif; ?>
        }

        // Preview image before upload
        function previewImage(event) {
            const input = event.target;
            const previewAvatar = document.getElementById('previewAvatar');
            const avatarIconPreview = document.getElementById('avatarIconPreview');
            const noPhotoMessage = document.getElementById('noPhotoMessage');

            // Reset checkbox hapus foto jika mengupload foto baru
            document.getElementById('hapusFotoCheckbox').checked = false;

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    // Pastikan elemen previewAvatar ada
                    if (!previewAvatar) {
                        // Buat elemen img jika belum ada
                        const avatarPreviewDiv = document.querySelector('.avatar-preview');
                        const img = document.createElement('img');
                        img.id = 'previewAvatar';
                        img.alt = 'Preview Foto';
                        img.style.width = '200px';
                        img.style.height = '200px';
                        img.style.borderRadius = '6px';
                        img.style.objectFit = 'cover';
                        img.src = e.target.result;
                        
                        // Sembunyikan icon
                        if (avatarIconPreview) avatarIconPreview.style.display = 'none';
                        if (noPhotoMessage) noPhotoMessage.style.display = 'none';
                        
                        // Hapus elemen yang ada dan tambahkan yang baru
                        const existingImg = document.getElementById('previewAvatar');
                        if (existingImg) existingImg.remove();
                        avatarPreviewDiv.prepend(img);
                    } else {
                        previewAvatar.src = e.target.result;
                        previewAvatar.style.display = 'block';
                        if (avatarIconPreview) avatarIconPreview.style.display = 'none';
                        if (noPhotoMessage) noPhotoMessage.style.display = 'none';
                    }
                }

                reader.readAsDataURL(input.files[0]);

                // Validasi ukuran file (maksimal 2MB)
                const fileSize = input.files[0].size / 1024 / 1024; // dalam MB
                if (fileSize > 2) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    input.value = '';
                    resetPreview();
                }
            } else {
                resetPreview();
            }
        }

        // Handle checkbox hapus foto
        document.getElementById('hapusFotoCheckbox')?.addEventListener('change', function() {
            if (this.checked) {
                // Reset input file
                document.getElementById('fotoInput').value = '';
                // Tampilkan icon (foto akan dihapus)
                const previewAvatar = document.getElementById('previewAvatar');
                const avatarIconPreview = document.getElementById('avatarIconPreview');
                const noPhotoMessage = document.getElementById('noPhotoMessage');
                
                if (previewAvatar) previewAvatar.style.display = 'none';
                if (avatarIconPreview) {
                    avatarIconPreview.style.display = 'flex';
                    avatarIconPreview.innerHTML = '<i class="fa-solid fa-user"></i>';
                }
                if (noPhotoMessage) {
                    noPhotoMessage.style.display = 'block';
                    noPhotoMessage.textContent = 'Foto akan dihapus';
                }
            } else {
                resetPreview();
            }
        });

        // ... close modal dan validasi tetap sama ...

        // Validasi form sebelum submit
        document.getElementById('editForm').addEventListener('submit', function(e) {
            const fotoInput = document.getElementById('fotoInput');
            const hapusFotoCheckbox = document.getElementById('hapusFotoCheckbox');
            
            // Jika checkbox hapus foto dicentang, beri nilai pada hidden input
            if (hapusFotoCheckbox && hapusFotoCheckbox.checked) {
                // Tambahkan hidden input untuk menandai penghapusan foto
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'hapus_foto';
                hiddenInput.value = '1';
                this.appendChild(hiddenInput);
            }
            
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