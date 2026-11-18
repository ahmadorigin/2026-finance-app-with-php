<?php
// login.php
require __DIR__ . '/app/bootstrap.php';
if (sudahLogin()) {
    redirect('index.php');
}
$flash = ambilFlash();
require __DIR__ . '/views/login.php';
