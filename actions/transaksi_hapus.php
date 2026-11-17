<?php
// actions/transaksi_hapus.php
require __DIR__ . '/../app/bootstrap.php';
requireLogin('../login.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$bulan = normalisasiBulan($_POST['bulan'] ?? null);
$id = (int) ($_POST['id'] ?? 0);

if (hapusTransaksi($pdo, (int) $_SESSION['user_id'], $id)) {
    setFlash('success', 'Transaksi berhasil dihapus.');
} else {
    setFlash('error', 'Transaksi tidak ditemukan.');
}
header('Location: ../index.php?bulan=' . $bulan);
exit;
