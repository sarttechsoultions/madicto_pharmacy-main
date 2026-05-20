/* ══════════════════════
   DATA
══════════════════════ */
let medicines = [
  { id: 1, name: 'Amoxicillin 500mg', sku: 'SKU: AMU-00124-B', cat: 'Antibiotics', price: 24.50, stock: 450, status: 'In Stock', icon: '💊', rx: true },
  { id: 2, name: 'Ibuprofen 200mg', sku: 'SKU: IBU-99210-C', cat: 'Pain Relief', price: 12.99, stock: 12, status: 'Low Stock', icon: '🩺', rx: false },
  { id: 3, name: 'Atorvastatin 20mg', sku: 'SKU: ATR-88211-A', cat: 'Cardiovascular', price: 56.00, stock: 0, status: 'Out of Stock', icon: '💉', rx: true },
  { id: 4, name: 'Lisinopril 10mg', sku: 'SKU: LIS-55219-F', cat: 'Cardiovascular', price: 18.25, stock: 82, status: 'In Stock', icon: '🧬', rx: true },
  { id: 5, name: 'Metformin 500mg', sku: 'SKU: MET-33201-D', cat: 'Diabetes', price: 8.75, stock: 320, status: 'In Stock', icon: '💊', rx: true },
  { id: 6, name: 'Cetirizine 10mg', sku: 'SKU: CET-22110-G', cat: 'Allergy', price: 6.50, stock: 8, status: 'Low Stock', icon: '🌿', rx: false },
  { id: 7, name: 'Omeprazole 20mg', sku: 'SKU: OMP-44322-H', cat: 'Gastric', price: 14.00, stock: 195, status: 'In Stock', icon: '💊', rx: false },
  { id: 8, name: 'Paracetamol 500mg', sku: 'SKU: PAR-11100-A', cat: 'Pain Relief', price: 4.25, stock: 0, status: 'Out of Stock', icon: '🩹', rx: false },
  { id: 9, name: 'Azithromycin 250mg', sku: 'SKU: AZI-77831-C', cat: 'Antibiotics', price: 32.00, stock: 60, status: 'In Stock', icon: '💊', rx: true },
  { id: 10, name: 'Vitamin D3 1000IU', sku: 'SKU: VTD-90201-B', cat: 'Vitamins', price: 11.99, stock: 5, status: 'Low Stock', icon: '🌞', rx: false },
];
let nextId = 11;
let drafts = [];
let nextDraftId = 1;
let filtered = [...medicines];
let currentPage = 1;
const perPage = 10;
let editingId = null;
let deletingId = null;
let editingDraftId = null; // when modal is editing a draft

/* ══════════════════════
   HELPERS
══════════════════════ */
function statusClass(s) {
  if (s === 'In Stock') return 'in-stock';
  if (s === 'Low Stock') return 'low-stock';
  if (s === 'Out of Stock') return 'out-stock';
  return 'draft-badge';
}
function stockClass(stock, status) {
  if (status === 'Low Stock') return 'stock-val low';
  if (status === 'Out of Stock') return 'stock-val out';
  return 'stock-val';
}
function determineStatus(stock) {
  if (stock <= 0) return 'Out of Stock';
  if (stock <= 15) return 'Low Stock';
  return 'In Stock';
}
function generateSKU(name) {
  const prefix = name.replace(/\s+/g, '').substring(0, 3).toUpperCase();
  const num = String(Math.floor(Math.random() * 90000) + 10000);
  const suf = String.fromCharCode(65 + Math.floor(Math.random() * 6));
  return `SKU: ${prefix}-${num}-${suf}`;
}

/* ══════════════════════
   TABLE RENDER
══════════════════════ */
function renderTable() {
  const tbody = document.getElementById('tableBody');
  const start = (currentPage - 1) * perPage;
  const rows = filtered.slice(start, start + perPage);

  tbody.innerHTML = rows.length ? rows.map(m => `
    <tr>
      <td><div class="med-cell">
        <div class="med-thumb">${m.icon || '💊'}</div>
        <div><div class="med-name">${m.name}</div><div class="med-sku">${m.sku}</div></div>
      </div></td>
      <td>${m.cat}</td>
      <td>$${m.price.toFixed(2)}</td>
      <td><span class="${stockClass(m.stock, m.status)}">${m.stock} units</span></td>
      <td><span class="badge ${statusClass(m.status)}">${m.status}</span></td>
      <td><div class="actions-cell">
        <button class="action-btn edit" title="Edit" onclick="openEditModal(${m.id})"><i class="fa-solid fa-pen"></i></button>
        <button class="action-btn del"  title="Delete" onclick="openConfirm(${m.id})"><i class="fa-solid fa-trash"></i></button>
      </div></td>
    </tr>`).join('') :
    `<tr><td colspan="6" style="text-align:center;padding:40px;color:var(--muted);">No medicines match your filters.</td></tr>`;

  const total = filtered.length;
  document.getElementById('tableInfo').textContent =
    total ? `Showing ${start + 1}–${Math.min(start + perPage, total)} of ${total} medicines` : `No results`;

  renderPagination();
}

