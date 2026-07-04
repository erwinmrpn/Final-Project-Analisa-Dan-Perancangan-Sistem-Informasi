<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_login();
require_owner();

$input = json_decode(file_get_contents('php://input'), true);
$nama = trim($input['nama'] ?? '');
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';
$role = trim($input['role'] ?? '');

if ($nama === '' || $username === '' || $password === '' || !in_array($role, ['Karyawan', 'Owner'])) {
    json_response(['error' => 'Data pegawai tidak valid'], 400);
}

$stmt = $conn->prepare('SELECT id FROM users WHERE username = ?');
$stmt->bind_param('s', $username);
$stmt->execute();
if ($stmt->get_result()->fetch_assoc()) {
    json_response(['error' => 'Username sudah digunakan'], 409);
}

$id = generate_id('USR');
$hash = password_hash($password, PASSWORD_BCRYPT);
$stmt = $conn->prepare('INSERT INTO users (id, nama, username, password, role) VALUES (?, ?, ?, ?, ?)');
$stmt->bind_param('sssss', $id, $nama, $username, $hash, $role);
$stmt->execute();

json_response(['pegawai' => ['id' => $id, 'nama' => $nama, 'username' => $username, 'role' => $role, 'aktif' => true]], 201);
