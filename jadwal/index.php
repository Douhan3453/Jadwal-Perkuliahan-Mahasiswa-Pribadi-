<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
?>


<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Perkuliahan Mahasiswa</title>
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

        <main class="main-center">

            <div class="table-responsive" style="margin-top:30px; text-align:center;">
                <h2 class="title-main">JADWAL PERKULIAHAN MAHASISWA</h2>
                <p class="subtitle">Kalender Akademik</p>


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

        // Tambahkan referensi modal (Pastikan ID ini ada di HTML Anda)
        const modal = document.getElementById('modal-detail');
        const modalBody = document.getElementById('modal-body');

        let cur = new Date();
        let jadwalData = [];

        /* ================= 1. AMBIL DATA DARI DATABASE ================= */
        function loadJadwal() {
            fetch('jadwal_api.php?aksi=ambil')
                .then(r => r.json())
                .then(data => {
                    jadwalData = data;
                    render(cur);
                })
                .catch(err => console.error('Gagal ambil jadwal:', err));
        }

        /* ================= 2. RENDER KALENDER & PENANDA ================= */
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

                const isoDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                cell.dataset.date = isoDate;

                // FILTER JADWAL BERDASARKAN TANGGAL INI
                const jadwalHariIni = jadwalData.filter(j => j.tanggal === isoDate);

                if (jadwalHariIni.length > 0) {
                    // Tambahkan titik penanda
                    const dot = document.createElement('span');
                    dot.className = 'marker';
                    cell.appendChild(dot);

                    // BIAR BISA DIKLIK
                    cell.style.cursor = 'pointer';
                    cell.onclick = () => {
                        showPopUp(isoDate, jadwalHariIni);
                    };
                }

                grid.appendChild(cell);
            }
        }

        /* ================= 3. FUNGSI POP-UP DETAIL ================= */
        function showPopUp(tanggal, list) {
            // Jika Anda belum membuat Modal HTML, alert sederhana bisa digunakan dulu:
            let info = `Jadwal Tanggal: ${tanggal}\n\n`;
            list.forEach((j, i) => {
                info += `${i+1}. ${j.mata_kuliah}\n   Jam: ${j.jam}\n   Ruang: ${j.ruang}\n\n`;
            });
            
            // Tampilkan Detail
            alert(info); 
            
            // Jika Anda sudah punya elemen modal-detail, gunakan ini:
            /*
            modalBody.innerHTML = list.map(j => `
                <div class="jadwal-item">
                    <strong>${j.mata_kuliah}</strong><br>
                    Jam: ${j.jam} | Ruang: ${j.ruang}
                </div>
            `).join('<hr>');
            modal.style.display = 'flex';
            */
        }

        /* ================= 4. NAVIGASI & INIT ================= */
        prev.addEventListener('click', () => {
            cur = new Date(cur.getFullYear(), cur.getMonth() - 1, 1);
            render(cur);
        });

        next.addEventListener('click', () => {
            cur = new Date(cur.getFullYear(), cur.getMonth() + 1, 1);
            render(cur);
        });

        loadJadwal();
    })();
</script>

        
    <div id="modal-detail" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detail Jadwal</h3>
            <span class="close-modal" id="close-modal">&times;</span>
        </div>
        <div id="modal-body">
            </div>
    </div>
</div>

</body>

</html>