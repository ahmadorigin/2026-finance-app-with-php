<?php
//views/layout/header.php
// Bagian atas setiap halaman. Variabel $judul diisi oleh view yang memanggi
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($judul ?? 'Keuangan') ?></title>
</head>

<body>
    <header>
        <strong> Catatan Keuangan</strong>
        <?php if (isset($_SESSION['user_id'])): ?>
            <span><?= e($_SESSION['user_name']) ?></span>
            <form action="actions/logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        <?php endif; ?>
    </header>
    <main>