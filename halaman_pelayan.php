<?php
session_start();
include "koneksi/connect_db.php";
// Kalau belum login atau bukan pelayan → kembali ke index.php
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'pelayan') {
    header("Location: index.php");
    exit;
}

// Jika ada aksi selesai (pesanan dipindahkan ke rekap_penjualan)
if (isset($_GET['selesai']) && isset($_GET['jam'])) {
    $meja = intval($_GET['selesai']);
    $jamMenit = $_GET['jam']; // format HH:ii

    // Pindahkan hanya pesanan dengan meja & jam/menit itu
    $db->query("
        INSERT INTO rekap_penjualan (meja, product_id, jumlah, total_harga, tanggal, status)
        SELECT meja, product_id, SUM(jumlah) AS jumlah, SUM(total_harga) AS total_harga, NOW(), 'BELUM'
        FROM pesanan 
        WHERE meja = '$meja' AND DATE_FORMAT(tanggal, '%H:%i') = '$jamMenit'
        GROUP BY product_id
    ");

    // Hapus pesanan yang sudah dipindahkan
    $db->query("
        DELETE FROM pesanan 
        WHERE meja = '$meja' AND DATE_FORMAT(tanggal, '%H:%i') = '$jamMenit'
    ");

    header("Location: halaman_pelayan.php");
    exit;
}

// Ambil pesanan aktif (dari tabel pesanan)
$query = "
    SELECT 
        x.meja,
        DATE_FORMAT(x.waktu, '%H:%i') AS jam_menit,
        GROUP_CONCAT(CONCAT(m.nama, ' (', x.jumlah, ')') SEPARATOR ', ') AS daftar_pesanan,
        SUM(x.total_harga) AS total_harga
    FROM (
        SELECT 
            p.meja,
            p.product_id,
            SUM(p.jumlah) AS jumlah,
            SUM(p.total_harga) AS total_harga,
            MIN(p.tanggal) AS waktu
        FROM pesanan p
        GROUP BY p.meja, p.product_id, DATE_FORMAT(p.tanggal, '%H:%i')
    ) x
    JOIN menu m ON m.id = x.product_id
    GROUP BY x.meja, jam_menit
    ORDER BY x.meja ASC, jam_menit ASC
";
$result = $db->query($query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Elkusa Cafe</title>
    <meta http-equiv="refresh" content="5">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="assets/image/logo_cafe.png">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .page-wrapper {
            border: 2px solid #0d6efd;
            border-radius: 10px;
            padding: 20px;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .page-title {
            background-color: #0d6efd;
            color: white;
            padding: 12px;
            font-weight: bold;
            text-align: left;
            border-radius: 8px 8px 0 0;
            margin: -20px -20px 20px -20px;
        }

        .logout-btn {
            position: absolute;
            top: 20px;
            right: 40px;
        }
    </style>
</head>


<body>
    <!-- HEADER NAVBAR -->
    <?php
    include 'head_kasir_pelayan.php';
    ?>
    <!-- END HEADER -->

    <div class="container mt-5">
        <div class="page-wrapper position-relative">

            <!-- Judul -->
            <h2 class="page-title"> Pelayan</h2>
            <!-- Tabel Pesanan -->
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-primary text-center">
                    <tr>
                        <th>Meja</th>
                        <th>Jam</th>
                        <th>Daftar Pesanan</th>
                        <th>Total Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center"><?php echo $row['meja']; ?></td>
                                <td class="text-center"><?php echo $row['jam_menit']; ?></td>
                                <td><?php echo $row['daftar_pesanan']; ?></td>
                                <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                <td class="text-center">
                                    <a href="?selesai=<?php echo $row['meja']; ?>&jam=<?php echo urlencode($row['jam_menit']); ?>"
                                        class="btn btn-success btn-sm"
                                        onclick="return confirm('Pesanan meja <?php echo $row['meja']; ?> jam <?php echo $row['jam_menit']; ?> sudah selesai?')">
                                        ✅ Selesai
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Belum ada pesanan</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <hr>
        <footer class="bg-light text-center py-4">
            <p class="mb-0">© 2025 masdhanar | Elkusa Cafe </p>
        </footer>
    </div>
</body>

</html>