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
     <h2 class="logo"><img src="image/polteki.png" align="left" width="100" height="100"></h2>
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
      (function (){
  const form = document.getElementById('note-form');
  const input = document.getElementById('note-input');
  const semInput = document.getElementById('note-semester');
  const typeInput = document.getElementById('note-type');
  const filterEl = document.getElementById('filter-notes-semester');
  const filterTypeEl = document.getElementById('filter-notes-type');
        const list = document.getElementById('notes-tbody');

        const STORAGE_NOTES = 'notes';

        // load notes (backwards-compatible: strings -> objects)
        function loadNotesRaw(){
          const raw = JSON.parse(localStorage.getItem(STORAGE_NOTES) || '[]');
          // normalize to objects {text, semester, type}
          return raw.map(item => {
            if(typeof item === 'string') return { text: item, semester: '', type: 'Pribadi' };
            return { text: item.text || '', semester: item.semester || '', type: item.type || 'Pribadi' };
          });
        }

        let notes = loadNotesRaw();

        function persist(){ localStorage.setItem(STORAGE_NOTES, JSON.stringify(notes)); }

        function render(){
          list.innerHTML = '';
          const filter = filterEl ? filterEl.value : 'all';
          const filterType = filterTypeEl ? filterTypeEl.value : 'all';
          notes.forEach((n, i) => {
            if(filter !== 'all' && String(n.semester) !== filter) return;
            if(filterType !== 'all' && String(n.type) !== filterType) return;
            const tr = document.createElement('tr');
            // No (1-based)
            const noTd = document.createElement('td'); noTd.style.padding='8px'; noTd.textContent = i+1;
            const textTd = document.createElement('td'); textTd.style.padding='8px'; textTd.textContent = n.text;
            const semesterTd = document.createElement('td'); semesterTd.style.padding='8px';
            semesterTd.textContent = n.semester ? ('Semester ' + n.semester) : 'Tanpa Semester';
            const jenisTd = document.createElement('td'); jenisTd.style.padding='8px';
            jenisTd.textContent = n.type || '';
            const aksiTd = document.createElement('td'); aksiTd.style.padding='8px'; aksiTd.style.textAlign='center'; aksiTd.style.display='flex'; aksiTd.style.gap='6px'; aksiTd.style.justifyContent='center'; aksiTd.style.alignItems='center';
            // Edit button
            const editBtn = document.createElement('button'); editBtn.className='btn small'; editBtn.textContent='Edit';
            editBtn.addEventListener('click', ()=>{ startEdit(i); });
            // Delete button
            const delBtn = document.createElement('button'); delBtn.className='btn small outline'; delBtn.textContent='Hapus';
            delBtn.addEventListener('click', ()=>{ removeNote(i); });
            aksiTd.appendChild(editBtn); aksiTd.appendChild(delBtn);
            tr.appendChild(noTd); tr.appendChild(textTd); tr.appendChild(semesterTd); tr.appendChild(jenisTd); tr.appendChild(aksiTd);
            list.appendChild(tr);
          });
        }

        let editingIndex = null;
        function addNote(text, semester, type){
          if(editingIndex === null){
            notes.unshift({ text, semester: semester || '', type: type || 'Pribadi' });
          } else {
            // update existing
            notes[editingIndex].text = text;
            notes[editingIndex].semester = semester || '';
            notes[editingIndex].type = type || 'Pribadi';
            editingIndex = null;
            // restore submit button label
            const submitBtn = document.getElementById('submit-note'); if(submitBtn) submitBtn.textContent = 'Tambah';
            const cancelBtn = document.getElementById('cancel-edit'); if(cancelBtn) cancelBtn.style.display='none';
          }
          persist(); render();
          // highlight newly added/updated row briefly
          const first = list.querySelector('tr');
          if(first){
            first.classList.add('note-new');
            setTimeout(()=>{ first.classList.remove('note-new'); }, 700);
          }
        }

        function startEdit(index){
          const n = notes[index];
          if(!n) return;
          editingIndex = index;
          input.value = n.text || '';
          if(semInput) semInput.value = n.semester || '';
          if(typeInput) typeInput.value = n.type || 'Pribadi';
          const submitBtn = document.getElementById('submit-note'); if(submitBtn) submitBtn.textContent = 'Simpan';
          const cancelBtn = document.getElementById('cancel-edit'); if(cancelBtn) cancelBtn.style.display='inline-flex';
          input.focus();
        }

        function removeNote(index){
          if(!confirm('Hapus catatan ini?')) return;
          notes.splice(index,1); persist(); render();
          // if we were editing this index, cancel edit
          if(editingIndex !== null){ editingIndex = null; const submitBtn = document.getElementById('submit-note'); if(submitBtn) submitBtn.textContent='Tambah'; const cancelBtn = document.getElementById('cancel-edit'); if(cancelBtn) cancelBtn.style.display='none'; }
        }

        form.addEventListener('submit', function(e){
          e.preventDefault();
          const v = input.value.trim();
          const s = semInput ? semInput.value : '';
          const t = typeInput ? typeInput.value : 'Pribadi';
          if(!v) return;
          addNote(v, s, t);
          input.value = ''; if(semInput) semInput.value = '';
          if(typeInput) typeInput.value = 'Pribadi';
        });

        const cancelBtn = document.getElementById('cancel-edit');
        if(cancelBtn){
          cancelBtn.addEventListener('click', function(){
            editingIndex = null;
            input.value = '';
            if(semInput) semInput.value = '';
            if(typeInput) typeInput.value = 'Pribadi';
            const submitBtn = document.getElementById('submit-note'); if(submitBtn) submitBtn.textContent='Tambah';
            cancelBtn.style.display='none';
          });
        }

        if(filterEl){
          filterEl.addEventListener('change', render);
        }
        if(filterTypeEl){
          filterTypeEl.addEventListener('change', render);
        }

        render();
      })();
    </script>
  </body>
</html>