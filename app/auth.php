<?php
// app/auth.php
// Logika autentikasi. Tidak ada HTML di sini.

// ----------- REGISTER ------------
function registerUser(PDO $pdo, string $name, string $email, string $password): array
{
    // 1. Validasi input
    $errors = [];
    if ($name === '') $errors[] = 'Nama wajib diisi.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid';
    if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';

    if ($errors) {
        return ['ok' => false, 'errors' => $errors];
    }
    // 2. Hash password sebelum disimpan
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // 3. Simpan dengan prepared statement
    try {
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $hash]);
        return ['ok' => true, 'errors' => []];
    } catch (PDOException $e) {
        // 1062 Duplicate entry: email sudah terdaftar (kolom UNIQUE)
        if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
            return ['ok' => false, 'errors' => ['Email sudah terdaftar.']];
        }

        error_log($e->getMessage());
        return ['ok' => false, 'errors' => ['Registrasi gagal, coba lagi.']];
    }
}

// ---------- LOGIN ----------
function loginUser(PDO $pdo, string $email, string $password): bool
{
    $stmt = $pdo->prepare('SELECT id, name, password FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Pesan error dibuat sama untuk "email salah" dan "password salah".
    // Tujuannya agar penyerang tidak tahu email mana yang terdaftar.
    if (!$user || ! password_verify($password, $user['password'])) {
        return false;
    }

    // Ganti ID session setelah login (mencegah session fixation)
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_name'] = $user['name'];
    return true;
}

// ------- LOGOUT --------
function logoutUser(): void
{
    $_SESSION = [];
    session_destroy();
}
//PROTEKSI HALAMAN
function sudahLogin(): bool
{
    return isset($_SESSION['user_id']);
}
// Dipanggil di awal halaman yang butuh login.
// $loginUrl dibuat parameter karena lokasi file login berbeda
// untuk halaman di root (login.php) dan untuk file di actions/ (../login.php).
function requireLogin(string $loginUrl = 'login.php'): void
{
    if (!sudahLogin()) {
        header('Location: ' . $loginUrl);
        exit;
    }
}