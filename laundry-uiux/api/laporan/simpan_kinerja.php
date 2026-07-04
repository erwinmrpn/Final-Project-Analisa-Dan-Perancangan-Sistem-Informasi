<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_login();
require_owner();

$input = json_decode(file_get_contents('php://input'), true);
$dari = trim($input['periode_awal'] ?? '');
$sampai = trim($input['periode_akhir'] ?? '');
$karyawan = $input['karyawan'] ?? [];

if ($dari === '' || $sampai === '' || !is_array($karyawan)) {
    json_response(['error' => 'Data laporan tidak valid'], 400);
}

$user_id = $_SESSION['user']['id'];
$stmt = $conn->prepare('INSERT INTO laporan_kinerja (id, user_id, karyawan_id, periode_awal, periode_akhir, jml_pesanan, jml_transaksi, score_kinerja) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');

foreach ($karyawan as $k) {
    $id = generate_id('LKJ');
    $karyawan_id = $k['id'];
    $jml_pesanan = intval($k['jml_pesanan']);
    $jml_transaksi = intval($k['jml_transaksi']);
    $score = floatval($k['score_kinerja']);
    $stmt->bind_param('sssssiid', $id, $user_id, $karyawan_id, $dari, $sampai, $jml_pesanan, $jml_transaksi, $score);
    $stmt->execute();
}

json_response(['ok' => true], 201);
