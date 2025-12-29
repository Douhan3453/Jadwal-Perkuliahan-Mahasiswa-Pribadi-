<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Informasi - Jadwal</title>
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

    <main class="container">
      <section id="jadwal-index">
        <div class="table-responsive">
          <h2 class="section-title">Informasi Mahasiswa</h2>
          <div class="cards-grid index-links">
            <a class="card card-link" href="jadwal.php">
              <div>
                <h3>Jadwal</h3>
                <p>Lihat jadwal perkuliahan.</p>
              </div>
              <span class="cta"><span class="btnLogin-popup">Buka »</span></span>
            </a>
            <a class="card card-link" href="catatan.php">
              <div>
                <h3>Catatan Mahasiswa</h3>
                <p>Tulis dan lihat catatan Anda.</p>
              </div>
              <span class="cta"><span class="btnLogin-popup">Buka »</span></span>
            </a>
          </div>
        </div>
      </section>
    </main>

    <script>
      // tab switcher
      document.addEventListener('click', function (e) {
        if (!e.target.classList.contains('tab-btn')) return;
        var target = e.target.getAttribute('data-target');
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        e.target.classList.add('active');
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        document.getElementById(target).classList.add('active');
      });

      // Notes (localStorage)
      (function (){
        const form = document.getElementById('note-form');
        const input = document.getElementById('note-input');
        const list = document.getElementById('notes-list');

        function loadNotes(){
          const notes = JSON.parse(localStorage.getItem('notes')||'[]');
          list.innerHTML = '';
          notes.forEach((n, i) => {
            const li = document.createElement('li');
            li.textContent = n;
            li.dataset.index = i;
            const btn = document.createElement('button');
            btn.textContent = 'Hapus';
            btn.className = 'btn small';
            btn.addEventListener('click', () => { removeNote(i); });
            li.appendChild(btn);
            list.appendChild(li);
          });
        }

        function saveNote(text){
          const notes = JSON.parse(localStorage.getItem('notes')||'[]');
          notes.unshift(text);
          localStorage.setItem('notes', JSON.stringify(notes));
          loadNotes();
        }

        function removeNote(index){
          const notes = JSON.parse(localStorage.getItem('notes')||'[]');
          notes.splice(index,1);
          localStorage.setItem('notes', JSON.stringify(notes));
          loadNotes();
        }

        form.addEventListener('submit', function(e){
          e.preventDefault();
          const v = input.value.trim();
          if(v) { saveNote(v); input.value = ''; }
        });

        loadNotes();
      })();

      // Simple calendar
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
            grid.appendChild(cell);
          }
        }

        prev.addEventListener('click', ()=>{ cur = new Date(cur.getFullYear(), cur.getMonth()-1,1); render(cur); });
        next.addEventListener('click', ()=>{ cur = new Date(cur.getFullYear(), cur.getMonth()+1,1); render(cur); });

        render(cur);
      })();
    </script>
  </body>
</html>