function renderPagination() {
  const total = filtered.length;
  const pages = Math.ceil(total / perPage);
  const el = document.getElementById('paginationEl');
  if (pages <= 1) { el.innerHTML = ''; return; }

  let html = `<button class="pg-btn" onclick="prevPage()"><i class="fa-solid fa-chevron-left"></i></button>`;
  for (let i = 1; i <= Math.min(pages, 5); i++) {
    html += `<button class="pg-btn ${i === currentPage ? 'active' : ''}" onclick="goPage(${i})">${i}</button>`;
  }
  if (pages > 5) html += `<button class="pg-btn">…</button><button class="pg-btn" onclick="goPage(${pages})">${pages}</button>`;
  html += `<button class="pg-btn" onclick="nextPage()"><i class="fa-solid fa-chevron-right"></i></button>`;
  el.innerHTML = html;
}

function filterTable() {
  const q = document.getElementById('filterInput').value.toLowerCase();
  const cat = document.getElementById('catFilter').value;
  const st = document.getElementById('statusFilter').value;
  const srt = document.getElementById('sortFilter').value;

  filtered = medicines.filter(m =>
    (!q || m.name.toLowerCase().includes(q) || m.sku.toLowerCase().includes(q)) &&
    (cat === 'All Categories' || m.cat === cat) &&
    (st === 'All Status' || m.status === st)
  );

  if (srt === 'Name (A-Z)') filtered.sort((a, b) => a.name.localeCompare(b.name));
  if (srt === 'Name (Z-A)') filtered.sort((a, b) => b.name.localeCompare(a.name));
  if (srt === 'Price ↑') filtered.sort((a, b) => a.price - b.price);
  if (srt === 'Price ↓') filtered.sort((a, b) => b.price - a.price);
  if (srt === 'Stock ↑') filtered.sort((a, b) => a.stock - b.stock);
  if (srt === 'Stock ↓') filtered.sort((a, b) => b.stock - a.stock);

  currentPage = 1; renderTable();
}

function filterByStatus(st) {
  document.getElementById('statusFilter').value = st;
  filterTable();
}

function resetFilters() {
  document.getElementById('filterInput').value = '';
  document.getElementById('catFilter').value = 'All Categories';
  document.getElementById('statusFilter').value = 'All Status';
  document.getElementById('sortFilter').value = 'Name (A-Z)';
  filterTable();
}

function globalSearch(val) {
  document.getElementById('filterInput').value = val;
  filterTable();
}

function prevPage() { if (currentPage > 1) { currentPage--; renderTable(); } }
function nextPage() { const max = Math.ceil(filtered.length / perPage); if (currentPage < max) { currentPage++; renderTable(); } }
function goPage(p) { currentPage = p; renderTable(); }

function updateStats() {
  document.getElementById('statLow').textContent = medicines.filter(m => m.status === 'Low Stock').length;
  document.getElementById('statIn').textContent = medicines.filter(m => m.status === 'In Stock').length;
  document.getElementById('statOut').textContent = medicines.filter(m => m.status === 'Out of Stock').length;
  document.getElementById('statTotal').textContent = medicines.length.toLocaleString();
}

/* ══════════════════════
   SIDEBAR TOGGLE
══════════════════════ */
const sidebar = document.getElementById('sidebar');
const mainWrap = document.getElementById('mainWrap');
const overlay = document.getElementById('overlay');
const toggleIcon = document.getElementById('toggleIcon');
const closeBtn = document.getElementById('sidebarCloseBtn');
let isCollapsed = false;
const isMobile = () => window.innerWidth <= 768;

function toggleSidebar() {
  if (isMobile()) {
    const open = sidebar.classList.toggle('mobile-open');
    overlay.classList.toggle('active', open);
  } else {
    isCollapsed = !isCollapsed;
    sidebar.classList.toggle('collapsed', isCollapsed);
    mainWrap.classList.toggle('sidebar-collapsed', isCollapsed);
    toggleIcon.className = isCollapsed ? 'fa-solid fa-bars-staggered' : 'fa-solid fa-bars';
  }
}

