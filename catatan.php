<?php
$conn = new mysqli("localhost", "root", "", "jadwalmahasiswa");
if ($conn->connect_error) {
  die(json_encode(['status' => 'error', 'msg' => 'DB Error']));
}

/* ================= SIMPAN ================= */
if (isset($_POST['aksi']) && $_POST['aksi'] === 'simpan') {
  // Validasi input
  if (empty(trim($_POST['isi']))) {
    echo json_encode(['status' => 'error', 'msg' => 'Isi catatan tidak boleh kosong']);
    exit;
  }
  
  $isi = trim($_POST['isi']);
  $semester = $_POST['semester'] ?? '';
  $jenis = $_POST['jenis'] ?? 'Pribadi';
  
  $stmt = $conn->prepare(
    "INSERT INTO catatan (isi_catatan, semester, jenis) VALUES (?, ?, ?)"
  );
  $stmt->bind_param("sss", $isi, $semester, $jenis);
  
  if ($stmt->execute()) {
    echo json_encode([
      'status' => 'ok',
      'msg' => 'Catatan berhasil disimpan!',
      'id' => $stmt->insert_id
    ]);
  } else {
    echo json_encode(['status' => 'error', 'msg' => 'Gagal menyimpan catatan']);
  }
  exit;
}

/* ================= AMBIL ================= */
if (isset($_GET['aksi']) && $_GET['aksi'] === 'ambil') {
  $res = $conn->query("SELECT * FROM catatan ORDER BY id_catatan DESC");
  echo json_encode($res->fetch_all(MYSQLI_ASSOC));
  exit;
}

