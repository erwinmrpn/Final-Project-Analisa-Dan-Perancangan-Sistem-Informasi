<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_login();

$no_hp = trim($_GET['no_hp'] ?? '');
if ($no_hp === '') {
    json_response(['error' => 'Nomor HP wajib diisi'], 400);
}

$stmt = $conn->prepare('SELECT id, nama, no_hp, alamat FROM pelanggan WHERE no_hp = ?');
$stmt->bind_param('s', $no_hp);
$stmt->execute();
$result = $stmt->get_result();
$pelanggan = $result->fetch_assoc();

json_response(['pelanggan' => $pelanggan ?: null]);
