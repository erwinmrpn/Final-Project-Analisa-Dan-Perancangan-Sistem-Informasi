function formatRupiah(angka) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(angka || 0);
}

function formatTanggal(iso) {
  if (!iso) return '-';
  return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(iso.replace(' ', 'T')));
}

const STATUS_BADGE = {
  'Masuk': 'bg-blue-100 text-blue-700',
  'Proses': 'bg-yellow-100 text-yellow-700',
  'Selesai': 'bg-green-100 text-green-700'
};

function statusBadge(status) {
  return `<span class="px-2 py-1 rounded-full text-xs font-semibold ${STATUS_BADGE[status] || 'bg-slate-100 text-slate-600'}">${status}</span>`;
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
  { href: 'dashboard-karyawan.html', icon: '🏠', label: 'Dashboard' },
  { href: 'input-pesanan.html', icon: '📝', label: 'Input Pesanan' },
  { href: 'daftar-pesanan.html', icon: '📋', label: 'Daftar Pesanan' },
];

const NAV_OWNER = [
  { href: 'dashboard-owner.html', icon: '🏠', label: 'Dashboard Owner' },
  { href: 'laporan-pendapatan.html', icon: '💰', label: 'Laporan Pendapatan' },
  { href: 'laporan-kinerja.html', icon: '👥', label: 'Laporan Kinerja' },
  { href: 'kelola-pegawai.html', icon: '⚙️', label: 'Kelola Pegawai' },
];

function renderChrome({ role, title }) {
  const nav = role === 'Owner' ? NAV_OWNER : NAV_KARYAWAN;
  const current = window.location.pathname.split('/').pop();
  const navHtml = nav.map(item => {
    const isActive = item.href === current;
    return `<a href="${item.href}" class="flex items-center gap-2 px-3 py-2 rounded-lg ${isActive ? 'bg-sky-50 text-sky-700 font-medium' : 'text-slate-600 hover:bg-slate-100'}"><span>${item.icon}</span><span>${item.label}</span></a>`;
  }).join('');

  document.getElementById('sidebarSlot').innerHTML = `
    <aside id="sidebarNav" class="w-64 bg-white border-r border-slate-200 flex flex-col shrink-0 h-screen overflow-y-auto fixed md:sticky top-0 left-0 -translate-x-full md:translate-x-0 transition-transform duration-200 z-40">
      <div class="p-5 border-b border-slate-200 flex items-center justify-between">
        <div>
          <div class="font-bold text-sky-700 text-lg">🧺 Laundry Kiloan</div>
          <div class="text-xs text-slate-400">${role === 'Owner' ? 'Panel Owner' : 'Panel Karyawan'}</div>
        </div>
        <button id="btnCloseSidebar" class="md:hidden text-slate-400 hover:text-slate-600 text-xl leading-none">✕</button>
      </div>
      <nav class="flex-1 p-3 space-y-1">${navHtml}</nav>
      <a href="wa-simulation.html" target="_blank" class="m-3 text-center text-xs text-slate-500 hover:text-sky-600 border border-slate-200 rounded-lg py-2">📱 Lihat WA Pelanggan</a>
    </aside>
    <div id="sidebarOverlay" class="hidden fixed inset-0 bg-black/30 z-30 md:hidden"></div>
  `;

  document.getElementById('headerSlot').innerHTML = `
    <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 shrink-0 sticky top-0 z-20">
      <div class="flex items-center gap-3 min-w-0">
        <button id="btnOpenSidebar" class="md:hidden text-slate-500 hover:text-slate-700 text-2xl leading-none shrink-0">☰</button>
        <h1 class="font-semibold text-slate-800 truncate">${title}</h1>
      </div>
      <div class="flex items-center gap-3 shrink-0">
        <span class="text-sm text-slate-600 hidden sm:inline" id="userNama">-</span>
        <button id="logoutBtn" class="text-xs text-red-500 hover:underline whitespace-nowrap">🚪 Logout</button>
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
  setupLogout();
  return user;
}
