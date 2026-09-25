/*
 * SIPPM - app.js
 * Peningkatan tampilan <select> menjadi dropdown custom (.csel), diporting
 * langsung dari mockup supaya interaksi & tampilannya identik.
 */

function enhanceSelect(wrapId){
  const wrap = document.getElementById(wrapId);
  if(!wrap) return;
  const select = wrap.querySelector('select');
  if(!select || wrap.dataset.enhanced) return;
  wrap.dataset.enhanced = '1';

  const trigger = document.createElement('button');
  trigger.type = 'button';
  trigger.className = 'csel-trigger';
  trigger.innerHTML = '<span class="csel-label"></span><svg class="csel-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>';

  const panel = document.createElement('div');
  panel.className = 'csel-panel';

  wrap.appendChild(trigger);
  wrap.appendChild(panel);

  trigger.addEventListener('click', (e) => {
    e.stopPropagation();
    document.querySelectorAll('.csel.is-open').forEach(o => { if(o !== wrap) o.classList.remove('is-open'); });
    wrap.classList.toggle('is-open');
  });

  buildSelectPanel(wrap);
}

function buildSelectPanel(wrap){
  const select = wrap.querySelector('select');
  const panel = wrap.querySelector('.csel-panel');
  if(!select || !panel) return;
  panel.innerHTML = '';

  Array.from(select.options).forEach(opt => {
    if(opt.disabled && opt.value === '') return;
    const item = document.createElement('div');
    item.className = 'csel-option' + (opt.selected ? ' is-selected' : '');
    item.textContent = opt.textContent;
    item.dataset.value = opt.value;
    item.addEventListener('click', () => {
      select.value = opt.value;
      select.dispatchEvent(new Event('change'));
      syncSelectLabel(wrap);
      wrap.classList.remove('is-open');
    });
    panel.appendChild(item);
  });

  syncSelectLabel(wrap);
}

function refreshEnhancedSelect(wrapId){
  const wrap = document.getElementById(wrapId);
  if(!wrap) return;
  buildSelectPanel(wrap);
}

function syncSelectLabel(wrap){
  const select = wrap.querySelector('select');
  const label = wrap.querySelector('.csel-label');
  if(!select || !label) return;
  const current = select.options[select.selectedIndex];
  label.textContent = current ? current.textContent : '';
  label.classList.toggle('is-placeholder', !!current && current.value === '' && current.disabled);
  wrap.querySelectorAll('.csel-option').forEach(o => {
    o.classList.toggle('is-selected', current && o.dataset.value === current.value);
  });
}

document.addEventListener('click', () => {
  document.querySelectorAll('.csel.is-open').forEach(o => o.classList.remove('is-open'));
});

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.csel').forEach(wrap => enhanceSelect(wrap.id));
});

/* Toast notifikasi singkat */
let toastTimer = null;
function toast(message){
  let el = document.getElementById('appToast');
  if(!el){
    el = document.createElement('div');
    el.id = 'appToast';
    el.className = 'toast';
    document.body.appendChild(el);
  }
  el.textContent = message;
  el.classList.add('show');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(()=>el.classList.remove('show'), 2200);
}

/* Kotak unggah foto (.upload-box) — pratinjau gambar sebelum diunggah */
function handleUploadBoxChange(input){
  const file = input.files && input.files[0];
  const box = input.closest('.upload-box');
  if(!file || !box) return;
  if(!file.type.startsWith('image/')){
    toast('File harus berupa gambar (JPG/PNG)');
    input.value = '';
    return;
  }
  const reader = new FileReader();
  reader.onload = e=>{
    box.classList.add('has-file');
    let img = box.querySelector('img');
    if(!img){
      img = document.createElement('img');
      box.insertBefore(img, box.firstChild);
    }
    img.src = e.target.result;
    let nameEl = box.querySelector('.upload-filename');
    if(!nameEl){
      nameEl = document.createElement('span');
      nameEl.className = 'upload-filename';
      box.appendChild(nameEl);
    }
    nameEl.textContent = file.name;
  };
  reader.readAsDataURL(file);
  toast('Foto "' + file.name + '" berhasil ditambahkan');
}

function clearUploadBox(btn){
  const box = btn.closest('.upload-box');
  if(!box) return;
  box.classList.remove('has-file');
  const img = box.querySelector('img'); if(img) img.remove();
  const nameEl = box.querySelector('.upload-filename'); if(nameEl) nameEl.remove();
  const input = box.querySelector('input[type="file"]'); if(input) input.value = '';
}
