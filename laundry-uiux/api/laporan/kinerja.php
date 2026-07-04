<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_login();
require_owner();

$dari = trim($_GET['dari'] ?? '');
$sampai = trim($_GET['sampai'] ?? '');

if ($dari === '' || $sampai === '') {
    json_response(['error' => 'Periode wajib diisi'], 400);
}

$stmt = $conn->prepare("SELECT u.id, u.nama, u.username,
    (SELECT COUNT(*) FROM pesanan p WHERE p.user_id = u.id AND DATE(p.tanggal_masuk) BETWEEN ? AND ?) AS jml_pesanan,
    (SELECT COUNT(*) FROM transaksi t WHERE t.user_id = u.id AND DATE(t.tanggal) BETWEEN ? AND ?) AS jml_transaksi
    FROM users u WHERE u.role = 'Karyawan' ORDER BY u.nama ASC");
$stmt->bind_param('ssss', $dari, $sampai, $dari, $sampai);
$stmt->execute();
$result = $stmt->get_result();

$karyawan = [];
$max_raw = 1;
while ($row = $result->fetch_assoc()) {
    $row['jml_pesanan'] = intval($row['jml_pesanan']);
    $row['jml_transaksi'] = intval($row['jml_transaksi']);
    $row['raw_score'] = $row['jml_pesanan'] * 0.6 + $row['jml_transaksi'] * 0.4;
    $max_raw = max($max_raw, $row['raw_score']);
    $karyawan[] = $row;
}

foreach ($karyawan as &$row) {
    $row['score_kinerja'] = round(($row['raw_score'] / $max_raw) * 100, 2);
    unset($row['raw_score']);
}
unset($row);

json_response(['karyawan' => $karyawan]);
