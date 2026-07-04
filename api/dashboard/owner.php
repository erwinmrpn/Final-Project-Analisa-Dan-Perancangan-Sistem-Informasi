<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_login();
require_owner();

$total_pendapatan = $conn->query("SELECT COALESCE(SUM(jumlah_bayar),0) c FROM transaksi WHERE status='Lunas' AND MONTH(tanggal)=MONTH(CURDATE()) AND YEAR(tanggal)=YEAR(CURDATE())")->fetch_assoc()['c'];
$total_pesanan = $conn->query("SELECT COUNT(*) c FROM pesanan WHERE MONTH(tanggal_masuk)=MONTH(CURDATE()) AND YEAR(tanggal_masuk)=YEAR(CURDATE())")->fetch_assoc()['c'];
$rata_rata = intval($total_pesanan) > 0 ? floatval($total_pendapatan) / intval($total_pesanan) : 0;

$rentang_valid = [7, 14, 30, 90];
$rentang = intval($_GET['rentang'] ?? 7);
if (!in_array($rentang, $rentang_valid)) {
    $rentang = 7;
}

$grafik = [];
$hari_mundur = $rentang - 1;
$stmt = $conn->prepare("SELECT DATE(tanggal) tgl, SUM(jumlah_bayar) total FROM transaksi WHERE status='Lunas' AND tanggal >= DATE_SUB(CURDATE(), INTERVAL ? DAY) GROUP BY DATE(tanggal)");
$stmt->bind_param('i', $hari_mundur);
$stmt->execute();
$result = $stmt->get_result();
$map = [];
while ($row = $result->fetch_assoc()) {
    $map[$row['tgl']] = floatval($row['total']);
}
for ($i = $hari_mundur; $i >= 0; $i--) {
    $tgl = date('Y-m-d', strtotime("-$i day"));
    $grafik[] = ['tanggal' => $tgl, 'total' => $map[$tgl] ?? 0];
}

$pesanan_terbaru = [];
$result = $conn->query("SELECT p.id, pl.nama AS pelanggan_nama, p.layanan, p.berat_kg, p.total_biaya, p.status, p.tanggal_masuk
    FROM pesanan p JOIN pelanggan pl ON pl.id = p.pelanggan_id ORDER BY p.tanggal_masuk DESC LIMIT 5");
while ($row = $result->fetch_assoc()) {
    $pesanan_terbaru[] = $row;
}

$kinerja_karyawan = [];
$result = $conn->query("SELECT u.id, u.nama,
    (SELECT COUNT(*) FROM pesanan p WHERE p.user_id = u.id AND MONTH(p.tanggal_masuk)=MONTH(CURDATE()) AND YEAR(p.tanggal_masuk)=YEAR(CURDATE())) AS jml_pesanan
    FROM users u WHERE u.role = 'Karyawan'");
while ($row = $result->fetch_assoc()) {
    $row['jml_pesanan'] = intval($row['jml_pesanan']);
    $kinerja_karyawan[] = $row;
}

json_response([
    'total_pendapatan' => floatval($total_pendapatan),
    'total_pesanan' => intval($total_pesanan),
    'rata_rata' => $rata_rata,
    'rentang' => $rentang,
    'grafik' => $grafik,
    'pesanan_terbaru' => $pesanan_terbaru,
    'kinerja_karyawan' => $kinerja_karyawan
]);
