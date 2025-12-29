<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kalender</title>
    <link rel="stylesheet" href="styles.css">
  </head>
  <body>
    <header>
      <h2 class="logo"><img src="image/polteki.png" align="left" width="100" height="100"></h2>
      <nav class="navigation">
        <a href="index.html">Home</a>
        <a href="biodata.html">Biodata</a>
        <a href="informasi.html">Informasi</a>
        <a class="btnLogin-popup" href="login.html">Logout</a>
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
          <label>
            Dosen:
            <input type="text" id="sched-lecturer" placeholder="Nama dosen">
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
              <th>Dosen</th>
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
      (function(){
        const grid = document.getElementById('calendar-grid');
        const monthYear = document.getElementById('month-year');
        const prev = document.getElementById('prev-month');
        const next = document.getElementById('next-month');
        let cur = new Date();
        const monthSelect = document.getElementById('month-select');

        // schedule elements
        const schedForm = document.getElementById('schedule-form');
        const scheduleTableBody = document.querySelector('#schedule-table tbody');

        const LS_KEY = 'schedules_v1';
        const JADWAL_KEY = 'jadwalEntries'; // from jadwal.html

        function mapHariToWeekday(h){
          if(!h) return '';
          const m = String(h).toLowerCase();
          if(m.includes('minggu')||m==='minggu') return 0;
          if(m.includes('senin')||m==='senin') return 1;
          if(m.includes('selasa')||m==='selasa') return 2;
          if(m.includes('rabu')||m==='rabu') return 3;
          if(m.includes('kamis')||m==='kamis') return 4;
          if(m.includes('jumat')||m==='jumat' || m.includes('jum\u00a4t')) return 5;
          if(m.includes('sabtu')||m==='sabtu') return 6;
          return '';
        }

        // load schedules from either kalender's own key or from jadwal.html key and normalize
        function loadSchedules(){
          let out = [];
          try{
            const a = JSON.parse(localStorage.getItem(LS_KEY)) || [];
            if(Array.isArray(a)) out = out.concat(a);
          }catch(e){}
          try{
            const b = JSON.parse(localStorage.getItem(JADWAL_KEY)) || [];
            if(Array.isArray(b)){
              // map jadwalEntries format to kalender format
              const mapped = b.map((it, i)=>{
                  return {
                  id: it.id || (`jadwal-${i}-${it.tanggal||it.jam||i}`),
                  title: it.matkul || it.title || '',
                  time: it.jam || it.time || '',
                  // weekday: map day name to numeric weekday if provided
                    weekday: (it.hari? mapHariToWeekday(it.hari) : (it.weekday!==undefined? it.weekday : '')),
                  // date stored as YYYY-MM-DD in jadwal.html 'tanggal'
                  date: it.tanggal || it.date || '',
                    bulan: it.bulan || '',
                  room: it.ruang || it.room || '',
                  lecturer: it.dosen || it.lecturer || '' ,
                  // keep original raw for reference
                  _source: 'jadwalEntries'
                };
              });
              out = out.concat(mapped);
            }
          }catch(e){}
          return out;
        }

        function saveSchedules(list){ localStorage.setItem(LS_KEY, JSON.stringify(list)); }

        // render calendar grid and inject schedule markers
        function render(d){
          grid.innerHTML = '';
          const year = d.getFullYear();
          const month = d.getMonth();
          monthYear.textContent = d.toLocaleString(undefined,{month:'long', year:'numeric'});
          if(monthSelect) monthSelect.value = String(month+1);

          const first = new Date(year,month,1);
          const last = new Date(year, month+1, 0).getDate();
          const startDay = first.getDay();

          ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].forEach(w => {
            const hd = document.createElement('div'); hd.className='cal-cell head'; hd.textContent = w; grid.appendChild(hd);
          });

          for(let i=0;i<startDay;i++){ const empty = document.createElement('div'); empty.className='cal-cell empty'; grid.appendChild(empty); }

          const schedules = loadSchedules();

          for(let day=1; day<=last; day++){
            const cell = document.createElement('div');
            cell.className='cal-cell day';
            cell.textContent = day;
            // attach data attributes
            cell.dataset.day = day;
            cell.dataset.weekday = new Date(year, month, day).getDay();

            // find date-specific events
            const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
            const events = schedules.filter(s => {
              try{
                // date match exact
                if(s.date && s.date === dateStr) return true;
                // weekday recurring match; if event has bulan specified, ensure month matches
                if(s.weekday!=='' && Number(s.weekday) === Number(cell.dataset.weekday)){
                  if(s.bulan){ return Number(s.bulan) === (month+1); }
                  return true;
                }
                return false;
              }catch(e){ return false; }
            });
            if(events.length){
              const list = document.createElement('div'); list.className='cell-events';
              events.slice(0,3).forEach(ev => {
                const dot = document.createElement('span'); dot.className='event-dot'; dot.title = `${ev.title} (${ev.time})`;
                list.appendChild(dot);
              });
              if(events.length>3){ const more = document.createElement('span'); more.className='event-more'; more.textContent = `+${events.length-3}`; list.appendChild(more); }
              cell.appendChild(list);
            }

            grid.appendChild(cell);
          }
        }

        // render schedule table (list view)
        function renderScheduleTable(){
          const schedules = loadSchedules();
          scheduleTableBody.innerHTML = '';
          if(schedules.length===0){
            const tr = document.createElement('tr');
            const td = document.createElement('td'); td.colSpan = 5; td.textContent = 'Belum ada jadwal. Tambahkan menggunakan formulir di atas.'; tr.appendChild(td); scheduleTableBody.appendChild(tr); return;
          }
          schedules.forEach(s => {
            const tr = document.createElement('tr');
            const dayLabel = s.date ? new Date(s.date).toLocaleDateString() : (s.weekday!==''? ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'][s.weekday] : '-');
            tr.innerHTML = `
              <td data-label="Hari">${dayLabel}</td>
              <td data-label="Waktu">${s.time || ''}</td>
              <td data-label="Mata Kuliah">${s.title || ''}</td>
              <td data-label="Ruang">${s.room || ''}</td>
              <td data-label="Dosen">${s.lecturer || ''} <button class=\"btn small outline\" data-action=\"edit\" data-id=\"${s.id}\">Edit</button> <button class=\"btn small outline\" data-action=\"del\" data-id=\"${s.id}\">Hapus</button></td>
            `;
            scheduleTableBody.appendChild(tr);
          });
        }

        // form handling
        function resetForm(){
          schedForm.reset(); document.getElementById('sched-id').value = '';
        }

        schedForm.addEventListener('submit', function(e){
          e.preventDefault();
          const id = document.getElementById('sched-id').value || String(Date.now());
          const title = document.getElementById('sched-title').value.trim();
          const time = document.getElementById('sched-time').value.trim();
          const weekday = document.getElementById('sched-weekday').value;
          const date = document.getElementById('sched-date').value || '';
          const room = document.getElementById('sched-room').value.trim();
          const lecturer = document.getElementById('sched-lecturer').value.trim();

          let list = loadSchedules();
          const existingIndex = list.findIndex(x=>x.id===id);
          const entry = { id, title, time, weekday, date, room, lecturer };
          if(existingIndex>=0) list[existingIndex]=entry; else list.push(entry);
          saveSchedules(list);
          resetForm(); renderScheduleTable(); render(cur);
        });

        document.getElementById('sched-cancel').addEventListener('click', ()=>{ resetForm(); });

        // delegate edit/delete buttons
        scheduleTableBody.addEventListener('click', (e)=>{
          const btn = e.target.closest('button'); if(!btn) return;
          const action = btn.dataset.action; const id = btn.dataset.id;
          let list = loadSchedules();
          if(action==='del'){
            if(confirm('Hapus jadwal ini?')){ list = list.filter(s=>s.id!==id); saveSchedules(list); renderScheduleTable(); render(cur); }
          } else if(action==='edit'){
            const s = list.find(x=>x.id===id); if(!s) return;
            document.getElementById('sched-id').value = s.id;
            document.getElementById('sched-title').value = s.title || '';
            document.getElementById('sched-time').value = s.time || '';
            document.getElementById('sched-weekday').value = s.weekday!==undefined? s.weekday : '';
            document.getElementById('sched-date').value = s.date || '';
            document.getElementById('sched-room').value = s.room || '';
            document.getElementById('sched-lecturer').value = s.lecturer || '';
            window.scrollTo({ top: 0, behavior: 'smooth' });
          }
        });

        prev.addEventListener('click', ()=>{ cur = new Date(cur.getFullYear(), cur.getMonth()-1,1); render(cur); });
        next.addEventListener('click', ()=>{ cur = new Date(cur.getFullYear(), cur.getMonth()+1,1); render(cur); });

        if(monthSelect){
          monthSelect.value = String(cur.getMonth()+1);
          monthSelect.addEventListener('change', (e)=>{
            const m = parseInt(e.target.value,10);
            if(!isNaN(m)){
              cur = new Date(cur.getFullYear(), m-1, 1);
              render(cur);
            }
          });
        }

        // initial render
        renderScheduleTable(); render(cur);

        // if another page (jadwal.html) updates localStorage, update calendar live
        window.addEventListener('storage', (e)=>{
          if(e.key===JADWAL_KEY || e.key===LS_KEY){
            renderScheduleTable(); render(cur);
          }
        });
      })();
    </script>
  </body>
</html>
<script src="transition.js"></script>
