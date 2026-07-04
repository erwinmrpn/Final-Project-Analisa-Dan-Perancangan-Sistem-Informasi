# Laundry Kiloan — UI/UX Prototype (PHP Native + MySQL)

Prototype interaktif Sistem Informasi Laundry Kiloan. Terdiri dari UI Sistem (Karyawan/Owner)
dan UI Simulasi WhatsApp (sudut pandang pelanggan).

## Tech Stack
- Frontend: HTML + Tailwind CSS (CDN) + Vanilla JavaScript
- Backend: PHP native (tanpa framework)
- Database: MySQL

## Cara Menjalankan (XAMPP / Laragon / php built-in server)

### 1. Siapkan Database
Buka phpMyAdmin, buat query baru, lalu copy-paste isi file `sql/init.sql` dan jalankan.
Atau via terminal:
```
mysql -u root -p < sql/init.sql
```

### 2. Sesuaikan koneksi database
Edit `config/db.php` jika user/password MySQL Anda berbeda dari default (`root` / tanpa password).

### 3A. Jalankan dengan XAMPP/Laragon
Salin folder `laundry-uiux` ke folder `htdocs` (XAMPP) atau `www` (Laragon), lalu buka:
- Sistem Laundry : http://localhost/laundry-uiux/login.html
- Simulasi WA    : http://localhost/laundry-uiux/wa-simulation.html

### 3B. Jalankan dengan PHP built-in server
Dari dalam folder `laundry-uiux`:
```
php -S localhost:8000
```
Lalu buka:
- Sistem Laundry : http://localhost:8000/login.html
- Simulasi WA    : http://localhost:8000/wa-simulation.html

## Akun Login
| Username | Password    | Role     |
|----------|-------------|----------|
| owner    | password123 | Owner    |
| budi     | password123 | Karyawan |
| siti     | password123 | Karyawan |

## Cara Test End-to-End
1. Login sebagai karyawan (budi/siti)
2. Input Pesanan → cari/daftarkan pelanggan → isi form → pilih "Bayar Nanti"
3. Buka tab baru → wa-simulation.html → pilih nomor HP pelanggan → lihat nota WA masuk
4. Kembali ke sistem → Daftar Pesanan → Update Status → Selesai
5. Cek WA simulation → notifikasi cucian selesai muncul
6. Daftar Pesanan → Proses Bayar → isi form bayar
7. Cek WA simulation → struk pembayaran muncul
8. Login sebagai owner → lihat dashboard, laporan pendapatan, laporan kinerja, kelola pegawai

## Struktur Folder
```
laundry-uiux/
├── config/db.php          Koneksi MySQL (mysqli)
├── includes/functions.php Helper: harga layanan, generate ID, format pesan WA, auth guard
├── api/                   Endpoint backend (dipanggil via fetch dari halaman HTML)
│   ├── auth/               login, logout, me
│   ├── pelanggan/           search, create, list
│   ├── pesanan/             create, list, detail, update_status
│   ├── pembayaran/          create, status
│   ├── pegawai/             list, create, update, toggle
│   ├── laporan/             pendapatan, kinerja, simpan_pendapatan, simpan_kinerja
│   ├── wa/                  inbox, contacts
│   └── dashboard/           karyawan, owner
├── js/app.js               Helper JS bersama (format rupiah/tanggal, auth check, badge status)
├── css/wa-style.css        Style simulasi WhatsApp
├── *.html                  Halaman UI Sistem & Simulasi WA
└── sql/init.sql            Schema + seed data
```

## Catatan
- Password disimpan dengan bcrypt (`password_hash` PHP), sudah cocok dengan hash seed di `init.sql`.
- "Pengiriman" WA disimulasikan: pesan hanya disimpan ke tabel `nota` / `struk` / `wa_notifikasi_status`,
  lalu dibaca oleh halaman `wa-simulation.html` melalui polling setiap 5 detik.
- Tidak ada login untuk pelanggan — pelanggan hanya memilih nomor HP di halaman simulasi WA.
