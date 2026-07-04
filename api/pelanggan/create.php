<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_login();

$input = json_decode(file_get_contents('php://input'), true);
$nama = trim($input['nama'] ?? '');
$no_hp = trim($input['no_hp'] ?? '');
$alamat = trim($input['alamat'] ?? '');

if ($nama === '' || $no_hp === '') {
    json_response(['error' => 'Nama dan nomor HP wajib diisi'], 400);
}

$stmt = $conn->prepare('SELECT id FROM pelanggan WHERE no_hp = ?');
$stmt->bind_param('s', $no_hp);
$stmt->execute();
if ($stmt->get_result()->fetch_assoc()) {
    json_response(['error' => 'Nomor HP sudah terdaftar'], 409);
}

$id = generate_id('PLG');
$stmt = $conn->prepare('INSERT INTO pelanggan (id, nama, no_hp, alamat) VALUES (?, ?, ?, ?)');
$stmt->bind_param('ssss', $id, $nama, $no_hp, $alamat);
$stmt->execute();

json_response(['pelanggan' => ['id' => $id, 'nama' => $nama, 'no_hp' => $no_hp, 'alamat' => $alamat]], 201);
