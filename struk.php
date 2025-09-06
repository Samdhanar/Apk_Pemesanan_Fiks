<?php
include "koneksi/connect_db.php";
date_default_timezone_set('Asia/Jakarta');
session_start();

// Pastikan hanya kasir
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'kasir') {
    header("Location: index.php");
    exit;
}

$meja = $_GET['meja'] ?? '';
$jam_menit = $_GET['jam_menit'] ?? '';
$bayar = intval($_GET['bayar'] ?? 0);
$diskon = intval($_GET['diskon'] ?? 0);

// Ambil total & tanggal
$q = $db->query("
    SELECT SUM(total_harga) as total_harga, MAX(tanggal) as tanggal
    FROM rekap_penjualan 
    WHERE meja='$meja' AND DATE_FORMAT(tanggal, '%H:%i')='$jam_menit'
");
$d = $q->fetch_assoc();
$total = $d['total_harga'];
$tanggal = date("d-m-Y H:i:s", strtotime($d['tanggal']));

// Ambil detail pesanan
$items = [];
$result = $db->query("
    SELECT m.nama, r.jumlah, r.total_harga
    FROM rekap_penjualan r
    JOIN menu m ON r.product_id = m.id
    WHERE r.meja='$meja' AND DATE_FORMAT(r.tanggal, '%H:%i')='$jam_menit'
");
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

$total_setelah_diskon = $total - ($total * $diskon / 100);
$potongan = $total - $total_setelah_diskon;
$kembali = $bayar - $total_setelah_diskon;
?>
<!DOCTYPE html>
<html lang="id">
<link rel="icon" type="image/png" href="assets/image/logo_cafe.png">
<head>
    <meta charset="UTF-8">
    <title>Cetak Struk</title>
    <style>
        body {
            font-family: monospace;
        }

        .struk {
            width: 300px;
            margin: auto;
        }

        .center {
            text-align: center;
        }

        hr {
            border: 1px dashed #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px 0;
        }

        .label {
            text-align: left;
        }

        .value {
            text-align: right;
        }
    </style>
</head>

<body onload="window.print()">
    <div class="struk">
        <div class="center">
            <h3>CAFE ELKUSA</h3>
            <small>Jl. Contoh Alamat No. 123<br>Telp: 1903-1903-1903</small>
        </div>
        <hr>
        Meja : <?= $meja ?><br>
        Jam Pesan : <?= $jam_menit ?><br>
        Tanggal : <?= $tanggal ?><br>
        <hr>
        <table>
            <?php foreach ($items as $row): ?>
                <tr>
                    <td class="label"><?= $row['nama']; ?> (x<?= $row['jumlah']; ?>)</td>
                    <td class="value">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <hr>
        <table>
            <tr>
                <td class="label">Subtotal</td>
                <td class="value">Rp <?= number_format($total, 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <td class="label">Diskon (<?= $diskon ?>%)</td>
                <td class="value">Rp <?= number_format($potongan, 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <td class="label">Total</td>
                <td class="value">Rp <?= number_format($total_setelah_diskon, 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <td class="label">Bayar</td>
                <td class="value">Rp <?= number_format($bayar, 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <td class="label">Kembali</td>
                <td class="value">Rp <?= number_format($kembali, 0, ',', '.'); ?></td>
            </tr>
        </table>
        <hr>
        <div class="center">
            Terima kasih sudah berbelanja 🙏<br>
            *** Sampai Jumpa Lagi ***
        </div>
    </div>

    <script>
        // Tunggu sampai proses print selesai, lalu otomatis kembali ke kasir.php
        window.onafterprint = function() {
            window.location.href = "halaman_kasir.php";
        };

        // Kalau user batal print, tetap redirect setelah beberapa detik
        setTimeout(() => {
            window.location.href = "halaman_kasir.php";
        }, 5000); // 5 detik
    </script>

</body>

</html>