<?php
// index.php
// Halaman utama: ambil data dari logika, lalu tampilkan lewat view.
require __DIR__ . '/app/bootstrap.php';
requireLogin();
$userId = (int) $_SESSION['user_id'];
$bulan = normalisasiBulan($_GET['bulan'] ?? null);
$transaksi = ambilTransaksiBulan($pdo, $userId, $bulan);
$ringkasan = ringkasanBulan($pdo, $userId, $bulan);
$daftarBulan = daftarBulan($pdo, $userId);
$flash = ambilFlash();
require __DIR__ . '/views/dashboard.php';
