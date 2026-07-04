<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_login();

$status = trim($_GET['status'] ?? '');
$hari_ini = isset($_GET['hari_ini']) && $_GET['hari_ini'] === '1';

$sql = 'SELECT p.id, p.pelanggan_id, pl.nama AS pelanggan_nama, pl.no_hp, p.layanan, p.berat_kg, p.total_biaya, p.status, p.tanggal_masuk,
        (SELECT COUNT(*) FROM transaksi t WHERE t.pesanan_id = p.id) AS sudah_bayar
        FROM pesanan p
        JOIN pelanggan pl ON pl.id = p.pelanggan_id
        WHERE 1=1';
$params = [];
$types = '';

if ($status !== '' && in_array($status, ['Masuk', 'Proses', 'Selesai'])) {
    $sql .= ' AND p.status = ?';
    $params[] = $status;
    $types .= 's';
}

if ($hari_ini) {
    $sql .= ' AND DATE(p.tanggal_masuk) = CURDATE()';
}

$sql .= ' ORDER BY p.tanggal_masuk DESC';

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$pesanan = [];
while ($row = $result->fetch_assoc()) {
    $row['sudah_bayar'] = intval($row['sudah_bayar']) > 0;
    $pesanan[] = $row;
}

json_response(['pesanan' => $pesanan]);
