function formatRupiah(angka) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(angka || 0);
}

function formatTanggal(iso) {
  if (!iso) return '-';
  return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(iso.replace(' ', 'T')));
}

const STATUS_BADGE = {
  'Masuk': 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-200',
  'Proses': 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200',
  'Selesai': 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200'
};

function statusBadge(status) {
  return `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ${STATUS_BADGE[status] || 'bg-slate-100 text-slate-600'}">${status}</span>`;
}

// --- Icon set (inline SVG, Heroicons-style outline) -------------------------
const ICON_PATHS = {
  home: '<path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 3l9 7.5"/><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 9v10.125c0 .621.504 1.125 1.125 1.125h11.25c.621 0 1.125-.504 1.125-1.125V9"/><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 20.25v-6a1.5 1.5 0 011.5-1.5h1.5a1.5 1.5 0 011.5 1.5v6"/>',
  edit: '<path stroke-linecap="round" stroke-linejoin="round" d="M16.5 4.5l3 3L7.5 19.5H4.5v-3z"/>',
  list: '<rect x="5.25" y="4.5" width="13.5" height="16.5" rx="2" stroke-linecap="round" stroke-linejoin="round"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 3.75h6a.75.75 0 01.75.75v1.5a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v-1.5A.75.75 0 019 3.75z"/><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 11.25h7.5M8.25 14.25h7.5M8.25 17.25h4.5"/>',
  creditCard: '<rect x="2.25" y="6" width="19.5" height="13.5" rx="2.25" stroke-linecap="round" stroke-linejoin="round"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 10.5h19.5"/>',
  cash: '<rect x="2.25" y="6.75" width="19.5" height="10.5" rx="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.25" stroke-linecap="round" stroke-linejoin="round"/>',
  users: '<circle cx="9" cy="8.25" r="2.75" stroke-linecap="round" stroke-linejoin="round"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 19.5a5.25 5.25 0 0110.5 0"/><circle cx="17" cy="9" r="2" stroke-linecap="round" stroke-linejoin="round"/><path stroke-linecap="round" stroke-linejoin="round" d="M15.5 14a4.5 4.5 0 014.75 4.5"/>',
  gear: '<circle cx="12" cy="12" r="3" stroke-linecap="round" stroke-linejoin="round"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09a1.65 1.65 0 00-1-1.51 1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09a1.65 1.65 0 001.51-1 1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>',
  phone: '<rect x="7.5" y="2.25" width="9" height="19.5" rx="2" stroke-linecap="round" stroke-linejoin="round"/><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 18.75h1.5"/>',
  logout: '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12H9m12 0l-3.75-3.75M21 12l-3.75 3.75"/>',
  menu: '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/>',
  close: '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>',
  chevronDown: '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>',
  droplet: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3c-3.5 4-6 7.5-6 10.5a6 6 0 0012 0C18 10.5 15.5 7 12 3z"/>',
  checkCircle: '<circle cx="12" cy="12" r="9" stroke-linecap="round" stroke-linejoin="round"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l1.75 1.75L15.5 9.5"/>',
  eye: '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12s-3.75 7.5-9.75 7.5S2.25 12 2.25 12z"/><circle cx="12" cy="12" r="3" stroke-linecap="round" stroke-linejoin="round"/>',
  eyeSlash: '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 002.25 12s3.75 7.5 9.75 7.5c1.556 0 2.994-.336 4.264-.93M6.228 6.228A10.45 10.45 0 0112 4.5c6 0 9.75 7.5 9.75 7.5a10.5 10.5 0 01-4.293 4.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243"/>',
  lock: '<rect x="4.5" y="10.5" width="15" height="9.75" rx="2" stroke-linecap="round" stroke-linejoin="round"/><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 10.5V7.5a3.75 3.75 0 117.5 0v3"/>',
  userCircle: '<circle cx="12" cy="8.25" r="3" stroke-linecap="round" stroke-linejoin="round"/><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 19.128a9.38 9.38 0 0113.5 0A9.708 9.708 0 0112 21.75a9.708 9.708 0 01-6.75-2.622z"/><circle cx="12" cy="12" r="9" stroke-linecap="round" stroke-linejoin="round"/>',
  warning: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3h.008v.008H12v-.008zM10.29 3.86L1.82 18a1.5 1.5 0 001.3 2.25h17.76a1.5 1.5 0 001.3-2.25L13.71 3.86a1.5 1.5 0 00-2.42 0z"/>',
  arrowDown: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l6-6m-6 6l-6-6"/>',
  phoneCall: '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 4.5a1.5 1.5 0 011.5-1.5h2.25a1.5 1.5 0 011.5 1.28l.7 3.5a1.5 1.5 0 01-.4 1.35l-1.4 1.4a12.75 12.75 0 006.36 6.36l1.4-1.4a1.5 1.5 0 011.35-.4l3.5.7a1.5 1.5 0 011.28 1.5v2.25a1.5 1.5 0 01-1.5 1.5H19.5C10.04 21 3 13.96 3 4.5V4.5z"/>',
  back: '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.5L7.5 12l7.5-7.5"/>',
  refresh: '<path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>',
  plus: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>',
};

