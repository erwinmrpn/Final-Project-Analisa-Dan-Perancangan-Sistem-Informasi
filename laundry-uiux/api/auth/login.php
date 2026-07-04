<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';

$input = json_decode(file_get_contents('php://input'), true);
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';

if ($username === '' || $password === '') {
    json_response(['error' => 'Username dan password wajib diisi'], 400);
}

$stmt = $conn->prepare('SELECT id, nama, username, password, role, aktif FROM users WHERE username = ?');
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || !password_verify($password, $user['password'])) {
    json_response(['error' => 'Username atau password salah'], 401);
}

if (!$user['aktif']) {
    json_response(['error' => 'Akun Anda dinonaktifkan'], 403);
}

$_SESSION['user'] = [
    'id' => $user['id'],
    'nama' => $user['nama'],
    'username' => $user['username'],
    'role' => $user['role'],
];

json_response(['user' => $_SESSION['user']]);