document.getElementById('sidebarToggle').addEventListener('click', toggleSidebar);

// Sidebar close/expand button (inside sidebar)
closeBtn.addEventListener('click', () => {
  if (isMobile()) {
    sidebar.classList.remove('mobile-open');
    overlay.classList.remove('active');
  } else {
    toggleSidebar();
  }
});

overlay.addEventListener('click', () => {
  sidebar.classList.remove('mobile-open');
  overlay.classList.remove('active');
});

window.addEventListener('resize', () => {
  if (!isMobile()) {
    sidebar.classList.remove('mobile-open');
    overlay.classList.remove('active');
  }
});

/* ══════════════════════
   TOAST
══════════════════════ */
function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.style.opacity = '1';
  t.style.transform = 'translateX(-50%) translateY(0)';
  clearTimeout(t._timer);
  t._timer = setTimeout(() => {
    t.style.opacity = '0';
    t.style.transform = 'translateX(-50%) translateY(20px)';
  }, 2600);
}

/* ══════════════════════
   ADD MEDICINE MODAL
══════════════════════ */
function openModal(draft = null) {
  editingDraftId = null;
  clearForm();

  if (draft) {
    // Load draft data
    editingDraftId = draft.id;
    document.getElementById('f-name').value = draft.name || '';
    document.getElementById('f-cat').value = draft.cat || '';
    document.getElementById('f-mfg').value = draft.mfg || '';
    document.getElementById('f-batch').value = draft.batch || '';
    document.getElementById('f-desc').value = draft.desc || '';
    document.getElementById('f-usage').value = draft.usage || '';
    document.getElementById('f-price').value = draft.price || '';
    document.getElementById('f-discount').value = draft.discount || '';
    document.getElementById('f-stock').value = draft.stock || '';
    document.getElementById('f-reorder').value = draft.reorder || '';
    document.getElementById('f-unit').value = draft.unit || 'Tablets';
    document.getElementById('f-pack').value = draft.pack || '';
    document.getElementById('f-expiry').value = draft.expiry || '';
    document.getElementById('f-mfgdate').value = draft.mfgdate || '';
    document.getElementById('f-rx').checked = !!draft.rx;
    document.getElementById('f-controlled').checked = !!draft.controlled;

    document.getElementById('modalBreadcrumb').textContent = 'Edit Draft';
    document.getElementById('modalTitle').textContent = 'Edit Draft Entry';
    document.getElementById('modalSubtitle').textContent = `Editing draft: ${draft.name || 'Untitled'}`;
    document.getElementById('publishBtnText').textContent = 'Publish Medicine';
  } else {
    document.getElementById('modalBreadcrumb').textContent = 'Add New Medicine';
    document.getElementById('modalTitle').textContent = 'Inventory Entry';
    document.getElementById('modalSubtitle').textContent = 'Register a new pharmaceutical product into the central management system.';
    document.getElementById('publishBtnText').textContent = 'Publish Medicine';
  }

  document.getElementById('addMedicineModal').classList.add('open');
  document.body.style.overflow = 'hidden';
  document.getElementById('thumb1').innerHTML = '';
  document.getElementById('thumb2').innerHTML = '';
}

function closeModal() {
  document.getElementById('addMedicineModal').classList.remove('open');
  document.body.style.overflow = '';
  editingDraftId = null;
}

function clearForm() {
  ['f-name', 'f-mfg', 'f-batch', 'f-desc', 'f-usage', 'f-price', 'f-discount',
    'f-stock', 'f-reorder', 'f-pack', 'f-expiry', 'f-mfgdate'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.value = '';
    });
  document.getElementById('f-cat').value = '';
  document.getElementById('f-unit').value = 'Tablets';
  document.getElementById('f-rx').checked = true;
  document.getElementById('f-controlled').checked = false;
}

function getFormData() {
  return {
    name: document.getElementById('f-name').value.trim(),
    cat: document.getElementById('f-cat').value,
    mfg: document.getElementById('f-mfg').value.trim(),
    batch: document.getElementById('f-batch').value.trim(),
    desc: document.getElementById('f-desc').value.trim(),
    usage: document.getElementById('f-usage').value.trim(),
    price: parseFloat(document.getElementById('f-price').value) || 0,
    discount: parseFloat(document.getElementById('f-discount').value) || 0,
    stock: parseInt(document.getElementById('f-stock').value) || 0,
    reorder: parseInt(document.getElementById('f-reorder').value) || 20,
    unit: document.getElementById('f-unit').value,
    pack: document.getElementById('f-pack').value.trim(),
    expiry: document.getElementById('f-expiry').value,
    mfgdate: document.getElementById('f-mfgdate').value,
    rx: document.getElementById('f-rx').checked,
    controlled: document.getElementById('f-controlled').checked,
  };
}

