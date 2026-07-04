<?php
// PHP default ke UTC, sedangkan MySQL server pakai timezone lokal (WIB) -
// tanpa ini, perhitungan tanggal "hari ini" di PHP (mis. grafik dashboard) meleset.
date_default_timezone_set('Asia/Jakarta');

// Konfigurasi koneksi MySQL
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'laundry_kiloan';

mysqli_report(MYSQLI_REPORT_OFF);
$conn = mysqli_init();
@$conn->real_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    http_response_code(500);
    header('Content-Type: application/json');
    die(json_encode(['error' => 'Koneksi database gagal: ' . $conn->connect_error]));
}

$conn->set_charset('utf8mb4');
