<?php
$conn = new mysqli("localhost", "root", "", "jadwalmahasiswa");
if ($conn->connect_error) {
  die(json_encode(['status' => 'error', 'msg' => 'DB Error']));
}

/* ================= SIMPAN ================= */
if (isset($_POST['aksi']) && $_POST['aksi'] === 'simpan') {
  $stmt = $conn->prepare(
    "INSERT INTO catatan (isi_catatan, semester, jenis) VALUES (?, ?, ?)"
  );
  $stmt->bind_param("sss", $_POST['isi'], $_POST['semester'], $_POST['jenis']);
  $stmt->execute();

  echo json_encode(['status' => 'ok']);
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

  <main class="container">
    <section class="table-responsive">
        <h2>Catatan Mahasiswa</h2>
        
        <div class="controls">
            <span class="filter-label">Filter:</span>
            <select id="filter-notes-semester">
                <option value="all">Semua Semester</option>
                <?php for($i=1; $i<=8; $i++) echo "<option value='$i'>Semester $i</option>"; ?>
            </select>
            <select id="filter-notes-type">
                <option value="all">Semua Jenis</option>
                <option value="Pribadi">Pribadi</option>
                <option value="Perkuliahan">Perkuliahan</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </div>

        <form id="note-form">
            <textarea id="note-input" placeholder="Tulis catatan penting Anda di sini..." required></textarea>
            
            <div style="display:flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div style="display:flex; gap:15px; align-items:center;">
                    <label style="color: #ccc; font-size: 0.9rem;">Semester:</label>
                    <select id="note-semester">
                        <option value="">Tanpa Semester</option>
                        <?php for($i=1; $i<=8; $i++) echo "<option value='$i'>Semester $i</option>"; ?>
                    </select>

                    <label style="color: #ccc; font-size: 0.9rem;">Kategori:</label>
                    <select id="note-type">
                        <option value="Pribadi">Pribadi</option>
                        <option value="Perkuliahan">Perkuliahan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                
                <button type="submit" id="submit-note" class="btn">Simpan Catatan</button>
            </div>
        </form>

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
    (function() {
      const form = document.getElementById('note-form');
      const input = document.getElementById('note-input');
      const semInput = document.getElementById('note-semester');
      const typeInput = document.getElementById('note-type');
      const list = document.getElementById('notes-tbody');
      const filterSem = document.getElementById('filter-notes-semester');
      const filterType = document.getElementById('filter-notes-type');

      function loadNotes() {
        fetch('catatan.php?aksi=ambil')
          .then(r => r.json())
          .then(data => {
            list.innerHTML = '';
            data.forEach((n, i) => {
              if (filterSem.value !== 'all' && n.semester !== filterSem.value) return;
              if (filterType.value !== 'all' && n.jenis !== filterType.value) return;

              list.innerHTML += `
            <tr>
              <td>${i + 1}</td>
              <td>${n.isi_catatan}</td>
              <td>${n.semester ? 'Semester ' + n.semester : 'Tanpa Semester'}</td>
              <td>${n.jenis}</td>
              <td style="text-align:center">
                <button
                  type="button"
                  class="btn small outline"
                  onclick="return hapus(${n.id_catatan})">
                  Hapus
                </button>

              </td>
            </tr>
          `;
            });
          });
      }

      window.hapus = (id) => {
        if (!confirm('Hapus catatan ini?')) return false;

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
              loadNotes();
            } else {
              alert(res.msg || 'Data gagal dihapus');
            }
          });

        return false;
      };



      form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!input.value.trim()) return;

        const fd = new FormData();
        fd.append('aksi', 'simpan');
        fd.append('isi', input.value);
        fd.append('semester', semInput.value);
        fd.append('jenis', typeInput.value);

        fetch('catatan.php', {
            method: 'POST',
            body: fd
          })
          .then(() => {
            input.value = '';
            semInput.value = '';
            typeInput.value = 'Pribadi';
            loadNotes();
          });
      });

      filterSem.addEventListener('change', loadNotes);
      filterType.addEventListener('change', loadNotes);

      loadNotes();
    })();
  </script>

</body>

</html>