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
  const nameEl = document.getElementById('userNama');
  if (nameEl) nameEl.textContent = data.user.nama;
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