function validateForm(data) {
  if (!data.name) { showToast('⚠️ Medicine name is required'); document.getElementById('f-name').focus(); return false; }
  if (!data.cat) { showToast('⚠️ Please select a category'); document.getElementById('f-cat').focus(); return false; }
  if (!data.price) { showToast('⚠️ Please enter a base price'); document.getElementById('f-price').focus(); return false; }
  if (!data.expiry) { showToast('⚠️ Please set an expiry date'); document.getElementById('f-expiry').focus(); return false; }
  return true;
}

function saveAsDraft() {
  const data = getFormData();
  if (!data.name) { showToast('⚠️ At least add a medicine name to save draft'); return; }

  const draftData = { ...data, savedAt: new Date().toLocaleString() };

  if (editingDraftId !== null) {
    // Update existing draft
    const idx = drafts.findIndex(d => d.id === editingDraftId);
    if (idx !== -1) { drafts[idx] = { ...draftData, id: editingDraftId }; }
    showToast('📝 Draft updated!');
  } else {
    draftData.id = nextDraftId++;
    drafts.push(draftData);
    showToast('📝 Saved as draft!');
  }

  updateDraftBadge();
  closeModal();
}

function publishMedicine() {
  const data = getFormData();
  if (!validateForm(data)) return;

  const newMed = {
    id: nextId++,
    name: data.name,
    sku: generateSKU(data.name),
    cat: data.cat,
    price: data.price,
    stock: data.stock,
    status: determineStatus(data.stock),
    icon: '💊',
    rx: data.rx,
  };

  medicines.unshift(newMed);

  // Remove draft if editing one
  if (editingDraftId !== null) {
    drafts = drafts.filter(d => d.id !== editingDraftId);
    updateDraftBadge();
    renderDraftPanel();
  }

  filtered = [...medicines];
  filterTable();
  updateStats();
  closeModal();
  showToast(`✅ ${data.name} published to inventory!`);
}

function previewImage(e) {
  const file = e.target.files[0];
  if (!file) return;
  const url = URL.createObjectURL(file);
  const t1 = document.getElementById('thumb1');
  const t2 = document.getElementById('thumb2');
  if (!t1.innerHTML) { t1.innerHTML = `<img src="${url}" alt="preview"/>`; }
  else { t2.innerHTML = `<img src="${url}" alt="preview"/>`; }
}

/* ══════════════════════
   DRAFT PANEL
══════════════════════ */
function openDraftPanel() {
  renderDraftPanel();
  document.getElementById('draftPanel').classList.add('open');
}

function closeDraftPanel() {
  document.getElementById('draftPanel').classList.remove('open');
}

function renderDraftPanel() {
  const body = document.getElementById('draftPanelBody');
  const empty = document.getElementById('draftEmpty');
  const sub = document.getElementById('draftSubtitle');

  sub.textContent = drafts.length === 0 ? '0 drafts saved'
    : `${drafts.length} draft${drafts.length > 1 ? 's' : ''} saved`;

  if (drafts.length === 0) {
    empty.style.display = 'flex';
    // Remove any existing items
    body.querySelectorAll('.draft-item').forEach(el => el.remove());
    return;
  }
  empty.style.display = 'none';
  // Clear old items
  body.querySelectorAll('.draft-item').forEach(el => el.remove());

  drafts.forEach(d => {
    const el = document.createElement('div');
    el.className = 'draft-item';
    el.innerHTML = `
      <div class="draft-item-header">
        <div>
          <div class="draft-item-name">${d.name || 'Untitled Draft'}</div>
          <div class="draft-item-cat">${d.cat || 'No category'}</div>
        </div>
        <button class="draft-item-del" onclick="deleteDraft(${d.id},event)" title="Delete draft">
          <i class="fa-solid fa-trash"></i>
        </button>
      </div>
      <div class="draft-item-meta">
        <i class="fa-regular fa-clock"></i> ${d.savedAt}
        ${d.price ? `<span>• $${Number(d.price).toFixed(2)}</span>` : ''}
        ${d.stock !== undefined ? `<span>• ${d.stock} units</span>` : ''}
      </div>`;
    el.addEventListener('click', (e) => {
      if (e.target.closest('.draft-item-del')) return;
      closeDraftPanel();
      openModal(d);
    });
    body.appendChild(el);
  });
}

