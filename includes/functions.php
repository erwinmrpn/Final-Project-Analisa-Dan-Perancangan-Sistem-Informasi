<?php
// Harga per kg per layanan
const HARGA_LAYANAN = [
    'Reguler' => 7000,
    'Express' => 12000,
    'Cuci+Setrika' => 10000
];

function generate_id($prefix) {
    return $prefix . '-' . strtoupper(base_convert(time(), 10, 36)) . strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 4));
}

function json_response($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function require_login() {
    if (!isset($_SESSION['user'])) {
        json_response(['error' => 'Tidak terautentikasi'], 401);
    }
}

function require_owner() {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Owner') {
        json_response(['error' => 'Akses ditolak'], 403);
    }
}

function format_pesan_nota($nama_pelanggan, $pesanan_id, $layanan, $berat_kg, $total_biaya) {
    $total_fmt = number_format($total_biaya, 0, ',', '.');
    return "🧺 *NOTA PESANAN*\nLaundry Kiloan\n\n" .
        "Halo, {$nama_pelanggan}!\n" .
        "Pesanan cucian Anda telah kami terima.\n\n" .
        "📋 Detail Pesanan\n" .
        "No. Pesanan   : {$pesanan_id}\n" .
        "Layanan       : {$layanan}\n" .
        "Berat         : {$berat_kg} kg\n" .
        "Total Biaya   : Rp {$total_fmt}\n" .
        "Status        : Masuk\n\n" .
        "Terima kasih telah menggunakan layanan kami! 🙏";
}

function format_pesan_status_selesai($nama_pelanggan, $pesanan_id, $sudah_bayar) {
    $catatan = $sudah_bayar ? '' : "\n[Pembayaran dilakukan saat pengambilan.]";
    return "✅ *CUCIAN SELESAI*\nLaundry Kiloan\n\n" .
        "Halo, {$nama_pelanggan}!\n" .
        "Cucian Anda dengan No. {$pesanan_id} sudah SELESAI diproses.\n\n" .
        "Silakan ambil cucian Anda di toko kami.{$catatan}\n\n" .
        "Terima kasih! 🙏";
}

function format_pesan_struk($nama_pelanggan, $transaksi_id, $pesanan_id, $layanan, $berat_kg, $jumlah_bayar, $metode, $tanggal) {
    $jumlah_fmt = number_format($jumlah_bayar, 0, ',', '.');
    return "🧾 *STRUK PEMBAYARAN*\nLaundry Kiloan\n\n" .
        "Halo, {$nama_pelanggan}!\n" .
        "Pembayaran Anda telah kami terima.\n\n" .
        "💳 Detail Pembayaran\n" .
        "No. Transaksi : {$transaksi_id}\n" .
        "No. Pesanan   : {$pesanan_id}\n" .
        "Layanan       : {$layanan}\n" .
        "Berat         : {$berat_kg} kg\n" .
        "Total Bayar   : Rp {$jumlah_fmt}\n" .
        "Metode        : {$metode}\n" .
        "Tanggal       : {$tanggal}\n" .
        "Status        : LUNAS ✓\n\n" .
        "Terima kasih sudah berlangganan! 🙏";
}
