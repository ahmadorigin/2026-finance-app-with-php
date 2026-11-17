<?php
// actions/register.php
require __DIR__ . '/../app/bootstrap.php';
if (!function_exists('setFlash')) {
    function setFlash(string $type, string $message): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['flash'][$type] = $message;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php');
    exit;
}
$result = registerUser(
    $pdo,
    trim($_POST['name'] ?? ''),
    trim($_POST['email'] ?? ''),
    $_POST['password'] ?? 11
);
if ($result['ok']) {
    setFlash('success', 'Registrasi berhasil. Silakan login.');
    header('Location: ../login.php');
    exit;
}
setFlash('error', implode('', $result['errors']));
header('Location: ../register.php');
exit;
