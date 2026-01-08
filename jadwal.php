<?php
session_start(); // Tambahkan ini di awal

$conn = new mysqli("localhost", "root", "", "jadwalmahasiswa");
if ($conn->connect_error) die("DB Error");

/* SIMPAN */
if (($_POST['aksi'] ?? '') === 'simpan') {
    $stmt = $conn->prepare(
        "INSERT INTO jadwal (tanggal,hari,jam,mata_kuliah,semester,ruang)
         VALUES (?,?,?,?,?,?)"
    );
    $stmt->bind_param(
        "ssssss",
        $_POST['tanggal'],
        $_POST['hari'],
        $_POST['jam'],
        $_POST['matkul'],
        $_POST['semester'],
        $_POST['ruang']
    );
    
    if ($stmt->execute()) {
        $_SESSION['pesan'] = "Jadwal berhasil ditambahkan!";
        echo json_encode(['status' => 'ok', 'message' => 'Jadwal berhasil ditambahkan!', 'id' => $stmt->insert_id]);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan jadwal!']);
        exit;
    }
}

/* AMBIL */
if (($_GET['aksi'] ?? '') === 'ambil') {
    $sql = "SELECT * FROM jadwal";
    if (($_GET['semester'] ?? '') !== 'all') {
        $sql .= " WHERE semester=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $_GET['semester']);
    } else {
        $stmt = $conn->prepare($sql);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    echo json_encode($res->fetch_all(MYSQLI_ASSOC));
    exit;
}

/* HAPUS SEMUA */
if (($_POST['aksi'] ?? '') === 'hapus') {
    $conn->query("TRUNCATE TABLE jadwal");
    echo json_encode(['status' => 'ok']);
    exit;
}

/* HAPUS SATU DATA */
if (($_POST['aksi'] ?? '') === 'hapus_satu') {
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
        exit;
    }
    
    $id = (int)$_POST['id'];
    $stmt = $conn->prepare("DELETE FROM jadwal WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'ok', 
            'message' => 'Jadwal berhasil dihapus!',
            'deleted_rows' => $stmt->affected_rows
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus jadwal!']);
    }
    exit;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Jadwal</title>
    <link rel="stylesheet" href="stylee.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <style>
        /* Popup Notification Styles */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 15px 25px;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 300px;
            max-width: 400px;
            animation: slideIn 0.3s ease-out;
            transform: translateX(0);
        }
        
        .notification.hidden {
            animation: slideOut 0.3s ease-out;
            transform: translateX(120%);
        }
        
        .notification.error {
            background-color: #f44336;
        }
        
        .notification.info {
            background-color: #2196F3;
        }
        
        .notification-content {
            flex-grow: 1;
        }
        
        .notification-close {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            margin-left: 15px;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background-color 0.2s;
        }
        
        .notification-close:hover {
            background-color: rgba(255,255,255,0.2);
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(120%);
            }
            to {
                transform: translateX(0);
            }
        }
        
        @keyframes slideOut {
            from {
                transform: translateX(0);
            }
            to {
                transform: translateX(120%);
            }
        }
        
        /* Styling untuk tombol hapus */
        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-weight: 500;
        }
        
        .btn-danger {
            background-color: #f44336;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #d32f2f;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        /* Styling untuk kolom aksi */
        .actions-cell {
            display: flex;
            gap: 5px;
            justify-content: center;
        }
        
        /* Loading state untuk tombol hapus */
        .btn-loading {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .btn-loading:hover {
            transform: none;
            box-shadow: none;
        }
    </style>
</head>

<body>
    <!-- Notification Popup -->
    <div id="notification" class="notification hidden">
        <div class="notification-content" id="notification-content"></div>
        <button class="notification-close" onclick="hideNotification()">×</button>
    </div>

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

    <main class="container">
        <h2>Jadwal</h2>

        <section class="table-responsive">
            <div class="controls">
                <!-- Form Tambah Jadwal -->
                <form id="schedule-form" class="schedule-form">
                    <label>
                        Hari:
                        <select id="hari" name="hari">
                            <option>Senin</option>
                            <option>Selasa</option>
                            <option>Rabu</option>
                            <option>Kamis</option>
                            <option>Jumat</option>
                            <option>Sabtu</option>
                        </select>
                    </label>

                    <label>
                        Tanggal (opsional):
                        <input type="date" id="tanggal" name="tanggal">
                    </label>

                    <label>
                        Jam:
                        <select id="jam" name="jam" required>
                            <?php for ($i = 8; $i <= 23; $i++): ?>
                                <option value="<?= sprintf('%02d:00', $i) ?>">
                                    <?= sprintf('%02d:00', $i) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </label>

                    <label>
                        Mata Kuliah:
                        <input type="text" id="matkul" name="matkul" placeholder="Mata Kuliah" required>
                    </label>

                    <label>
                        Semester:
                        <select id="semester" name="semester" required>
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?= $i ?>">Semester <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </label>

                    <label>
                        Ruang:
                        <input type="text" id="ruang" name="ruang" placeholder="Ruang" required>
                    </label>

                    <button type="submit" class="btn">
                        Tambah Jadwal
                    </button>
                </form>

                <!-- Filter & Aksi -->
                <div class="filter-actions">
                    <label for="filter-semester">Filter Semester:</label>
                    <select id="filter-semester">
                        <option value="all">Semua Semester</option>
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?= $i ?>">Semester <?= $i ?></option>
                        <?php endfor; ?>
                    </select>

                    <button id="clear-all" class="btn outline danger">
                        Hapus Semua
                    </button>
                    <button id="print-pdf" class="btn outline" title="Cetak jadwal ke PDF">
                        Cetak PDF
                    </button>
                </div>
            </div>

            <!-- Tabel Jadwal -->
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Hari</th>
                        <th>Jam</th>
                        <th>Mata Kuliah</th>
                        <th>Semester</th>
                        <th>Ruang</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody"></tbody>
            </table>
        </section>

         <div style="text-align: center;">
        <a href="informasi.php" class="btn outline back-btn">← Kembali ke Informasi</a>
    </div>
    </main>

    <script>
        const tbody = document.getElementById('tbody');
        const filter = document.getElementById('filter-semester');
        const notification = document.getElementById('notification');
        const notificationContent = document.getElementById('notification-content');

        // Fungsi untuk menampilkan notifikasi
        function showNotification(message, type = 'success') {
            notificationContent.textContent = message;
            notification.className = 'notification ' + type;
            notification.classList.remove('hidden');
            
            // Auto hide setelah 5 detik
            setTimeout(() => {
                hideNotification();
            }, 5000);
        }

        // Fungsi untuk menyembunyikan notifikasi
        function hideNotification() {
            notification.classList.add('hidden');
        }

        // Fungsi untuk menghapus satu jadwal
        function deleteSchedule(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus jadwal ini?')) {
                return;
            }
            
            // Dapatkan tombol yang diklik
            const deleteButton = event.target;
            const originalText = deleteButton.innerHTML;
            
            // Set loading state
            deleteButton.innerHTML = 'Menghapus...';
            deleteButton.classList.add('btn-loading');
            deleteButton.disabled = true;
            
            fetch('jadwal.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    aksi: 'hapus_satu',
                    id: id
                })
            })
            .then(response => response.json())
            .then(data => {
                // Reset tombol
                deleteButton.innerHTML = originalText;
                deleteButton.classList.remove('btn-loading');
                deleteButton.disabled = false;
                
                if (data.status === 'ok') {
                    showNotification(data.message || 'Jadwal berhasil dihapus!', 'success');
                    // Reload data
                    loadData();
                } else {
                    showNotification(data.message || 'Gagal menghapus jadwal!', 'error');
                }
            })
            .catch(error => {
                // Reset tombol
                deleteButton.innerHTML = originalText;
                deleteButton.classList.remove('btn-loading');
                deleteButton.disabled = false;
                
                console.error('Error:', error);
                showNotification('Terjadi kesalahan saat menghapus!', 'error');
            });
        }

        /* LOAD DATA + FILTER */
        function loadData() {
            fetch(`jadwal.php?aksi=ambil&semester=${filter.value}`)
                .then(r => r.json())
                .then(data => {
                    tbody.innerHTML = '';
                    
                    if (data.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #888;">
                                    <div style="font-size: 48px; margin-bottom: 20px;">📅</div>
                                    <h3 style="color: #aaa; margin-bottom: 10px;">Belum ada jadwal</h3>
                                    <p style="color: #666;">Tambahkan jadwal perkuliahan Anda</p>
                                </td>
                            </tr>
                        `;
                        return;
                    }
                    
                    data.forEach(d => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${d.tanggal ? d.tanggal : '-'}</td>
                                <td>${d.hari}</td>
                                <td>${d.jam}</td>
                                <td>${d.mata_kuliah}</td>
                                <td>Semester ${d.semester}</td>
                                <td>${d.ruang}</td>
                                <td style="text-align: center;">
                                    <button onclick="deleteSchedule(${d.id})" 
                                            class="btn-small btn-danger"
                                            title="Hapus jadwal">
                                        Hapus
                                    </button>
                                </td>
                            </tr>`;
                    });
                })
                .catch(error => {
                    console.error('Error loading data:', error);
                    showNotification('Gagal memuat data jadwal!', 'error');
                });
        }
        
        // Load data saat halaman dimuat
        loadData();

        /* FILTER EVENT */
        filter.addEventListener('change', loadData);

        /* SIMPAN */
        document.getElementById('schedule-form').onsubmit = e => {
            e.preventDefault();
            const form = e.target;
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            // Set loading state
            submitButton.innerHTML = 'Menyimpan...';
            submitButton.disabled = true;
            
            const fd = new FormData(form);
            fd.append('aksi', 'simpan');
            
            fetch('jadwal.php', {
                method: 'POST',
                body: fd
            })
            .then(response => response.json())
            .then(data => {
                // Reset tombol
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
                
                if (data.status === 'ok') {
                    // Tampilkan notifikasi sukses
                    showNotification(data.message || 'Jadwal berhasil ditambahkan!', 'success');
                    
                    // Reset form
                    form.reset();
                    
                    // Reload data
                    loadData();
                } else {
                    showNotification(data.message || 'Gagal menambahkan jadwal!', 'error');
                }
            })
            .catch(error => {
                // Reset tombol
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
                
                console.error('Error:', error);
                showNotification('Terjadi kesalahan saat menyimpan!', 'error');
            });
        };

        /* HAPUS SEMUA */
        document.getElementById('clear-all').onclick = () => {
            if (confirm('Apakah Anda yakin ingin menghapus semua jadwal?')) {
                const deleteAllButton = document.getElementById('clear-all');
                const originalText = deleteAllButton.innerHTML;
                
                // Set loading state
                deleteAllButton.innerHTML = 'Menghapus...';
                deleteAllButton.disabled = true;
                
                fetch('jadwal.php', {
                    method: 'POST',
                    body: new URLSearchParams({
                        aksi: 'hapus'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Reset tombol
                    deleteAllButton.innerHTML = originalText;
                    deleteAllButton.disabled = false;
                    
                    if (data.status === 'ok') {
                        showNotification('Semua jadwal berhasil dihapus!', 'success');
                        loadData();
                    } else {
                        showNotification('Gagal menghapus jadwal!', 'error');
                    }
                })
                .catch(error => {
                    // Reset tombol
                    deleteAllButton.innerHTML = originalText;
                    deleteAllButton.disabled = false;
                    
                    console.error('Error:', error);
                    showNotification('Terjadi kesalahan saat menghapus!', 'error');
                });
            }
        };
    </script>
    <!-- jsPDF and html2canvas (for client-side PDF export) -->
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <script>
        async function generatePDF() {
            const btn = document.getElementById('print-pdf');
            if (!btn) return;
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Membuat PDF...';
            btn.disabled = true;

            const element = document.querySelector('.table-responsive');
            try {
                const canvas = await html2canvas(element, { scale: 2 });
                const imgData = canvas.toDataURL('image/png');
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF('p', 'mm', 'a4');
                const pdfWidth = pdf.internal.pageSize.getWidth();
                const imgProps = pdf.getImageProperties(imgData);
                const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

                pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                pdf.save('jadwal.pdf');
                showNotification('PDF berhasil dibuat.', 'success');
            } catch (err) {
                console.error('PDF error:', err);
                showNotification('Gagal membuat PDF.', 'error');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        document.getElementById('print-pdf').addEventListener('click', generatePDF);
    </script>
</body>
</html>