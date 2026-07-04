<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_login();

$masuk = $conn->query("SELECT COUNT(*) c FROM pesanan WHERE DATE(tanggal_masuk) = CURDATE()")->fetch_assoc()['c'];
$selesai = $conn->query("SELECT COUNT(*) c FROM pesanan WHERE status = 'Selesai' AND DATE(tanggal_masuk) = CURDATE()")->fetch_assoc()['c'];
$pendapatan = $conn->query("SELECT COALESCE(SUM(jumlah_bayar),0) c FROM transaksi WHERE DATE(tanggal) = CURDATE() AND status = 'Lunas'")->fetch_assoc()['c'];

json_response([
    'pesanan_masuk_hari_ini' => intval($masuk),
    'pesanan_selesai_hari_ini' => intval($selesai),
    'pendapatan_hari_ini' => floatval($pendapatan)
]);
