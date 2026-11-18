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
    <h2>Tambah Transaksi</h2>
    <form action="actions/transaksi_simpan.php" method="POST">
        <input type="hidden" name="bulan" value="<?= e($bulan) ?>">
        <label>
            Jenis
            <select name="type" required>
                <option value="income">Pemasukan</option>
                <option value="expense">Pengeluaran</option>
            </select>
        </label>
        <label>
            Jumlah (Rp)
            <input type="number" name="amount" min="1" step="any" required>
        </label>
        <label>
            Kategori
            <input type="text" name="category" placeholder="mis. Makan, Gaji" required>
        </label>
        <label>
            Keterangan (opsional)
            <input type=" text" name="note">
        </label>
        <label>
            Keterangan (opsional)
            <input type="text" name="note">
        </label>
        <label>
            Tanggal
            <input type="date" name="trx_date" value="<?= e(date('Y-m-d')) ?>" required>
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
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/layout/footer.php'; ?>