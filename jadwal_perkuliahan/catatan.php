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
  <link rel="stylesheet" href="styles.css">
</head>

<body>
  <header>
    <div class="logo">
      <img src="image/polteki.png" alt="Polteki Logo" width="60" height="60">
    </div>
    <nav class="navigation">
      <a href="index.php">Home</a>
      <a href="biodata.php">Biodata</a>
      <a href="informasi.php">Informasi</a>
      <a class="btnLogin-popup" href="login.php">Login</a>
    </nav>
  </header>

  <main class="container">
    <section class="table-responsive">
      <h2>Catatan Mahasiswa</h2>
      <div class="notes">
        <div class="controls">
          <label for="filter-notes-semester" class="filter-label">Filter:</label>
          <select id="filter-notes-semester" class="filter-select">
            <option value="all">Semua Semester</option>
            <option value="1">Semester 1</option>
            <option value="2">Semester 2</option>
            <option value="3">Semester 3</option>
            <option value="4">Semester 4</option>
            <option value="5">Semester 5</option>
            <option value="6">Semester 6</option>
            <option value="7">Semester 7</option>
            <option value="8">Semester 8</option>
          </select>
          <label for="filter-notes-type" class="filter-label" style="margin-left:8px;">Jenis:</label>
          <select id="filter-notes-type" class="filter-select">
            <option value="all">Semua Jenis</option>
            <option value="Pribadi">Pribadi</option>
            <option value="Perkuliahan">Perkuliahan</option>
            <option value="Lainnya">Lainnya</option>
          </select>
        </div>

        <form id="note-form">
          <textarea id="note-input" placeholder="Tulis catatan... (tekan Enter untuk baris baru)" aria-label="Tulis catatan"></textarea>
          <div style="display:flex; gap:8px; margin-top:8px; align-items:center;">
            <select id="note-semester">
              <option value="">Tanpa Semester</option>
              <option value="1">Semester 1</option>
              <option value="2">Semester 2</option>
              <option value="3">Semester 3</option>
              <option value="4">Semester 4</option>
              <option value="5">Semester 5</option>
              <option value="6">Semester 6</option>
              <option value="7">Semester 7</option>
              <option value="8">Semester 8</option>
            </select>
            <label for="note-type" style="margin-left:6px;">Jenis:</label>
            <select id="note-type">
              <option value="Pribadi">Pribadi</option>
              <option value="Perkuliahan">Perkuliahan</option>
              <option value="Lainnya">Lainnya</option>
            </select>
          </div>
          <div style="display:flex; gap:8px; margin-top:8px;">
            <button type="submit" id="submit-note" class="btn">Tambah</button>
            <button type="button" id="cancel-edit" class="btn outline" style="display:none;">Batal</button>
          </div>
        </form>

        <table id="notes-table" class="notes-table" style="width:100%; margin-top:16px; border-collapse:collapse;">
          <thead>
            <tr>
              <th style="width:60px; text-align:left; padding:8px;">No</th>
              <th style="text-align:left; padding:8px;">Isi Catatan</th>
              <th style="width:140px; text-align:left; padding:8px;">Semester</th>
              <th style="width:180px; text-align:left; padding:8px;">Jenis</th>
              <th style="width:140px; text-align:center; padding:8px;">Aksi</th>
            </tr>
          </thead>
          <tbody id="notes-tbody"></tbody>
        </table>
      </div>
    </section>
    <a href="informasi.php" class="btn outline back-btn" onclick="event.preventDefault(); if(history.length>1){ history.back(); } else { location.href='informasi.html'; }">← Kembali</a>
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