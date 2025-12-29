<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Kalender</title>
  <link rel="stylesheet" href="stylee.css">
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
      <h2>Kalender</h2>

      <!-- Jadwal Perkuliahan Pribadi: form tambah / edit / hapus -->
      <h3>Jadwal Perkuliahan (Pribadi)</h3>

      <form id="schedule-form" class="schedule-form" aria-label="Form tambah jadwal">
        <input type="hidden" id="sched-id">
        <label>
          Mata Kuliah:
          <input type="text" id="sched-title" placeholder="Nama mata kuliah" required>
        </label>
        <label>
          Waktu:
          <input type="text" id="sched-time" placeholder="08:00 - 10:00" required>
        </label>
        <label>
          Pilih Hari (opsional jika pakai tanggal):
          <select id="sched-weekday">
            <option value="">--Tidak berulang--</option>
            <option value="0">Minggu</option>
            <option value="1">Senin</option>
            <option value="2">Selasa</option>
            <option value="3">Rabu</option>
            <option value="4">Kamis</option>
            <option value="5">Jumat</option>
            <option value="6">Sabtu</option>
          </select>
        </label>
        <label>
          Atau Tanggal (YYYY-MM-DD):
          <input type="date" id="sched-date">
        </label>
        <label>
          Ruang:
          <input type="text" id="sched-room" placeholder="Ruang">
        </label>
        <button type="submit" class="btn">Simpan</button>
        <button type="button" id="sched-cancel" class="btn outline">Batal</button>
      </form>

      <table class="schedule-table" id="schedule-table" aria-label="Jadwal Perkuliahan">
        <caption>Jadwal perkuliahan mahasiswa (sesuaikan sesuai data pribadi)</caption>
        <thead>
          <tr>
            <th>Hari</th>
            <th>Waktu</th>
            <th>Mata Kuliah</th>
            <th>Ruang</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>

      <div class="calendar">
        <div class="calendar-header">
          <button id="prev-month" class="btn">‹</button>
          <label for="month-select" style="display:none">Pilih Bulan</label>
          <select id="month-select" aria-label="Pilih Bulan" style="min-width:120px;">
            <option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">April</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
          </select>
          <h3 id="month-year">Month Year</h3>
          <button id="next-month" class="btn">›</button>
        </div>
        <div id="calendar-grid" class="calendar-grid"></div>
      </div>
    </section> <a href="informasi.html" class="btn outline back-btn" onclick="event.preventDefault(); if(history.length>1){ history.back(); } else { location.href='informasi.html'; }">← Kembali</a>



    
  </main>



  <script>
    const grid = document.getElementById('calendar-grid');
    const monthYear = document.getElementById('month-year');
    const prev = document.getElementById('prev-month');
    const next = document.getElementById('next-month');
    const monthSelect = document.getElementById('month-select');

    const tbody = document.querySelector('#schedule-table tbody');
    const form = document.getElementById('schedule-form');

    let cur = new Date();
    let jadwalData = [];

    /* ================== LOAD DATA ================== */
    function loadJadwal() {
      fetch('jadwal_api.php?aksi=ambil')
        .then(r => r.json())
        .then(data => {
          jadwalData = data;
          renderTable();
          renderCalendar(cur);
        });
    }

    /* ================== TABEL ================== */
    function renderTable() {
      tbody.innerHTML = '';
      jadwalData.forEach(j => {
        tbody.innerHTML += `
        <tr>
          <td>${j.tanggal ?? j.hari}</td>
          <td>${j.jam}</td>
          <td>${j.mata_kuliah}</td>
          <td>${j.ruang}</td>
          <td>
            <button class="btn small outline" onclick="hapus(${j.id})">Hapus</button>
          </td>
        </tr>
      `;
      });
    }

    /* ================== KALENDER ================== */
    function renderCalendar(date) {
      grid.innerHTML = '';

      const year = date.getFullYear();
      const month = date.getMonth();

      monthYear.textContent = date.toLocaleString('id-ID', {
        month: 'long',
        year: 'numeric'
      });

      monthSelect.value = month + 1;

      const firstDay = new Date(year, month, 1).getDay();
      const lastDate = new Date(year, month + 1, 0).getDate();

      const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
      days.forEach(d => {
        const el = document.createElement('div');
        el.className = 'cal-cell head';
        el.textContent = d;
        grid.appendChild(el);
      });

      for (let i = 0; i < firstDay; i++) {
        const empty = document.createElement('div');
        empty.className = 'cal-cell empty';
        grid.appendChild(empty);
      }

      for (let d = 1; d <= lastDate; d++) {
        const cell = document.createElement('div');
        cell.className = 'cal-cell day';
        cell.textContent = d;

        const tanggalStr =
          `${year}-${String(month + 1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;

        const adaJadwal = jadwalData.some(j => j.tanggal === tanggalStr);

        if (adaJadwal) {
          const dot = document.createElement('span');
          dot.className = 'event-dot';
          cell.appendChild(dot);
        }

        grid.appendChild(cell);
      }
    }

    /* ================== NAVIGASI ================== */
    prev.onclick = () => {
      cur = new Date(cur.getFullYear(), cur.getMonth() - 1, 1);
      renderCalendar(cur);
    };

    next.onclick = () => {
      cur = new Date(cur.getFullYear(), cur.getMonth() + 1, 1);
      renderCalendar(cur);
    };

    monthSelect.onchange = e => {
      cur = new Date(cur.getFullYear(), e.target.value - 1, 1);
      renderCalendar(cur);
    };

    /* ================== FORM ================== */
    form.addEventListener('submit', e => {
      e.preventDefault();

      const fd = new FormData();
      fd.append('aksi', 'simpan');
      fd.append('tanggal', document.getElementById('sched-date').value);
      fd.append('hari', document.getElementById('sched-weekday').selectedOptions[0].text);
      fd.append('jam', document.getElementById('sched-time').value);
      fd.append('mata_kuliah', document.getElementById('sched-title').value);
      fd.append('semester', 1);
      fd.append('ruang', document.getElementById('sched-room').value);

      fetch('jadwal_api.php', {
          method: 'POST',
          body: fd
        })
        .then(() => {
          form.reset();
          loadJadwal();
        });
    });

    function hapus(id) {
      if (!confirm('Hapus jadwal ini?')) return;
      const fd = new FormData();
      fd.append('aksi', 'hapus');
      fd.append('id', id);

      fetch('jadwal_api.php', {
          method: 'POST',
          body: fd
        })
        .then(() => loadJadwal());
    }

    loadJadwal();
  </script>


</body>

</html>
<script src="transition.js"></script>