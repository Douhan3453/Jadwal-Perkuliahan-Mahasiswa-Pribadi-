<?php
$conn = new mysqli("localhost", "root", "", "jadwalmahasiswa");
if ($conn->connect_error) die("DB Error");

/* SIMPAN */
if ($_POST['aksi'] ?? '' === 'simpan') {
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
  $stmt->execute();
  echo json_encode(['status' => 'ok']);
  exit;
}

/* AMBIL */
if ($_GET['aksi'] ?? '' === 'ambil') {
  $sql = "SELECT * FROM jadwal";
  if ($_GET['semester'] !== 'all') {
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
if ($_POST['aksi'] ?? '' === 'hapus') {
  $conn->query("TRUNCATE jadwal");
  echo json_encode(['status' => 'ok']);
  exit;
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Jadwal</title>
  <link rel="stylesheet" href="styles.css">
</head>

<body>
  <header>
    <div class="logo">
      <img src="image/polteki.png" width="60">
    </div>
    <nav class="navigation">
      <a href="index.php">Home</a>
      <a href="biodata.php">Biodata</a>
      <a href="informasi.php">Informasi</a>
      <a class="btnLogin-popup" href="login.html">Logout</a>
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
            <input
              type="text"
              id="matkul"
              name="matkul"
              placeholder="Mata Kuliah"
              required>
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
            <input
              type="text"
              id="ruang"
              name="ruang"
              placeholder="Ruang"
              required>
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
            <?php for ($i = 1; $i <= 8; $i++): ?>
              <option value="<?= $i ?>">Semester <?= $i ?></option>
            <?php endfor; ?>
          </select>

          <button id="clear-all" class="btn outline danger">
            Hapus Semua
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
          </tr>
        </thead>
        <tbody id="tbody"></tbody>
      </table>

    </section>

  </main>

  <script>
    const tbody = document.getElementById('tbody');
    const filter = document.getElementById('filter-semester');

    /* LOAD DATA + FILTER */
    function loadData() {
      fetch(`jadwal.php?aksi=ambil&semester=${filter.value}`)
        .then(r => r.json())
        .then(data => {
          tbody.innerHTML = '';
          data.forEach(d => {
            tbody.innerHTML += `
          <tr>
            <td>${d.tanggal ?? '-'}</td>
            <td>${d.hari}</td>
            <td>${d.jam}</td>
            <td>${d.mata_kuliah}</td>
            <td>Semester ${d.semester}</td>
            <td>${d.ruang}</td>
          </tr>`;
          });
        });
    }
    loadData();

    /* FILTER EVENT */
    filter.addEventListener('change', loadData);

    /* SIMPAN */
    document.getElementById('schedule-form').onsubmit = e => {
      e.preventDefault();
      const fd = new FormData(e.target);
      fd.append('aksi', 'simpan');
      fetch('jadwal.php', {
          method: 'POST',
          body: fd
        })
        .then(() => {
          e.target.reset();
          loadData();
        });
    };

    /* HAPUS SEMUA */
    document.getElementById('clear-all').onclick = () => {
      if (confirm('Hapus semua jadwal?'))
        fetch('jadwal.php', {
          method: 'POST',
          body: new URLSearchParams({
            aksi: 'hapus'
          })
        })
        .then(loadData);
    };
  </script>
</body>

</html>