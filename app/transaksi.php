<?php
// app/transaksi.php
// Logika transaksi: validasi, simpan, ambil, hapus, dan ringkasan bulanan.
// Tidak ada HTML di sini.
// Memastikan format bulan benar: "YYYY-MM". Kalau tidak valid, pakai bulan saat ini

function normalisasiBulan(?string $bulan): string
{
    if ($bulan !== null && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $bulan)) {
        return $bulan;
    }
    return date('Y-m');
}

// Mengubah amount tampilan menjadi angka murni sebelum divalidasi atau disimpan.
// Titik adalah pemisah ribuan, sedangkan spasi juga dibuang untuk jaga-jaga.
function normalisasiAmount(mixed $amount): string
{
    $amount = trim((string) $amount);
    $amount = str_replace(['.', ' '], '', $amount);

    return $amount;
}

// Validasi input form. Mengembalikan array berisi pesan error (kosong = valid).
function validasiTransaksi(array $input): array
{
    $errors = [];
    if (!in_array($input['type'] ?? '', ['income', 'expense'], true)) {
        $errors[] = 'Jenis transaksi tidak valid.';
    }
    // Nominal bertitik dinormalisasi dulu agar tetap bisa melewati validasi angka.
    $jumlah = normalisasiAmount($input['amount'] ?? '');
    if (!is_numeric($jumlah) || (float) $jumlah <= 0) {
        $errors[] = 'Jumlah harus angka lebih dari 0.';
    }
    if (trim($input['category'] ?? '') === '') {
        $errors[] = 'Kategori wajib diisi.';
    }
    // Tanggal harus tepat berformat YYYY-MM-DD dan benar-benar ada.
    $tanggal = $input['trx_date'] ?? '';
    $dt = DateTime::createFromFormat('Y-m-d', $tanggal);
    if (!$dt || $dt->format('Y-m-d') !== $tanggal) {
        $errors[] = 'Tanggal tidak valid.';
    }
    return $errors;
}

// Menyimpan transaksi baru
function tambahTransaksi(PDO $pdo, int $userId, array $input): array
{
    $errors = validasiTransaksi($input);
    if ($errors) {
        return ['ok' => false, 'errors' => $errors];
    }

    // Titik hanya format tampilan; database menerima angka tanpa pemisah ribuan.
    // Normalisasi lagi saat edit agar data lama dan data baru memiliki format backend yang sama.
    $amount = normalisasiAmount($input['amount'] ?? '');
    $note = trim($input['note'] ?? '');
    $stmt = $pdo->prepare(
        'INSERT INTO transactions (user_id, type, amount, category, note, trx_date)
            VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $userId,
        $input['type'],
        $amount,
        trim($input['category']),
        $note === '' ? null : $note,
        $input['trx_date'],
    ]);
    return ['ok' => true, 'errors' => []];
}

// Mengedit transaksi yang sudah ada
function editTransaksi(PDO $pdo, int $userId, int $id, array $input): array
{
    $errors = validasiTransaksi($input);
    if ($errors) {
        return ['ok' => false, 'errors' => $errors];
    }

    $amount = normalisasiAmount($input['amount'] ?? '');
    $note = trim($input['note'] ?? '');
    $stmt = $pdo->prepare(
        'UPDATE transactions
            SET type = ?, amount = ?, category = ?, note = ?, trx_date = ?
            WHERE id = ? AND user_id = ?'
    );
    $stmt->execute([
        $input['type'],
        $amount,
        trim($input['category']),
        $note === '' ? null : $note,
        $input['trx_date'],
        (int) $id,
        $userId,
    ]);
    return ['ok' => true, 'errors' => []];
}

// Mengambil semua transaksi milik user pada satu bulan
function ambilTransaksiBulan(PDO $pdo, int $userId, string $bulan): array
{
    $stmt = $pdo->prepare(
        "SELECT id, type, amount, category, note, trx_date
            FROM transactions
            WHERE user_id = ? AND DATE_FORMAT(trx_date, '%Y-%m') = ?
            ORDER BY trx_date DESC, id DESC"
    );
    $stmt->execute([$userId, $bulan]);
    return $stmt->fetchAll();
}

// Mengambil 1 transaksi milik user yg terpilih
function ambilTransaksiById(PDO $pdo, int $userId, int $id): ?array
{
    $stmt = $pdo->prepare(
        "SELECT id, type, amount, category, note, trx_date
            FROM transactions
            WHERE user_id = ? AND id = ?"
    );
    $stmt->execute([$userId, $id]);
    $transaksi = $stmt->fetch();
    return $transaksi ?: null;
}

// Menghitung total masuk, keluar, dan saldo pada satu bulan
function ringkasanBulan(PDO $pdo, int $userId, string $bulan): array
{
    $stmt = $pdo->prepare(
        "SELECT type, SUM(amount) AS total
            FROM transactions
            WHERE user_id = ? AND DATE_FORMAT(trx_date, '%Y-%m') = ?
            GROUP BY type"
    );
    $stmt->execute([$userId, $bulan]);
    $masuk = 0.0;
    $keluar = 0.0;
    foreach ($stmt->fetchAll() as $baris) {
        if ($baris['type'] === 'income') $masuk = (float) $baris['total'];
        if ($baris['type'] === 'expense') $keluar = (float) $baris['total'];
    }
    return [
        'masuk' => $masuk,
        'keluar' => $keluar,
        'saldo' => $masuk - $keluar,
    ];
}

// Daftar bulan yang punya transaksi (untuk navigasi cepat), terbaru di atas
function daftarBulan(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare(
        "SELECT DISTINCT DATE_FORMAT(trx_date, '%Y-%m') AS bulan
            FROM transactions
            WHERE user_id = ?
            ORDER BY bulan DESC"
    );
    $stmt->execute([$userId]);
    return array_column($stmt->fetchAll(), 'bulan');
}

// Menghapus transaksi. user_id ikut di WHERE supaya user tidak bisa menghapus
function hapusTransaksi(PDO $pdo, int $userId, int $id): bool
{
    $stmt = $pdo->prepare('DELETE FROM transactions WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $userId]);
    return $stmt->rowCount() > 0;
}
