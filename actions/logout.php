<?php
// actions/logout.php
require __DIR__ . '/../app/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    logoutUser();
}
header('Location: ../login.php');
exit;
