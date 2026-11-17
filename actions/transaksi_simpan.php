<?php
// actions/transaksi_simpan.php
require __DIR__ . '/../app/bootstrap.php';
requireLogin('../login.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}
$bulan = normalisasiBulan($_POST['bulan'] ?? null);
$result = tambahTransaksi($pdo, (int) $_SESSION['user_id'], $_POST);
if ($result['ok']) {
    setFlash('success', 'Transaksi berhasil disimpan.');
} else {
    setFlash('error', implode('', $result['errors']));
}
header('Location: ../index.php?bulan=' . $bulan);
exit;
