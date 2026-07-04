<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_login();

$input = json_decode(file_get_contents('php://input'), true);
$id = trim($input['id'] ?? '');

if ($id === '') {
    json_response(['error' => 'ID pesanan wajib diisi'], 400);
}

$stmt = $conn->prepare('SELECT p.id, p.status, p.pelanggan_id, pl.nama AS pelanggan_nama, pl.no_hp,
    (SELECT COUNT(*) FROM transaksi t WHERE t.pesanan_id = p.id) AS sudah_bayar
    FROM pesanan p JOIN pelanggan pl ON pl.id = p.pelanggan_id WHERE p.id = ?');
$stmt->bind_param('s', $id);
$stmt->execute();
$pesanan = $stmt->get_result()->fetch_assoc();

if (!$pesanan) {
    json_response(['error' => 'Pesanan tidak ditemukan'], 404);
}

$urutan = ['Masuk' => 'Proses', 'Proses' => 'Selesai'];
$status_baru = $urutan[$pesanan['status']] ?? null;

if (!$status_baru) {
    json_response(['error' => 'Status sudah final, tidak bisa diubah lagi'], 400);
}

$stmt = $conn->prepare('UPDATE pesanan SET status = ? WHERE id = ?');
$stmt->bind_param('ss', $status_baru, $id);
$stmt->execute();

$notif_terkirim = false;
if ($status_baru === 'Selesai') {
    $sudah_bayar = intval($pesanan['sudah_bayar']) > 0;
    $isi_pesan = format_pesan_status_selesai($pesanan['pelanggan_nama'], $id, $sudah_bayar);
    $notif_id = generate_id('WAN');
    $stmt = $conn->prepare('INSERT INTO wa_notifikasi_status (id, pesanan_id, no_hp, isi_pesan) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('ssss', $notif_id, $id, $pesanan['no_hp'], $isi_pesan);
    $stmt->execute();
    $notif_terkirim = true;
}

json_response(['status' => $status_baru, 'notifikasi_wa_terkirim' => $notif_terkirim]);
