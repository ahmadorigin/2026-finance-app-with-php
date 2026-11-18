<?php
// views/register.php
$judul = 'Daftar';
require __DIR__ . '/layout/header.php';
?>
<section>
    <h1>Daftar Akun Baru</h1>
    <?php require __DIR__ . '/layout/flash.php'; ?>
    <form action="actions/register.php" method="POST">
        <label>
            Nama lengkap
            <input type="text" name="name" required>
        </label>
        <label>
            Email
            <input type="email" name="email" required>
        </label>
        <label>
            Password (min. 6 karakter)
            <input type="password" name="password" required>
        </label>
        <button type="submit">Daftar</button>
    </form>

    <p>Sudah punya akun? <a href="login.php">Login</a></p>
</section>

<?php require __DIR__ . '/layout/footer.php'; ?>