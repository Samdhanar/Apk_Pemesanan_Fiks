<?php
include "koneksi/connect_db.php";
date_default_timezone_set('Asia/Jakarta');
// Cek login admin
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Ambil tipe filter (default bulanan)
$tipe = $_GET['tipe'] ?? 'bulanan';
$bulan_aktif = $_GET['bulan'] ?? date('Y-m');
$hari_aktif  = $_GET['hari'] ?? date('Y-m-d');

// Kondisi WHERE sesuai tipe
if ($tipe === 'harian') {
    $where = "DATE(r.tanggal) = '$hari_aktif'";
} else {
    $where = "DATE_FORMAT(r.tanggal, '%Y-%m') = '$bulan_aktif'";
}

// === Query Menu Favorit (Top 5) ===
$q_menu = "
    SELECT m.nama, SUM(r.jumlah) AS total_jual
    FROM rekap_penjualan r
    JOIN menu m ON r.product_id = m.id
    WHERE $where 
    GROUP BY r.product_id
    ORDER BY total_jual DESC
    LIMIT 5
";
$res_menu = $db->query($q_menu);
$menu_labels = [];
$menu_values = [];
while($row = $res_menu->fetch_assoc()){
    $menu_labels[] = $row['nama'];
    $menu_values[] = $row['total_jual'];
}

// === Query Waktu Ramai (per jam) ===
$q_jam = "
    SELECT 
        HOUR(r.tanggal) AS jam, 
        COUNT(*) AS jumlah_transaksi
    FROM rekap_penjualan r
    WHERE $where
    GROUP BY HOUR(r.tanggal)
    ORDER BY jam ASC
";
$res_jam = $db->query($q_jam);
$jam_labels = [];
$jam_values = [];
while ($row = $res_jam->fetch_assoc()) {
    $jam_labels[] = $row['jam'] . ':00';
    $jam_values[] = $row['jumlah_transaksi'];
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Diagram Rekap Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container py-4">

    <!--<h2 class="mb-3">Diagram Rekap Penjualan</h2>-->

    <!-- Filter -->
    <form method="get" class="row g-2 mb-3 mt-4">
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
                <input type="date" name="hari" value="<?= $hari_aktif ?>" class="form-control" onchange="this.form.submit()">
            <?php endif; ?>
        </div>
    </form>

    <!-- Diagram -->
    <div class="row mt-5">
        <div class="col-md-6">
            <h5 class="text-center">Menu Terfavorit</h5>
            <canvas id="chartMenu"></canvas>
        </div>
        <div class="col-md-6">
            <h5 class="text-center">Waktu Ramai</h5>
            <canvas id="chartJam"></canvas>
        </div>
    </div>
    </div>  
    <!-- Chart.js -->
    <script>
        // Chart Menu Terfavorit
        new Chart(document.getElementById('chartMenu'), {
            type: 'pie',
            data: {
                labels: <?= json_encode($menu_labels) ?>,
                datasets: [{
                    data: <?= json_encode($menu_values) ?>,
                    backgroundColor: ['#FF6384','#36A2EB','#FFCE56','#4BC0C0','#9966FF']
                }]
            }
        });

        // Chart Waktu Ramai
        new Chart(document.getElementById('chartJam'), {
            type: 'line',
            data: {
                labels: <?= json_encode($jam_labels) ?>,
                datasets: [{
                    label: 'Jumlah Pesanan',
                    data: <?= json_encode($jam_values) ?>,
                    borderColor: '#36A2EB',
                    backgroundColor: 'rgba(54,162,235,0.2)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>

</body>
</html>
