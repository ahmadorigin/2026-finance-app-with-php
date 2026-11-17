<?php
// app/config.php
// Koneksi database + session. Dimuat oleh bootstrap.php.
// session_start() harus dipanggil sebelum ada output apa pun.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Deteksi lingkungan lokal: localhost, 127.0.0.1, atau domain *.test (Larago
$serverName = $_SERVER['SERVER_NAME'] ?? 'localhost';
$isLocal = in_array($serverName, ['localhost', '127.0.0.1'], true)
    || str_ends_with($serverName, 'test');
if ($isLocal) {
    // --Local (MAMP / XAMPP / Laragon)
    // Sesuaikan dengan tabel di bagian A.1
    $host = '127.0.0.1';
    $port = '3306';
    // MAMP: 8889 | XAMPP & Laragon: 3306
    $db = 'db_financeapp';
    $user = 'root';
    $pass = '';
    // MAMP: root | XAMPP & Laragon: (kosong)
} else {
    // Production (InfinityFree)
    // Nilai ini diisi dari panel hosting (lihat bagian F).
    $host = 'sqlXXX.infinityfree.com';
    $port = '3306';
    $db = 'if0_43460027_db_finance';
    $user = 'if0_43460027';
    $pass = 'VAjEYvWfPqOQOv';
    // Jangan tampilkan error PHP ke pengunjung di production
    ini_set('display_errors', '0');
}
try {
    $pdo = new PDO(
        "mysql:host=$host; port=$port; dbname=$db; charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION, // error
            PDO::ATTR_DEFAULT_FETCH_MODE    => PDO::FETCH_ASSOC, // hasil
        ]
    );
} catch (PDOException $e) {
    // Catat detail error di log, tampilkan pesan umum saja
    error_log($e->getMessage());
    die('Koneksi database gagal.');
}
