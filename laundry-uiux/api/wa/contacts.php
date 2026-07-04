<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';

$result = $conn->query('SELECT id, nama, no_hp FROM pelanggan ORDER BY nama ASC');
$kontak = [];
while ($row = $result->fetch_assoc()) {
    $kontak[] = $row;
}

json_response(['kontak' => $kontak]);
