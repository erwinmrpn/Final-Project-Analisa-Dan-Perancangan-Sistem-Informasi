<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_login();

$result = $conn->query('SELECT id, nama, no_hp, alamat FROM pelanggan ORDER BY nama ASC');
$pelanggan = [];
while ($row = $result->fetch_assoc()) {
    $pelanggan[] = $row;
}

json_response(['pelanggan' => $pelanggan]);
