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

$stmt = $conn->prepare('SELECT t.id AS transaksi_id, t.pesanan_id, pl.nama AS pelanggan_nama, p.layanan, t.jumlah_bayar, t.metode_pembayaran, t.tanggal
    FROM transaksi t
    JOIN pesanan p ON p.id = t.pesanan_id
    JOIN pelanggan pl ON pl.id = p.pelanggan_id
    WHERE DATE(t.tanggal) BETWEEN ? AND ?
    ORDER BY t.tanggal ASC');
$stmt->bind_param('ss', $dari, $sampai);
$stmt->execute();
$result = $stmt->get_result();

$transaksi = [];
$total_pendapatan = 0;
$per_hari = [];
while ($row = $result->fetch_assoc()) {
    $transaksi[] = $row;
    $total_pendapatan += floatval($row['jumlah_bayar']);
    $tgl = date('Y-m-d', strtotime($row['tanggal']));
    $per_hari[$tgl] = ($per_hari[$tgl] ?? 0) + floatval($row['jumlah_bayar']);
}

$total_pesanan = count($transaksi);
$rata_rata = $total_pesanan > 0 ? $total_pendapatan / $total_pesanan : 0;

json_response([
    'total_pendapatan' => $total_pendapatan,
    'total_pesanan' => $total_pesanan,
    'rata_rata' => $rata_rata,
    'per_hari' => $per_hari,
    'transaksi' => $transaksi
]);
