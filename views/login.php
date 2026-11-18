<?php
// views/login.php
$judul = 'Login';
require __DIR__ . '/layout/header.php';
?>
<section>
    <h1>Login</h1>
    <?php require __DIR__ . '/layout/flash.php'; ?>
    <form action="actions/login.php" method="POST">
        <label>
            Email
            <input type="email" name="email" required>
        </label>
        <label>
            Password
            <input type="password" name="password" required>
        </label>
        <button type="submit">Login</button>
    </form>
    <p>Belum punya akun? <a href="register.php">Daftar</a></p>
</section>
<?php require __DIR__ . '/layout/footer.php'; ?>