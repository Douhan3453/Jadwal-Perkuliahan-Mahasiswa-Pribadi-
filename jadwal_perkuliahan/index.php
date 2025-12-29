<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Perkuliahan Mahasiswa</title>
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
    <main>
        <div class="table-responsive" style="margin-top:30px; text-align:center;">
            <h2 style="font-size:1.5rem; letter-spacing:1px; margin-bottom:20px;">Kalender Akademik</h2>
            <!-- Judul tambahan di atas tabel kalender -->
            <h2 style="margin-top:18px; margin-bottom:8px; text-align:center;">JADWAL PERKULIAHAN MAHASISWA</h2>

            <div class="calendar">
                <div class="calendar-header">
                    <button id="prev-month" class="btn">‹</button>
                    <h3 id="month-year">Month Year</h3>
                    <button id="next-month" class="btn">›</button>
                </div>
                <div id="calendar-grid" class="calendar-grid"></div>
            </div>
        </div>
    </main>

    <script>
        (function() {
            const grid = document.getElementById('calendar-grid');
            const monthYear = document.getElementById('month-year');
            const prev = document.getElementById('prev-month');
            const next = document.getElementById('next-month');

            let cur = new Date();
            let jadwalData = [];

            /* ================= AMBIL DATA DB ================= */
            function loadJadwal() {
                fetch('jadwal_api.php?aksi=ambil')
                    .then(r => r.json())
                    .then(data => {
                        jadwalData = data;
                        render(cur);
                    })
                    .catch(err => console.error('Gagal ambil jadwal:', err));
            }

            /* ================= RENDER KALENDER ================= */
            function render(d) {
                grid.innerHTML = '';

                const year = d.getFullYear();
                const month = d.getMonth();

                monthYear.textContent = d.toLocaleString('id-ID', {
                    month: 'long',
                    year: 'numeric'
                });

                const firstDay = new Date(year, month, 1).getDay();
                const lastDate = new Date(year, month + 1, 0).getDate();

                // Header hari
                ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'].forEach(h => {
                    const hd = document.createElement('div');
                    hd.className = 'cal-cell head';
                    hd.textContent = h;
                    grid.appendChild(hd);
                });

                // Sel kosong
                for (let i = 0; i < firstDay; i++) {
                    const empty = document.createElement('div');
                    empty.className = 'cal-cell empty';
                    grid.appendChild(empty);
                }

                // Tanggal
                for (let day = 1; day <= lastDate; day++) {
                    const cell = document.createElement('div');
                    cell.className = 'cal-cell day';
                    cell.textContent = day;

                    const isoDate =
                        `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

                    cell.dataset.date = isoDate;

                    // CEK ADA JADWAL DARI DATABASE
                    const adaJadwal = jadwalData.some(j => j.tanggal === isoDate);

                    if (adaJadwal) {
                        const dot = document.createElement('span');
                        dot.className = 'marker';
                        dot.title = 'Ada jadwal';
                        cell.appendChild(dot);
                    }

                    grid.appendChild(cell);
                }
            }

            /* ================= NAVIGASI ================= */
            prev.addEventListener('click', () => {
                cur = new Date(cur.getFullYear(), cur.getMonth() - 1, 1);
                render(cur);
            });

            next.addEventListener('click', () => {
                cur = new Date(cur.getFullYear(), cur.getMonth() + 1, 1);
                render(cur);
            });

            /* ================= INIT ================= */
            loadJadwal();
        })();
    </script>

</body>

</html>