function icon(name, cls = 'w-5 h-5') {
  const inner = ICON_PATHS[name] || '';
  return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="${cls}">${inner}</svg>`;
}

async function checkAuth(requireOwner) {
  const res = await fetch('api/auth/me.php');
  const data = await res.json();
  if (!data.user) {
    window.location.href = 'login.html';
    return null;
  }
  if (requireOwner && data.user.role !== 'Owner') {
    window.location.href = 'dashboard-karyawan.html';
    return null;
  }
  return data.user;
}

function setupLogout() {
  const btn = document.getElementById('logoutBtn');
  if (!btn) return;
  btn.addEventListener('click', async () => {
    await fetch('api/auth/logout.php', { method: 'POST' });
    window.location.href = 'login.html';
  });
}

const NAV_KARYAWAN = [
  { href: 'dashboard-karyawan.html', icon: 'home', label: 'Dashboard' },
  { href: 'input-pesanan.html', icon: 'edit', label: 'Input Pesanan' },
  { href: 'daftar-pesanan.html', icon: 'list', label: 'Daftar Pesanan' },
];

const NAV_OWNER = [
  { href: 'dashboard-owner.html', icon: 'home', label: 'Dashboard Owner' },
  { href: 'laporan-pendapatan.html', icon: 'cash', label: 'Laporan Pendapatan' },
  { href: 'laporan-kinerja.html', icon: 'users', label: 'Laporan Kinerja' },
  { href: 'kelola-pegawai.html', icon: 'gear', label: 'Kelola Pegawai' },
];

function initials(nama) {
  return (nama || '').trim().split(/\s+/).slice(0, 2).map(w => w[0]).join('').toUpperCase();
}

function renderChrome({ role, title }) {
  const nav = role === 'Owner' ? NAV_OWNER : NAV_KARYAWAN;
  const current = window.location.pathname.split('/').pop();
  const navHtml = nav.map(item => {
    const isActive = item.href === current;
    return `
      <a href="${item.href}" class="group relative flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors ${isActive ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700'}">
        ${isActive ? '<span class="absolute left-0 top-1.5 bottom-1.5 w-1 rounded-full bg-brand-600"></span>' : ''}
        <span class="${isActive ? 'text-brand-600' : 'text-slate-400 group-hover:text-slate-500'}">${icon(item.icon, 'w-5 h-5')}</span>
        <span class="text-sm">${item.label}</span>
      </a>`;
  }).join('');

  document.getElementById('sidebarSlot').innerHTML = `
    <aside id="sidebarNav" class="w-72 bg-white border-r border-slate-100 flex flex-col shrink-0 h-screen overflow-y-auto fixed md:sticky top-0 left-0 -translate-x-full md:translate-x-0 transition-transform duration-200 z-40">
      <div class="p-5 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
          <span class="w-10 h-10 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white flex items-center justify-center shadow-lg shadow-brand-200">${icon('droplet', 'w-5 h-5')}</span>
          <div>
            <div class="font-bold text-slate-800 leading-tight">Laundry Kiloan</div>
            <div class="text-[11px] text-slate-400">${role === 'Owner' ? 'Panel Owner' : 'Panel Karyawan'}</div>
          </div>
        </div>
        <button id="btnCloseSidebar" class="md:hidden text-slate-400 hover:text-slate-600">${icon('close', 'w-5 h-5')}</button>
      </div>
      <nav class="flex-1 px-3.5 pt-2 space-y-1">${navHtml}</nav>
      <a href="wa-simulation.html" target="_blank" class="m-3.5 flex items-center justify-center gap-2 text-xs font-medium text-brand-700 bg-brand-50 hover:bg-brand-100 transition-colors rounded-xl py-2.5">
        ${icon('phone', 'w-4 h-4')} Lihat WA Pelanggan
      </a>
    </aside>
    <div id="sidebarOverlay" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-[1px] z-30 md:hidden"></div>
  `;

  document.getElementById('headerSlot').innerHTML = `
    <header class="h-16 bg-white/80 backdrop-blur border-b border-slate-100 flex items-center justify-between px-4 sm:px-6 shrink-0 sticky top-0 z-20">
      <div class="flex items-center gap-3 min-w-0">
        <button id="btnOpenSidebar" class="md:hidden text-slate-500 hover:text-slate-700 shrink-0">${icon('menu', 'w-6 h-6')}</button>
        <h1 class="font-semibold text-slate-800 truncate">${title}</h1>
      </div>
      <div class="flex items-center gap-3 shrink-0">
        <div class="hidden sm:flex items-center gap-2 pr-3 border-r border-slate-200">
          <span class="w-8 h-8 rounded-full bg-brand-100 text-brand-700 text-xs font-bold flex items-center justify-center" id="userAvatar">--</span>
          <span class="text-sm text-slate-600" id="userNama">-</span>
        </div>
        <button id="logoutBtn" class="flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-rose-600 transition-colors">
          ${icon('logout', 'w-4 h-4')} <span class="hidden sm:inline">Logout</span>
        </button>
      </div>
    </header>
  `;

  const sidebar = document.getElementById('sidebarNav');
  const overlay = document.getElementById('sidebarOverlay');
  const openSidebar = () => { sidebar.classList.remove('-translate-x-full'); overlay.classList.remove('hidden'); };
  const closeSidebar = () => { sidebar.classList.add('-translate-x-full'); overlay.classList.add('hidden'); };
  document.getElementById('btnOpenSidebar').addEventListener('click', openSidebar);
  document.getElementById('btnCloseSidebar').addEventListener('click', closeSidebar);
  overlay.addEventListener('click', closeSidebar);
}

async function initPage({ requireOwner = false, title = '' } = {}) {
  const user = await checkAuth(requireOwner);
  if (!user) return null;
  renderChrome({ role: user.role, title });
  document.getElementById('userNama').textContent = user.nama;
  const avatar = document.getElementById('userAvatar');
  if (avatar) avatar.textContent = initials(user.nama);
  setupLogout();
  return user;
}
