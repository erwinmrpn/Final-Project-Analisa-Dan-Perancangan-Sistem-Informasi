<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';

$no_hp = trim($_GET['no_hp'] ?? '');
if ($no_hp === '') {
    json_response(['error' => 'Nomor HP wajib diisi'], 400);
}

$pesan = [];

$stmt = $conn->prepare('SELECT n.isi_pesan, n.tanggal FROM nota n
    JOIN pesanan p ON p.id = n.pesanan_id
    JOIN pelanggan pl ON pl.id = p.pelanggan_id
    WHERE pl.no_hp = ?');
$stmt->bind_param('s', $no_hp);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $pesan[] = ['tipe' => 'nota', 'isi_pesan' => $row['isi_pesan'], 'tanggal' => $row['tanggal']];
}

$stmt = $conn->prepare('SELECT isi_pesan, tanggal FROM wa_notifikasi_status WHERE no_hp = ?');
$stmt->bind_param('s', $no_hp);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $pesan[] = ['tipe' => 'status', 'isi_pesan' => $row['isi_pesan'], 'tanggal' => $row['tanggal']];
}

$stmt = $conn->prepare('SELECT s.isi_pesan, s.tanggal FROM struk s
    JOIN transaksi t ON t.id = s.transaksi_id
    JOIN pesanan p ON p.id = t.pesanan_id
    JOIN pelanggan pl ON pl.id = p.pelanggan_id
    WHERE pl.no_hp = ?');
$stmt->bind_param('s', $no_hp);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $pesan[] = ['tipe' => 'struk', 'isi_pesan' => $row['isi_pesan'], 'tanggal' => $row['tanggal']];
}

usort($pesan, fn($a, $b) => strtotime($a['tanggal']) <=> strtotime($b['tanggal']));

json_response(['pesan' => $pesan]);
