<?php
// register.php
require __DIR__ . '/app/bootstrap.php';
if (sudahLogin()) {
    header('Location: index.php');
    exit;
}

$flash = ambilFlash();
require __DIR__ . '/views/register.php';
