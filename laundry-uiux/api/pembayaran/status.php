<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_login();

$pesanan_id = trim($_GET['pesanan_id'] ?? '');
if ($pesanan_id === '') {
    json_response(['error' => 'ID pesanan wajib diisi'], 400);
}

$stmt = $conn->prepare('SELECT id, jumlah_bayar, metode_pembayaran, status, tanggal FROM transaksi WHERE pesanan_id = ?');
$stmt->bind_param('s', $pesanan_id);
$stmt->execute();
$transaksi = $stmt->get_result()->fetch_assoc();

json_response(['transaksi' => $transaksi ?: null]);
