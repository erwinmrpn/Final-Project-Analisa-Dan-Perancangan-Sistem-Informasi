<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_login();
require_owner();

$input = json_decode(file_get_contents('php://input'), true);
$id = trim($input['id'] ?? '');
$nama = trim($input['nama'] ?? '');
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';

if ($id === '' || $nama === '' || $username === '') {
    json_response(['error' => 'Data pegawai tidak valid'], 400);
}

$stmt = $conn->prepare('SELECT id FROM users WHERE username = ? AND id != ?');
$stmt->bind_param('ss', $username, $id);
$stmt->execute();
if ($stmt->get_result()->fetch_assoc()) {
    json_response(['error' => 'Username sudah digunakan'], 409);
}

if ($password !== '') {
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare('UPDATE users SET nama = ?, username = ?, password = ? WHERE id = ?');
    $stmt->bind_param('ssss', $nama, $username, $hash, $id);
} else {
    $stmt = $conn->prepare('UPDATE users SET nama = ?, username = ? WHERE id = ?');
    $stmt->bind_param('sss', $nama, $username, $id);
}
$stmt->execute();

json_response(['ok' => true]);
