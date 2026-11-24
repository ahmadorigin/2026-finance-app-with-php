<?php
// views/dashboard.php
// Hanya menampilkan variabel: $bulan, $transaksi, $ringkasan, $daftarBulan,
$judul = 'Dashboard Keuangan';
require __DIR__ . '/layout/header.php';
require __DIR__ . '/../index.php';
?>

<!-- Pilih bulan -->
<form method="GET" action="index.php">
    <input type="month" name="bulan" value="<?= e($bulan) ?>">
    <button type="submit">Tampilkan</button>
</form>
<?php if ($daftarBulan): ?>
<nav>
    <?php foreach ($daftarBulan as $b): ?>
    <a href="index.php?bulan=<?= e($b) ?>" <?= $b === $bulan ? 'aria-current="page"' : '' ?>>
        <?= e(namaBulan($b)) ?>
    </a>
    <?php endforeach; ?>
</nav>
<?php endif; ?>
<h1>Ringkasan <?= e(namaBulan($bulan)) ?></h1>
<?php require __DIR__ . '/layout/flash.php'; ?>

<!-- Ringkasan bulan -->
<section>
    <article>
        <h2>Pemasukan</h2>
        <p data-jenis="income"><?= rupiah($ringkasan['masuk']) ?></p>
    </article>
    <article>
        <h2>Pengeluaran</h2>
        <p data-jenis="expense"><?= rupiah($ringkasan['keluar']) ?></p>
    </article>
    <article>
        <h2>Saldo</h2>
        <p data-saldo="<?= $ringkasan['saldo'] >= 0 ? 'positif' : 'negatif' ?>">
            <?= rupiah($ringkasan['saldo']) ?>
        </p>
    </article>
</section>

<!-- Form tambah transaksi -->
<section>
    <h2><?= isset($transaksiEdit) ? 'Edit' : 'Tambah'; ?> Transaksi</h2>
    <form action="actions/transaksi_simpan.php" method="POST">
        <input type="hidden" name="bulan" value="<?= e($bulan) ?>">
        <input type="hidden" name="id" value="<?= e($transaksiEdit['id'] ?? '') ?>">
        <input type="hidden" name="status" value="<?= (isset($transaksiEdit)) ? 'edit' : 'add' ?>">
        <label>
            Jenis
            <select name="type" required>
                <option value="income"
                    <?= (isset($transaksiEdit['type']) && $transaksiEdit['type'] == 'income') ? 'selected' : ''; ?>>
                    Pemasukan
                </option>
                <option value="expense"
                    <?= (isset($transaksiEdit['type']) && $transaksiEdit['type'] == 'expense') ? 'selected' : ''; ?>>
                    Pengeluaran
                </option>
            </select>
        </label>
        <label>
            Jumlah (Rp)
            <input type="number" name="amount" min="1" step="any" required
                <?php if (isset($transaksiEdit)) echo 'value="' . e($transaksiEdit['amount']) . '"'; ?>>
        </label>
        <label>
            Kategori
            <input type="text" name="category" placeholder="mis. Makan, Gaji" required
                <?php if (isset($transaksiEdit)) echo 'value="' . e($transaksiEdit['category']) . '"'; ?>>
        </label>
        <label>
            Keterangan (opsional)
            <input type=" text" name="note"
                <?php if (isset($transaksiEdit)) echo 'value="' . e($transaksiEdit['note']) . '"'; ?>>
        </label>
        <label>
            Tanggal
            <input type="date" name="trx_date" required
                <?php if (isset($transaksiEdit)) echo 'value="' . e($transaksiEdit['trx_date']) . '"'; ?>>
        </label>
        <button type="submit">Simpan</button>
    </form>
</section>

<!-- Daftar transaksi -->
<section>
    <h2>Daftar Transaksi</h2>
    <?php if (!$transaksi): ?>
    <p>Belum ada transaksi di bulan ini.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Kategori</th>
                <th>Keterangan</th>
                <th>Jumlah</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transaksi as $t): ?>
            <tr>
                <td><?= e(date('d/m/Y', strtotime($t['trx_date']))) ?></td>
                <td data-jenis="<?= e($t['type']) ?>">
                    <?= $t['type'] === 'income' ? 'Masuk' : 'Keluar' ?>
                </td>
                <td><?= e($t['category']) ?></td>
                <td><?= e($t['note'] ?? '-') ?></td>
                <td><?= rupiah($t['amount']) ?></td>
                <td>
                    <form action="actions/transaksi_hapus.php" method="POST"
                        onsubmit="return confirm('Hapus transaksi ini?')">
                        <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
                        <input type="hidden" name="bulan" value="<?= e($bulan) ?>">
                        <button type="submit">Hapus</button>
                    </form>
                </td>
                <td>
                    <form action="" method="POST">
                        <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
                        <input type="hidden" name="bulan" value="<?= e($bulan) ?>">
                        <button type="submit">Edit</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/layout/footer.php'; ?>