<?php
// views/layout/flash.php
// Menampilkan pesan sekali tampil. $flash diisi oleh halaman root.
if (isset($flash)):
?>
    <div role="alert" data-tipe="<?= e($flash['tipe']) ?>">
        <?= e($flash['pesan']) ?>
    </div>
<?php endif; ?>