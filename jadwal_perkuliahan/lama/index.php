
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
                <a class="btnLogin-popup" href="login.php">Logout</a>
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
            (function(){
                const grid = document.getElementById('calendar-grid');
                const monthYear = document.getElementById('month-year');
                const prev = document.getElementById('prev-month');
                const next = document.getElementById('next-month');
                let cur = new Date();

                function render(d){
                    grid.innerHTML = '';
                    const year = d.getFullYear();
                    const month = d.getMonth();
                    monthYear.textContent = d.toLocaleString(undefined,{month:'long', year:'numeric'});

                    const first = new Date(year,month,1);
                    const last = new Date(year, month+1, 0).getDate();
                    const startDay = first.getDay();

                    // weekday headers
                    ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].forEach(w => {
                        const hd = document.createElement('div'); hd.className='cal-cell head'; hd.textContent = w; grid.appendChild(hd);
                    });

                    for(let i=0;i<startDay;i++){ const empty = document.createElement('div'); empty.className='cal-cell empty'; grid.appendChild(empty); }

                    for(let day=1; day<=last; day++){
                            const cell = document.createElement('div');
                            cell.className='cal-cell day';
                            cell.textContent = day;
                            // store ISO date on the cell for easy matching (YYYY-MM-DD)
                            const iso = `${year}-${String(month+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
                            cell.dataset.date = iso;
                            grid.appendChild(cell);
                        }

                        // after building grid, add markers from stored jadwalEntries
                        addCalendarMarkers(year, month);
                }

                    function addCalendarMarkers(year, month){
                        // remove existing markers
                        document.querySelectorAll('.cal-cell.day .marker').forEach(m => m.remove());
                        let entries = [];
                        try{ entries = JSON.parse(localStorage.getItem('jadwalEntries') || '[]'); }catch(e){ entries = []; }
                        if(!entries.length) return;

                        // helper to get weekday index from nama hari
                        const hariMap = { 'Minggu':0,'Senin':1,'Selasa':2,'Rabu':3,'Kamis':4,'Jumat':5,'Sabtu':6,
                                          'Sunday':0,'Monday':1,'Tuesday':2,'Wednesday':3,'Thursday':4,'Friday':5,'Saturday':6 };

                        entries.forEach(en => {
                            if(en.tanggal){
                                // en.tanggal should be in YYYY-MM-DD format
                                const cell = document.querySelector(`.cal-cell.day[data-date="${en.tanggal}"]`);
                                if(cell) appendMarker(cell);
                            } else if(en.hari){
                                // mark all days in the current month matching the weekday
                                const target = hariMap[en.hari] ?? hariMap[en.hari.charAt(0).toUpperCase()+en.hari.slice(1)];
                                if(target === undefined) return;
                                // iterate day cells
                                document.querySelectorAll('.cal-cell.day').forEach(cell => {
                                    const parts = cell.dataset.date.split('-');
                                    const d = new Date(Number(parts[0]), Number(parts[1])-1, Number(parts[2]));
                                    if(d.getFullYear()===year && d.getMonth()===month && d.getDay()===target){
                                        appendMarker(cell);
                                    }
                                });
                            }
                        });
                    }

                    function appendMarker(cell){
                        // avoid duplicate markers
                        if(cell.querySelector('.marker')) return;
                        const m = document.createElement('span');
                        m.className = 'marker';
                        m.title = 'Ada jadwal';
                        cell.appendChild(m);
                    }

                prev.addEventListener('click', ()=>{ cur = new Date(cur.getFullYear(), cur.getMonth()-1,1); render(cur); });
                next.addEventListener('click', ()=>{ cur = new Date(cur.getFullYear(), cur.getMonth()+1,1); render(cur); });

                render(cur);

                // update markers when storage changes (e.g., schedule added on another tab)
                window.addEventListener('storage', (ev)=>{
                    if(ev.key === 'jadwalEntries') render(cur);
                });

                // also refresh when the page regains focus
                window.addEventListener('focus', ()=>{ render(cur); });
            })();
        </script>
    </body>
</html>