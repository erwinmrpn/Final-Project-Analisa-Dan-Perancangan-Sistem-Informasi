<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_login();

$id = trim($_GET['id'] ?? '');
if ($id === '') {
    json_response(['error' => 'ID pesanan wajib diisi'], 400);
}

$stmt = $conn->prepare('SELECT p.id, p.pelanggan_id, pl.nama AS pelanggan_nama, pl.no_hp, p.layanan, p.berat_kg, p.total_biaya, p.status, p.tanggal_masuk,
    (SELECT COUNT(*) FROM transaksi t WHERE t.pesanan_id = p.id) AS sudah_bayar
    FROM pesanan p JOIN pelanggan pl ON pl.id = p.pelanggan_id WHERE p.id = ?');
$stmt->bind_param('s', $id);
$stmt->execute();
$pesanan = $stmt->get_result()->fetch_assoc();

if (!$pesanan) {
    json_response(['error' => 'Pesanan tidak ditemukan'], 404);
}

$pesanan['sudah_bayar'] = intval($pesanan['sudah_bayar']) > 0;

json_response(['pesanan' => $pesanan]);
