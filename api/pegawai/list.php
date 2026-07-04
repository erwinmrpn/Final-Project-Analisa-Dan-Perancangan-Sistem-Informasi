<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_login();
require_owner();

$result = $conn->query('SELECT id, nama, username, role, aktif, created_at FROM users ORDER BY created_at ASC');
$pegawai = [];
while ($row = $result->fetch_assoc()) {
    $row['aktif'] = (bool)$row['aktif'];
    $pegawai[] = $row;
}

json_response(['pegawai' => $pegawai]);