function deleteDraft(id, e) {
  e.stopPropagation();
  drafts = drafts.filter(d => d.id !== id);
  updateDraftBadge();
  renderDraftPanel();
  showToast('🗑️ Draft deleted');
}

function updateDraftBadge() {
  document.getElementById('draftCountBadge').textContent = drafts.length;
  renderDraftPanel();
}

/* ══════════════════════
   EDIT MODAL
══════════════════════ */
function openEditModal(id, name, category, price, stock, status) {

  document.getElementById('e-name').value = name;
  document.getElementById('e-cat').value = category;
  document.getElementById('e-price').value = price;
  document.getElementById('e-stock').value = stock;
  document.getElementById('e-status').value = status;

  // Dynamic update route
  document.getElementById('editForm').action =
    `/admin/medicine/${id}`;

  document.getElementById('editModal').classList.add('open');
}

function closeEditModal() {

  document.getElementById('editModal').classList.remove('open');
}


/* ══════════════════════
   DELETE CONFIRM
══════════════════════ */
function openConfirm(id, name) {

  document.getElementById('confirmMsg').textContent =
    `"${name}" will be permanently removed from inventory. This action cannot be undone.`;

  // Dynamic delete route
  document.getElementById('deleteForm').action =
    `/admin/medicine/${id}`;

  document.getElementById('confirmModal').classList.add('open');
}

function closeConfirm() {

  document.getElementById('confirmModal').classList.remove('open');
}


/* ══════════════════════
   KEYBOARD SHORTCUTS
══════════════════════ */
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    if (document.getElementById('confirmModal').classList.contains('open')) { closeConfirm(); return; }
    if (document.getElementById('editModal').classList.contains('open')) { closeEditModal(); return; }
    if (document.getElementById('addMedicineModal').classList.contains('open')) { closeModal(); return; }
    if (document.getElementById('draftPanel').classList.contains('open')) { closeDraftPanel(); return; }
  }
});

/* ══════════════════════
   INIT
══════════════════════ */
renderTable();
updateStats();
updateDraftBadge();
setTimeout(() => { document.getElementById('progressFill').style.width = '98%'; }, 600);









/* ════════════════════════
   DATA
════════════════════════ */
const CATE_ICONS = ['fa-pills', 'fa-heart-pulse', 'fa-brain', 'fa-shield-virus', 'fa-hand-holding-medical',
  'fa-syringe', 'fa-eye', 'fa-tooth', 'fa-lungs', 'fa-bone', 'fa-dna', 'fa-microscope',
  'fa-baby', 'fa-person-cane', 'fa-sun', 'fa-leaf'];

const CATE_DEMANDS = { 'HIGH DEMAND': 'cate-tag-high', 'STABLE': 'cate-tag-stable', 'LOW STOCK': 'cate-tag-low', 'GROWING': 'cate-tag-growing' };

let cateCategories = [
  { id: 1, name: 'Antibiotics', desc: 'Drugs that inhibit the growth of or destroy microorganisms.', meds: 412, demand: 'HIGH DEMAND', icon: 'fa-pills', published: true },
  { id: 2, name: 'Cardiovascular', desc: 'Medications for heart conditions and blood pressure regulation.', meds: 256, demand: 'STABLE', icon: 'fa-heart-pulse', published: true },
  { id: 3, name: 'Neurology', desc: 'Treatments for nervous system disorders and mental health.', meds: 189, demand: 'STABLE', icon: 'fa-brain', published: true },
  { id: 4, name: 'Antivirals', desc: 'Specific medications for viral infections and seasonal flu.', meds: 94, demand: 'LOW STOCK', icon: 'fa-shield-virus', published: true },
  { id: 5, name: 'Pain Management', desc: 'Analgesics and inflammatory relief medications for chronic care.', meds: 320, demand: 'STABLE', icon: 'fa-hand-holding-medical', published: true },
  { id: 6, name: 'Pediatrics', desc: 'Safe and effective dosages specifically formulated for children.', meds: 211, demand: 'GROWING', icon: 'fa-baby', published: true },
];
let cateNextId = 7;
let cateEditId = null;
let cateDeleteId = null;
let cateSelectedIcon = 'fa-pills';
let cateView = 'grid';