/* ================= HAPUS ================= */
if (isset($_POST['aksi']) && $_POST['aksi'] === 'hapus') {

  if (empty($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode([
      'status' => 'error',
      'msg' => 'ID tidak valid'
    ]);
    exit;
  }

  $id = (int) $_POST['id'];

  $stmt = $conn->prepare("DELETE FROM catatan WHERE id_catatan = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();

  echo json_encode([
    'status' => 'ok',
    'msg' => 'Catatan berhasil dihapus!',
    'deleted' => $stmt->affected_rows
  ]);
  exit;
}
?>



<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Catatan Mahasiswa</title>
  <link rel="stylesheet" href="stylee.css">
  <style>
    /* Notification Styles */
    .notification {
      position: fixed;
      top: 100px;
      right: 20px;
      padding: 15px 20px;
      border-radius: 5px;
      color: white;
      font-weight: bold;
      z-index: 1000;
      animation: slideIn 0.3s ease-out;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      max-width: 300px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    
    .notification.success {
      background-color: #4CAF50;
      border-left: 5px solid #388E3C;
    }
    
    .notification.error {
      background-color: #f44336;
      border-left: 5px solid #d32f2f;
    }
    
    .notification.info {
      background-color: #2196F3;
      border-left: 5px solid #1976D2;
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
        transform: translateX(100%);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
    
    @keyframes slideOut {
      from {
        opacity: 1;
      }
      to {
        opacity: 0;
      }
    }
    
    .notification.hidden {
      animation: slideOut 0.3s ease-out forwards;
    }
    
    /* Loading state */
    .loading {
      opacity: 0.7;
      cursor: not-allowed;
    }
    
    /* Button styles */
    .btn-action {
      padding: 6px 12px;
      font-size: 12px;
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.3s ease;
      border: 1px solid transparent;
      font-weight: 500;
      background-color: rgba(0, 0, 0, 0.5);
      color: white;
    }
    
    .btn-action:hover {
      background-color: rgba(0, 0, 0, 0.7);
      transform: translateY(-2px);
    }
    
    .btn-action:focus {
      outline: none;
      border-color: #f44336;
      box-shadow: 0 0 0 3px rgba(244, 67, 54, 0.3);
    }
    
    .btn-danger {
      background-color: rgba(244, 67, 54, 0.1);
      border-color: #f44336;
    }
    
    .btn-danger:hover {
      background-color: rgba(244, 67, 54, 0.3);
    }
    
    /* Form styling */
    #note-input {
      width: 100%;
      min-height: 120px;
      padding: 12px;
      border: 1px solid #444;
      border-radius: 6px;
      background-color: #2a2a2a;
      color: #fff;
      font-size: 14px;
      resize: vertical;
      margin-bottom: 15px;
    }
    
    #note-input:focus {
      outline: none;
      border-color: #007bff;
      box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
    }
    
    .form-select {
      padding: 8px 12px;
      border: 1px solid #444;
      border-radius: 4px;
      background-color: #2a2a2a;
      color: #fff;
      min-width: 150px;
    }
    
    /* Tag styling */
    .tag {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .tag.pribadi {
      background-color: rgba(76, 175, 80, 0.15);
      color: #4CAF50;
      border: 1px solid #4CAF50;
    }
    
    .tag.perkuliahan {
      background-color: rgba(33, 150, 243, 0.15);
      color: #2196F3;
      border: 1px solid #2196F3;
    }
    
    .tag.lainnya {
      background-color: rgba(255, 193, 7, 0.15);
      color: #FFC107;
      border: 1px solid #FFC107;
    }
  </style>
</head>

<body>
  <!-- Notification Container -->
  <div id="notification-container" style="position: fixed; top: 100px; right: 20px; z-index: 1000;"></div>

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
    <section class="table-responsive">
          <h2>Catatan Mahasiswa</h2>
        
        <form id="note-form">
            <textarea id="note-input" placeholder="Tulis catatan penting Anda di sini..." required></textarea>
            
          <div style="display:flex; gap:15px; align-items:center; flex-wrap:wrap;">
            <div style="display:flex; gap:15px; align-items:center;">
              <label style="color: #ccc; font-size: 0.9rem;">Semester:</label>
              <select id="note-semester" class="form-select">
                <option value="">Tanpa Semester</option>
                <?php for($i=1; $i<=8; $i++) echo "<option value='$i'>Semester $i</option>"; ?>
              </select>

              <label style="color: #ccc; font-size: 0.9rem;">Kategori:</label>
              <select id="note-type" class="form-select">
                <option value="Pribadi">Pribadi</option>
                <option value="Perkuliahan">Perkuliahan</option>
                <option value="Lainnya">Lainnya</option>
              </select>
            </div>
          </div>

          <div style="text-align:center; margin-top:12px;">
            <button type="submit" id="submit-note" class="btn">
              <span id="submit-text">💾 Simpan Catatan</span>
              <span id="loading-text" style="display:none;">Menyimpan...</span>
            </button>
          </div>
        </form>

        <div class="controls">
          <span class="filter-label">Filter:</span>
          <select id="filter-notes-semester" class="form-select">
            <option value="all">Semua Semester</option>
            <?php for($i=1; $i<=8; $i++) echo "<option value='$i'>Semester $i</option>"; ?>
          </select>
          <select id="filter-notes-type" class="form-select">
            <option value="all">Semua Jenis</option>
            <option value="Pribadi">Pribadi</option>
            <option value="Perkuliahan">Perkuliahan</option>
            <option value="Lainnya">Lainnya</option>
          </select>
        </div>

        <table id="notes-table" class="notes-table">
            <thead>
                <tr>
                    <th style="width:50px">No</th>
                    <th>Isi Catatan</th>
                    <th style="width:150px">Semester</th>
                    <th style="width:150px">Jenis</th>
                    <th style="width:100px; text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody id="notes-tbody">
                </tbody>
        </table>
    </section>

    <div style="text-align: center;">
        <a href="informasi.php" class="btn outline back-btn">← Kembali ke Informasi</a>
    </div>
</main>

  <script>
    // Fungsi untuk menampilkan notifikasi
    function showNotification(message, type = 'success') {
      // Hapus notifikasi sebelumnya
      const existing = document.querySelector('.notification');
      if (existing) existing.remove();
      
      // Buat elemen notifikasi baru
      const notification = document.createElement('div');
      notification.className = `notification ${type}`;
      notification.innerHTML = `
        <div class="notification-content">${message}</div>
        <button class="notification-close" onclick="this.parentElement.remove()">×</button>
      `;
      
      document.getElementById('notification-container').appendChild(notification);
      
      // Auto hide setelah 5 detik
      setTimeout(() => {
        if (notification.parentElement) {
          notification.classList.add('hidden');
          setTimeout(() => {
            if (notification.parentElement) {
              notification.remove();
            }
          }, 300);
        }
      }, 5000);
    }

    (function() {
      const form = document.getElementById('note-form');
      const input = document.getElementById('note-input');
      const semInput = document.getElementById('note-semester');
      const typeInput = document.getElementById('note-type');
      const list = document.getElementById('notes-tbody');
      const filterSem = document.getElementById('filter-notes-semester');
      const filterType = document.getElementById('filter-notes-type');
      const submitBtn = document.getElementById('submit-note');
      const submitText = document.getElementById('submit-text');
      const loadingText = document.getElementById('loading-text');

      function loadNotes() {
        fetch('catatan.php?aksi=ambil')
          .then(r => r.json())
          .then(data => {
            list.innerHTML = '';
            if (data.length === 0) {
              list.innerHTML = `
                <tr>
                  <td colspan="5" style="text-align: center; padding: 40px; color: #888;">
                    <div style="font-size: 48px; margin-bottom: 20px;">📝</div>
                    <h3 style="color: #aaa; margin-bottom: 10px;">Belum ada catatan</h3>
                    <p style="color: #666;">Mulailah mencatat hal-hal penting perkuliahan Anda</p>
                  </td>
                </tr>
              `;
              return;
            }
            
            data.forEach((n, i) => {
              if (filterSem.value !== 'all' && n.semester !== filterSem.value) return;
              if (filterType.value !== 'all' && n.jenis !== filterType.value) return;

              list.innerHTML += `
                <tr>
                  <td>${i + 1}</td>
                  <td style="white-space: pre-wrap; line-height: 1.5;">${n.isi_catatan.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</td>
                  <td>${n.semester ? 'Semester ' + n.semester : 'Tanpa Semester'}</td>
                  <td>
                    <span class="tag ${n.jenis.toLowerCase()}">${n.jenis}</span>
                  </td>
                  <td style="text-align:center">
                    <button
                      type="button"
                      class="btn-action btn-danger"
                      onclick="return hapusCatatan(${n.id_catatan})"
                      title="Hapus catatan">
                      Hapus
                    </button>
                  </td>
                </tr>
              `;
            });
          })
          .catch(error => {
            console.error('Error loading notes:', error);
            showNotification('Gagal memuat catatan', 'error');
          });
      }

      window.hapusCatatan = (id) => {
        if (!confirm('Apakah Anda yakin ingin menghapus catatan ini?')) return false;

        const fd = new FormData();
        fd.append('aksi', 'hapus');
        fd.append('id', id);

        fetch('catatan.php', {
            method: 'POST',
            body: fd
          })
          .then(r => r.json())
          .then(res => {
            console.log(res);
            if (res.status === 'ok') {
              showNotification(res.msg || 'Catatan berhasil dihapus!', 'success');
              loadNotes();
            } else {
              showNotification(res.msg || 'Data gagal dihapus', 'error');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan saat menghapus', 'error');
          });

        return false;
      };

      form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validasi client-side
        if (!input.value.trim()) {
          showNotification('Isi catatan tidak boleh kosong', 'error');
          input.focus();
          return;
        }
        
        // Tampilkan loading state
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        submitText.style.display = 'none';
        loadingText.style.display = 'inline';

        const fd = new FormData();
        fd.append('aksi', 'simpan');
        fd.append('isi', input.value.trim());
        fd.append('semester', semInput.value);
        fd.append('jenis', typeInput.value);

        fetch('catatan.php', {
            method: 'POST',
            body: fd
          })
          .then(r => r.json())
          .then(res => {
            // Reset loading state
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
            submitText.style.display = 'inline';
            loadingText.style.display = 'none';
            
            if (res.status === 'ok') {
              // Reset form
              input.value = '';
              semInput.value = '';
              typeInput.value = 'Pribadi';
              
              // Tampilkan notifikasi sukses
              showNotification(res.msg || 'Catatan berhasil disimpan!', 'success');
              
              // Muat ulang daftar catatan
              loadNotes();
              
              // Fokus kembali ke input
              input.focus();
            } else {
              showNotification(res.msg || 'Gagal menyimpan catatan', 'error');
            }
          })
          .catch(error => {
            // Reset loading state
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
            submitText.style.display = 'inline';
            loadingText.style.display = 'none';
            
            console.error('Error saving note:', error);
            showNotification('Terjadi kesalahan koneksi', 'error');
          });
      });

      filterSem.addEventListener('change', loadNotes);
      filterType.addEventListener('change', loadNotes);

      // Load catatan saat halaman dimuat
      loadNotes();
      
      // Fokus ke input catatan saat halaman dimuat
      input.focus();
    })();
  </script>

</body>

</html>