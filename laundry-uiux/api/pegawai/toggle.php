<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_login();
require_owner();

$input = json_decode(file_get_contents('php://input'), true);
$id = trim($input['id'] ?? '');

if ($id === '') {
    json_response(['error' => 'ID pegawai wajib diisi'], 400);
}

$stmt = $conn->prepare('SELECT aktif FROM users WHERE id = ?');
$stmt->bind_param('s', $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    json_response(['error' => 'Pegawai tidak ditemukan'], 404);
}

$aktif_baru = $user['aktif'] ? 0 : 1;
$stmt = $conn->prepare('UPDATE users SET aktif = ? WHERE id = ?');
$stmt->bind_param('is', $aktif_baru, $id);
$stmt->execute();

json_response(['aktif' => (bool)$aktif_baru]);
