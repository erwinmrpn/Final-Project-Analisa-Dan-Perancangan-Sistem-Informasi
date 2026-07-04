<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_login();

$input = json_decode(file_get_contents('php://input'), true);
$pelanggan_id = trim($input['pelanggan_id'] ?? '');
$layanan = trim($input['layanan'] ?? '');
$berat_kg = floatval($input['berat_kg'] ?? 0);

if ($pelanggan_id === '' || !isset(HARGA_LAYANAN[$layanan]) || $berat_kg <= 0) {
    json_response(['error' => 'Data pesanan tidak valid'], 400);
}

$stmt = $conn->prepare('SELECT id, nama, no_hp FROM pelanggan WHERE id = ?');
$stmt->bind_param('s', $pelanggan_id);
$stmt->execute();
$pelanggan = $stmt->get_result()->fetch_assoc();
if (!$pelanggan) {
    json_response(['error' => 'Pelanggan tidak ditemukan'], 404);
}

$total_biaya = $berat_kg * HARGA_LAYANAN[$layanan];
$pesanan_id = generate_id('PSN');
$user_id = $_SESSION['user']['id'];

$stmt = $conn->prepare('INSERT INTO pesanan (id, pelanggan_id, user_id, layanan, berat_kg, total_biaya, status) VALUES (?, ?, ?, ?, ?, ?, "Masuk")');
$stmt->bind_param('ssssdd', $pesanan_id, $pelanggan_id, $user_id, $layanan, $berat_kg, $total_biaya);
$stmt->execute();

// Buat & "kirim" nota WA
$isi_pesan = format_pesan_nota($pelanggan['nama'], $pesanan_id, $layanan, $berat_kg, $total_biaya);
$nota_id = generate_id('NTA');
$stmt = $conn->prepare('INSERT INTO nota (id, pesanan_id, isi_pesan, status_kirim) VALUES (?, ?, ?, TRUE)');
$stmt->bind_param('sss', $nota_id, $pesanan_id, $isi_pesan);
$stmt->execute();

json_response([
    'pesanan' => [
        'id' => $pesanan_id,
        'pelanggan_id' => $pelanggan_id,
        'pelanggan_nama' => $pelanggan['nama'],
        'no_hp' => $pelanggan['no_hp'],
        'layanan' => $layanan,
        'berat_kg' => $berat_kg,
        'total_biaya' => $total_biaya,
        'status' => 'Masuk'
    ]
], 201);
