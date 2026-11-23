/* STOCKLY — JS nativo */

const $  = (s, r=document) => r.querySelector(s);
const $$ = (s, r=document) => Array.from(r.querySelectorAll(s));

// ---------- Toasts ----------
function toast(msg, type='') {
  const box = $('#toastContainer');
  if (!box) return;
  const el = document.createElement('div');
  el.className = 'toast ' + type;
  el.textContent = msg;
  box.appendChild(el);
  setTimeout(() => el.remove(), 5000); // 5 segundos visible
}
window.toast = toast;

// ---------- Sidebar toggle ----------
const toggle = $('#toggleSidebar');
const sidebar = $('#sidebar');
if (toggle && sidebar) {
  toggle.addEventListener('click', () => sidebar.classList.toggle('open'));
}

// ---------- Tabs dashboard ----------
$$('[data-tabs] .tab').forEach(tab => {
  tab.addEventListener('click', () => {
    const wrap = tab.closest('[data-tabs]');
    $$('.tab', wrap).forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    const key = tab.dataset.tab;
    $$('.tab-panel').forEach(p => p.classList.toggle('active', p.dataset.panel === key));
  });
});

// ---------- Modal helpers ----------
function openModal(id) { $('#'+id)?.classList.add('open'); }
function closeModals() { $$('.modal').forEach(m => m.classList.remove('open')); }
document.addEventListener('click', e => {
  if (e.target.matches('[data-close-modal]') || e.target.classList.contains('modal')) closeModals();
});

// ==================== PRODUCTOS ====================
if (document.getElementById('tablaProductos')) {
  const state = { page: 1, perPage: 10, q: '', categoria: '', sort: 'nombre', dir: 'asc', data: [] };
  const tbody = $('#tablaProductos tbody');
  const pager = $('#paginacionProductos');
  const search = $('#searchProducto');
  const filter = $('#filterCategoria');
  const form = $('#formProducto');
  const titulo = $('#modalProductoTitulo');
  let deleteTarget = null;

  async function cargar() {
    tbody.innerHTML = `<tr><td colspan="6" class="empty"><span class="spinner"></span> Cargando…</td></tr>`;
    const url = `api/productos.php?q=${encodeURIComponent(state.q)}&categoria=${state.categoria || ''}`;
    try {
      const r = await fetch(url);
      const json = await r.json();
      
      // Leemos la nueva estructura de datos
      state.data = Array.isArray(json.productos) ? json.productos : [];
      
      // Si hay productos con stock bajo, lanzamos el aviso
      if (json.alerta_stock > 0) {
          toast(`⚠️ Alerta: Hay ${json.alerta_stock} producto(s) con stock bajo.`, 'error');
      }
      
      render();
    } catch (e) { toast('Error al cargar productos','error'); }
  }

  function render() {
    // Ordenar
    state.data.sort((a, b) => {
      const va = a[state.sort] ?? ''; const vb = b[state.sort] ?? '';
      if (state.sort === 'categoria') return String(va).localeCompare(String(vb)) * (state.dir==='asc'?1:-1);
      if (typeof va === 'number') return (va - vb) * (state.dir==='asc'?1:-1);
      return String(va).localeCompare(String(vb)) * (state.dir==='asc'?1:-1);
    });

    const total = state.data.length;
    const pages = Math.max(1, Math.ceil(total / state.perPage));
    if (state.page > pages) state.page = pages;
    const start = (state.page - 1) * state.perPage;
    const slice = state.data.slice(start, start + state.perPage);

    if (!slice.length) {
      tbody.innerHTML = `<tr><td colspan="6" class="empty">Sin productos</td></tr>`;
    } else {
      tbody.innerHTML = slice.map(p => `
        <tr>
          <td>${esc(p.codigo)}</td>
          <td>${esc(p.nombre)}</td>
          <td>${esc(p.categoria || '—')}</td>
          <td>$${Number(p.precio_venta).toFixed(2)}</td>
          <td>${p.stock_actual <= p.stock_minimo
              ? `<span class="badge badge-warn">${p.stock_actual}</span>`
              : p.stock_actual}</td>
           <td>
             <button class="btn btn-sm" data-edit="${p.id}">Editar</button>
             <a class="btn btn-sm btn-danger" href="productos.php?delete=${p.id}" onclick="return confirm('¿Eliminar producto?')">Eliminar</a>
           </td>
        </tr>`).join('');
    }

    pager.innerHTML = '';
    for (let i = 1; i <= pages; i++) {
      const b = document.createElement('button');
      b.textContent = i;
      if (i === state.page) b.classList.add('active');
      b.onclick = () => { state.page = i; render(); };
      pager.appendChild(b);
    }
  }

  function esc(s) { const d = document.createElement('div'); d.textContent = s ?? ''; return d.innerHTML; }

  // Orden por header
  $$('#tablaProductos th[data-sort]').forEach(th => {
    th.addEventListener('click', () => {
      const k = th.dataset.sort;
      if (state.sort === k) state.dir = state.dir === 'asc' ? 'desc' : 'asc';
      else { state.sort = k; state.dir = 'asc'; }
      render();
    });
  });

  search?.addEventListener('input', debounce(() => { state.q = search.value.trim(); state.page = 1; cargar(); }, 300));
  filter?.addEventListener('change', () => { state.categoria = filter.value; state.page = 1; cargar(); });

  // Abrir nuevo
  $('#btnNuevoProducto').onclick = () => {
    form.reset(); form.id.value = '';
    titulo.textContent = 'Nuevo producto';
    openModal('modalProducto');
  };

  // Editar / eliminar (delegación)
  tbody.addEventListener('click', async e => {
    const btn = e.target.closest('button');
    if (!btn) return;
    if (btn.dataset.edit) {
      const p = state.data.find(x => x.id == btn.dataset.edit);
      if (!p) return;
      Object.entries(p).forEach(([k, v]) => { if (form[k]) form[k].value = v ?? ''; });
      titulo.textContent = 'Editar producto';
      openModal('modalProducto');
    }
    if (btn.dataset.del) {
      deleteTarget = btn.dataset.del;
      openModal('modalConfirm');
    }
  });

  // Eliminación vía enlace normal (manejada en servidor)

  cargar();
}

// Util debounce
function debounce(fn, ms) {
  let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); };
}