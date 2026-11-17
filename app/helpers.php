<?php
// app/helpers.php
// Fungsi bantu kecil yang dipakai di banyak tempat.
// Mengubah karakter khusus HTML agar aman ditampilkan (cegah XSS)
function e(?string $teks): string
{
    return htmlspecialchars($teks ?? '', ENT_QUOTES, 'UTF-8');
}

// Mengubah angka menjadi format rupiah: 1500000 -> "Rp 1.500.000"
function rupiah($angka): string
{
    return 'Rp ' . number_format((float) $angka, 0, ',', '.');
}

// Mengubah nama bulan: "2026-11" -> "November 2026"
function namaBulan(string $ym): string
{
    $daftar = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '07' => 'Juli',
        '06' => 'Juni',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember',
    ];
    [$tahun, $bulan] = explode('-', $ym);
    return $daftar[$bulan] . ' ' . $tahun;
}

// Pindah halaman lalu hentikan script
function redirect(string $url): void
{
    header("Location: $url");
    exit;
}
// Menyimpan pesan sekali tampil (flash message)
function setFlash(string $tipe, string $pesan): void
{
    $_SESSION['flash'] = ['tipe' => $tipe, 'pesan' => $pesan];
}
// Mengambil pesan lalu langsung menghapusnya dari session
function ambilFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}
