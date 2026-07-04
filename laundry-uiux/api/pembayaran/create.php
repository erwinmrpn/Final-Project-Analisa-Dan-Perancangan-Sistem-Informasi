<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_login();

$input = json_decode(file_get_contents('php://input'), true);
$pesanan_id = trim($input['pesanan_id'] ?? '');
$jumlah_bayar = floatval($input['jumlah_bayar'] ?? 0);
$metode_pembayaran = trim($input['metode_pembayaran'] ?? '');

if ($pesanan_id === '' || $jumlah_bayar <= 0 || !in_array($metode_pembayaran, ['Cash', 'Transfer', 'E-wallet'])) {
    json_response(['error' => 'Data pembayaran tidak valid'], 400);
}

$stmt = $conn->prepare('SELECT p.id, p.layanan, p.berat_kg, p.total_biaya, pl.nama AS pelanggan_nama, pl.no_hp
    FROM pesanan p JOIN pelanggan pl ON pl.id = p.pelanggan_id WHERE p.id = ?');
$stmt->bind_param('s', $pesanan_id);
$stmt->execute();
$pesanan = $stmt->get_result()->fetch_assoc();

if (!$pesanan) {
    json_response(['error' => 'Pesanan tidak ditemukan'], 404);
}

$stmt = $conn->prepare('SELECT id FROM transaksi WHERE pesanan_id = ?');
$stmt->bind_param('s', $pesanan_id);
$stmt->execute();
if ($stmt->get_result()->fetch_assoc()) {
    json_response(['error' => 'Pesanan ini sudah dibayar'], 409);
}

$transaksi_id = generate_id('TRX');
$user_id = $_SESSION['user']['id'];

$stmt = $conn->prepare('INSERT INTO transaksi (id, pesanan_id, user_id, jumlah_bayar, metode_pembayaran, status) VALUES (?, ?, ?, ?, ?, "Lunas")');
$stmt->bind_param('sssds', $transaksi_id, $pesanan_id, $user_id, $jumlah_bayar, $metode_pembayaran);
$stmt->execute();

$tanggal = date('d-m-Y H:i');
$isi_pesan = format_pesan_struk($pesanan['pelanggan_nama'], $transaksi_id, $pesanan_id, $pesanan['layanan'], $pesanan['berat_kg'], $jumlah_bayar, $metode_pembayaran, $tanggal);
$struk_id = generate_id('STR');
$stmt = $conn->prepare('INSERT INTO struk (id, transaksi_id, isi_pesan, status_kirim) VALUES (?, ?, ?, TRUE)');
$stmt->bind_param('sss', $struk_id, $transaksi_id, $isi_pesan);
$stmt->execute();

json_response(['transaksi_id' => $transaksi_id, 'status' => 'Lunas'], 201);
