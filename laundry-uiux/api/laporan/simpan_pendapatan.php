<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_login();
require_owner();

$input = json_decode(file_get_contents('php://input'), true);
$dari = trim($input['periode_awal'] ?? '');
$sampai = trim($input['periode_akhir'] ?? '');
$total_pendapatan = floatval($input['total_pendapatan'] ?? 0);
$total_pesanan = intval($input['total_pesanan'] ?? 0);
$rata_rata = floatval($input['rata_rata'] ?? 0);

if ($dari === '' || $sampai === '') {
    json_response(['error' => 'Periode wajib diisi'], 400);
}

$id = generate_id('LPD');
$user_id = $_SESSION['user']['id'];
$stmt = $conn->prepare('INSERT INTO laporan_pendapatan (id, user_id, periode_awal, periode_akhir, total_pendapatan, total_pesanan, rata_rata) VALUES (?, ?, ?, ?, ?, ?, ?)');
$stmt->bind_param('ssssdid', $id, $user_id, $dari, $sampai, $total_pendapatan, $total_pesanan, $rata_rata);
$stmt->execute();

json_response(['id' => $id], 201);
