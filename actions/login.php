<?php
// actions/login.php
require __DIR__ . '/../app/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}
$berhasil = loginUser(
    $pdo,
    trim($_POST['email'] ?? ''),
    $_POST['password'] ?? ''
);
if ($berhasil) {
    header('Location: ../index.php');
    exit;
}
setFlash('error', 'Email atau password salah.');
header('Location: ../login.php');
exit;;