const cateActivity = [
  { name: 'Antibiotics', time: 'Oct 24, 2023 • 09:45 AM', admin: 'John Doe', initials: 'JD', color: '#d4155b', status: 'pub' },
  { name: 'Cardiovascular', time: 'Oct 23, 2023 • 02:12 PM', admin: 'Sarah Smith', initials: 'SS', color: '#3b82f6', status: 'pub' },
  { name: 'Draft Category', time: 'Oct 22, 2023 • 11:30 AM', admin: 'Mark Adams', initials: 'MA', color: '#8b90a7', status: 'draft' },
];

/* ════════════════════════
   RENDER CARDS
════════════════════════ */
function cateGetFiltered() {
  const q = document.getElementById('cateFilterInput').value.toLowerCase();
  const dm = document.getElementById('cateDemandFilter').value;
  const st = document.getElementById('cateSortSelect').value;
  let arr = cateCategories.filter(c =>
    (!q || c.name.toLowerCase().includes(q) || c.desc.toLowerCase().includes(q)) &&
    (!dm || c.demand === dm)
  );
  if (st === 'name-az') arr.sort((a, b) => a.name.localeCompare(b.name));
  if (st === 'name-za') arr.sort((a, b) => b.name.localeCompare(a.name));
  if (st === 'meds-desc') arr.sort((a, b) => b.meds - a.meds);
  if (st === 'meds-asc') arr.sort((a, b) => a.meds - b.meds);
  return arr;
}

