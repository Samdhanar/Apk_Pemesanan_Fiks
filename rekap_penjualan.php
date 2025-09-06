<?php
session_start();
include "koneksi/connect_db.php";
date_default_timezone_set('Asia/Jakarta');

// Kalau belum login atau bukan admin → kembali ke index.php
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// === Ambil tipe rekap ===
$tipe = $_GET['tipe'] ?? 'bulanan';
$bulan_aktif = $_GET['bulan'] ?? date('Y-m');
$tanggal_aktif = $_GET['tanggal'] ?? date('Y-m-d');

// === Buat filter SQL ===
$where = "";
if ($tipe == "bulanan") {
    $where = "DATE_FORMAT(r.tanggal, '%Y-%m') = '$bulan_aktif'";
} else {
    $where = "DATE(r.tanggal) = '$tanggal_aktif'";
}


// === Query data utama, dikelompokkan per meja + jam+menit ===
$query = "
    SELECT 
        r.meja,
        DATE_FORMAT(r.tanggal, '%H:%i') AS jam_menit,
        GROUP_CONCAT(CONCAT(m.nama, ' (', r.jumlah, ')') SEPARATOR ', ') AS menu_list,
        SUM(r.jumlah) AS total_jumlah,
        SUM(r.total_harga) AS total_harga
    FROM rekap_penjualan r
    JOIN menu m ON r.product_id = m.id
    WHERE $where AND r.status = 'BELUM'
    GROUP BY r.meja, DATE_FORMAT(r.tanggal, '%H:%i')
    ORDER BY r.meja ASC, r.tanggal ASC
";

$result = $db->query($query);

?>
<!DOCTYPE html>
<html>

<head>
    <title>Elkusa Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="assets/image/logo_cafe.png">
</head>

<body>
    <?php include "header.php"; ?>

    <div class="container mt-4">

        <h3 class="mb-4 text-center">Rekap Pesanan Belum Lunas</h3>

        <!-- Filter Form -->
        <form method="get" class="row g-2 mb-3">
            <div class="col-auto">
                <select name="tipe" class="form-select" onchange="this.form.submit()">
                    <option value="bulanan" <?= $tipe == 'bulanan' ? 'selected' : '' ?>>Bulanan</option>
                    <option value="harian" <?= $tipe == 'harian' ? 'selected' : '' ?>>Harian</option>
                </select>
            </div>
            <div class="col-auto">
                <?php if ($tipe == 'bulanan'): ?>
                    <input type="month" name="bulan" value="<?= $bulan_aktif ?>" class="form-control" onchange="this.form.submit()">
                <?php else: ?>
                    <input type="date" name="tanggal" value="<?= $tanggal_aktif ?>" class="form-control" onchange="this.form.submit()">
                <?php endif; ?>
            </div>
        </form>

        <!-- Tabel Data -->
        <div class="table-responsive mt-4">
            <table class="table table-bordered table-striped">
                <thead class="table-primary text-center">
                    <tr>
                        <th>Meja</th>
                        <th>Jam</th>
                        <th>Menu</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center"><?= $row['meja'] ?></td>
                                <td class="text-center"><?= $row['jam_menit'] ?></td>
                                <td><?= $row['menu_list'] ?></td>
                                <td class="text-center"><?= $row['total_jumlah'] ?></td>
                                <td class="text-end"><?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
    <hr class="mt-5">
    <footer class="text-center py-4 mt-4">
        <p class="mb-0">© 2025 masdhanar | Elkusa Cafe</p>
    </footer>
</body>

</html>