function cateRenderCards() {
  const grid = document.getElementById('cateGrid');
  const arr = cateGetFiltered();
  grid.classList.toggle('cate-list-view', cateView === 'list');

  if (!arr.length) {
    grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:50px;color:var(--cate-muted);">
      <i class="fa-solid fa-folder-open" style="font-size:2.5rem;margin-bottom:10px;display:block;color:#dde1ed;"></i>
      No categories match your filters.</div>`;
    return;
  }

  grid.innerHTML = arr.map(c => {
    const tagClass = CATE_DEMANDS[c.demand] || 'cate-tag-stable';
    if (cateView === 'grid') {
      return `<div class="cate-card" onclick="cateCardClick(event,${c.id})">
        <div class="cate-card-top">
          <div class="cate-card-icon"><i class="fa-solid ${c.icon}"></i></div>
          <div class="cate-card-actions">
            <button class="cate-card-action-btn cate-edit" title="Edit" onclick="cateOpenEdit(${c.id},event)"><i class="fa-solid fa-pen"></i></button>
            <button class="cate-card-action-btn cate-del"  title="Delete" onclick="cateOpenConfirm(${c.id},event)"><i class="fa-solid fa-trash"></i></button>
          </div>
        </div>
        <div class="cate-card-name">${c.name}</div>
        <div class="cate-card-desc">${c.desc}</div>
        <div class="cate-card-divider"></div>
        <div class="cate-card-footer">
          <div class="cate-card-count">${c.meds.toLocaleString()} Medicines</div>
          <span class="cate-demand-tag ${tagClass}">${c.demand}</span>
        </div>
      </div>`;
    } else {
      return `<div class="cate-card" onclick="cateCardClick(event,${c.id})">
        <div class="cate-card-top">
          <div class="cate-card-icon"><i class="fa-solid ${c.icon}"></i></div>
        </div>
        <div class="cate-card-body">
          <div class="cate-card-name">${c.name}</div>
          <div class="cate-card-desc">${c.desc}</div>
        </div>
        <div class="cate-card-footer">
          <div class="cate-card-count">${c.meds.toLocaleString()} Medicines</div>
          <span class="cate-demand-tag ${tagClass}">${c.demand}</span>
          <div class="cate-card-actions">
            <button class="cate-card-action-btn cate-edit" title="Edit" onclick="cateOpenEdit(${c.id},event)"><i class="fa-solid fa-pen"></i></button>
            <button class="cate-card-action-btn cate-del"  title="Delete" onclick="cateOpenConfirm(${c.id},event)"><i class="fa-solid fa-trash"></i></button>
          </div>
        </div>
      </div>`;
    }
  }).join('');

  // Update stats
  document.getElementById('cateTotalCount').textContent = cateCategories.length;
  const highDemand = cateCategories.reduce((a, c) => a + (c.demand === 'HIGH DEMAND' ? 1 : 0), 0);
  const topCat = [...cateCategories].sort((a, b) => b.meds - a.meds)[0];
  if (topCat) document.getElementById('cateTopPerf').textContent = topCat.name;
}

function cateCardClick(e, id) {
  if (e.target.closest('.cate-card-actions')) return;
  cateOpenEdit(id, e);
}

function cateFilterCards() { cateRenderCards(); }
function cateSearchCards(v) { document.getElementById('cateFilterInput').value = v; cateFilterCards(); }

function cateSetView(v) {
  cateView = v;
  document.getElementById('cateGridViewBtn').classList.toggle('cate-view-active', v === 'grid');
  document.getElementById('cateListViewBtn').classList.toggle('cate-view-active', v === 'list');
  cateRenderCards();
}

/* ════════════════════════
   RENDER ACTIVITY
════════════════════════ */
function cateRenderActivity() {
  document.getElementById('cateActivityBody').innerHTML = cateActivity.map(a => `
    <tr>
      <td><span class="cate-activity-name">${a.name}</span></td>
      <td><span class="cate-activity-time">${a.time}</span></td>
      <td><div class="cate-admin-pill">
        <div class="cate-admin-avatar" style="background:${a.color};">${a.initials}</div>
        ${a.admin}
      </div></td>
      <td><span class="cate-status-${a.status}">${a.status === 'pub' ? 'Published' : 'Draft'}</span></td>
    </tr>`).join('');
}

/* ════════════════════════
   ICON PICKER
════════════════════════ */
function cateRenderIconPicker() {
  document.getElementById('cateIconGrid').innerHTML = CATE_ICONS.map(ic => `
    <div class="cate-icon-opt ${ic === cateSelectedIcon ? 'cate-icon-selected' : ''}" onclick="cateSelectIcon('${ic}')" title="${ic}">
      <i class="fa-solid ${ic}"></i>
    </div>`).join('');
}
function cateSelectIcon(ic) {
  cateSelectedIcon = ic;
  cateRenderIconPicker();
}

/* ════════════════════════
   MODAL OPEN/CLOSE
════════════════════════ */
function cateOpenModal() {
  cateEditId = null;
  cateClearForm();
  document.getElementById('cateModalTitle').textContent = 'Add New Category';
  document.getElementById('cateModalSubtitle').textContent = 'Register a new therapeutic drug category';
  document.getElementById('cateModalPublishText').textContent = 'Publish Category';
  cateRenderIconPicker();
  document.getElementById('cateModal').classList.add('cate-modal-open');
  document.body.style.overflow = 'hidden';
}

function cateOpenEdit(id, e) {
  if (e) e.stopPropagation();
  const cat = cateCategories.find(c => c.id === id);
  if (!cat) return;
  cateEditId = id;
  document.getElementById('cf-name').value = cat.name;
  document.getElementById('cf-desc').value = cat.desc;
  document.getElementById('cf-meds').value = cat.meds;
  document.getElementById('cf-demand').value = cat.demand;
  document.getElementById('cf-publish').checked = cat.published;
  cateSelectedIcon = cat.icon;
  document.getElementById('cateModalTitle').textContent = `Edit: ${cat.name}`;
  document.getElementById('cateModalSubtitle').textContent = 'Update category details';
  document.getElementById('cateModalPublishText').textContent = 'Save Changes';
  cateRenderIconPicker();
  document.getElementById('cateModal').classList.add('cate-modal-open');
  document.body.style.overflow = 'hidden';
}

function cateCloseModal() {
  document.getElementById('cateModal').classList.remove('cate-modal-open');
  document.body.style.overflow = '';
  cateEditId = null;
}

function cateClearForm() {
  ['cf-name', 'cf-desc', 'cf-meds'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
  document.getElementById('cf-demand').value = 'STABLE';
  document.getElementById('cf-publish').checked = true;
  document.getElementById('cf-rx').checked = false;
  document.getElementById('cf-featured').checked = false;
  cateSelectedIcon = 'fa-pills';
}

/* ════════════════════════
   PUBLISH / SAVE DRAFT
════════════════════════ */
function cateGetFormData() {
  return {
    name: document.getElementById('cf-name').value.trim(),
    desc: document.getElementById('cf-desc').value.trim(),
    meds: parseInt(document.getElementById('cf-meds').value) || 0,
    demand: document.getElementById('cf-demand').value,
    published: document.getElementById('cf-publish').checked,
    rx: document.getElementById('cf-rx').checked,
    featured: document.getElementById('cf-featured').checked,
    icon: cateSelectedIcon,
  };
}

function catePublishCategory() {
  const data = cateGetFormData();
  if (!data.name) { cateToast('⚠️ Category name is required'); document.getElementById('cf-name').focus(); return; }

  if (cateEditId !== null) {
    const idx = cateCategories.findIndex(c => c.id === cateEditId);
    if (idx !== -1) cateCategories[idx] = { ...cateCategories[idx], ...data };
    cateToast(`✅ "${data.name}" updated!`);
    // Update activity
    cateActivity.unshift({ name: data.name, time: new Date().toLocaleString(), admin: 'Admin', initials: 'SA', color: '#d4155b', status: 'pub' });
    if (cateActivity.length > 5) cateActivity.pop();
  } else {
    cateCategories.unshift({ id: cateNextId++, ...data });
    cateToast(`✅ "${data.name}" published!`);
    cateActivity.unshift({ name: data.name, time: new Date().toLocaleString(), admin: 'Admin', initials: 'SA', color: '#d4155b', status: 'pub' });
    if (cateActivity.length > 5) cateActivity.pop();
  }

  cateRenderCards();
  cateRenderActivity();
  cateCloseModal();
}

function cateSaveDraft() {
  const data = cateGetFormData();
  if (!data.name) { cateToast('⚠️ Add a name to save draft'); return; }
  cateToast(`📝 Draft "${data.name}" saved!`);
  cateActivity.unshift({ name: data.name + ' (Draft)', time: new Date().toLocaleString(), admin: 'Admin', initials: 'SA', color: '#8b90a7', status: 'draft' });
  if (cateActivity.length > 5) cateActivity.pop();
  cateRenderActivity();
  cateCloseModal();
}

/* ════════════════════════
   DELETE
════════════════════════ */
function cateOpenConfirm(id, e) {
  if (e) e.stopPropagation();
  const cat = cateCategories.find(c => c.id === id);
  if (!cat) return;
  cateDeleteId = id;
  document.getElementById('cateConfirmMsg').textContent = `"${cat.name}" and all its associations will be permanently removed. This cannot be undone.`;
  document.getElementById('cateConfirmModal').classList.add('cate-modal-open');
}

function cateCloseConfirm() {
  document.getElementById('cateConfirmModal').classList.remove('cate-modal-open');
  cateDeleteId = null;
}

function cateDoDelete() {
  const cat = cateCategories.find(c => c.id === cateDeleteId);
  cateCategories = cateCategories.filter(c => c.id !== cateDeleteId);
  cateToast(`🗑️ "${cat?.name}" deleted`);
  cateRenderCards();
  cateCloseConfirm();
}

/* ════════════════════════
   SIDEBAR TOGGLE
════════════════════════ */
const cateSidebar = document.getElementById('cateSidebar');
const cateMainWrap = document.getElementById('cateMainWrap');
const cateOverlay = document.getElementById('cateOverlay');
let cateCollapsed = false;
const cateMobile = () => window.innerWidth <= 768;

function cateToggleSidebar() {
  if (cateMobile()) {
    const open = cateSidebar.classList.toggle('cate-mobile-open');
    cateOverlay.classList.toggle('cate-overlay-active', open);
    cateOverlay.style.display = 'block';
    cateOverlay.style.pointerEvents = open ? 'all' : 'none';
  } else {
    cateCollapsed = !cateCollapsed;
    cateSidebar.classList.toggle('cate-collapsed', cateCollapsed);
    cateMainWrap.classList.toggle('cate-sb-collapsed', cateCollapsed);
    document.getElementById('cateToggleIcon').className = cateCollapsed ? 'fa-solid fa-bars-staggered' : 'fa-solid fa-bars';
  }
}

document.getElementById('cateToggleBtn').addEventListener('click', cateToggleSidebar);
document.getElementById('cateSbCloseBtn').addEventListener('click', cateToggleSidebar);
cateOverlay.addEventListener('click', () => {
  cateSidebar.classList.remove('cate-mobile-open');
  cateOverlay.classList.remove('cate-overlay-active');
  cateOverlay.style.pointerEvents = 'none';
});

/* ════════════════════════
   KEYBOARD
════════════════════════ */
document.addEventListener('keydown', e => {
  if (e.key !== 'Escape') return;
  if (document.getElementById('cateConfirmModal').classList.contains('cate-modal-open')) { cateCloseConfirm(); return; }
  if (document.getElementById('cateModal').classList.contains('cate-modal-open')) { cateCloseModal(); return; }
});

/* ════════════════════════
   TOAST
════════════════════════ */
function cateToast(msg) {
  const t = document.getElementById('cate-toast');
  t.textContent = msg;
  t.style.opacity = '1';
  t.style.transform = 'translateX(-50%) translateY(0)';
  clearTimeout(t._timer);
  t._timer = setTimeout(() => {
    t.style.opacity = '0';
    t.style.transform = 'translateX(-50%) translateY(20px)';
  }, 2600);
}

/* ════════════════════════
   INIT
════════════════════════ */
cateRenderCards();
cateRenderActivity();

