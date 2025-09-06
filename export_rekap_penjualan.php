<?php
include "koneksi/connect_db.php";
session_start();
// Kalau belum login atau bukan admin → kembali ke index.php
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Ambil parameter URL 
$tipe = $_GET['tipe'] ?? 'harian';
$bulan = $_GET['bulan'] ?? date('Y-m');
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');

// Rekap (bulanan/harian)
$where = "";
if ($tipe == 'harian') {
    $where = "DATE(r.tanggal) = '$tanggal'";
} else if ($tipe == 'bulanan') {
    $where = "DATE_FORMAT(r.tanggal, '%Y-%m') = '$bulan'";
}

// Query: kelompokkan per jam:menit
$query = "
  SELECT 
        DATE_FORMAT(r.tanggal, '%d-%m-%Y %H:%i') AS waktu,
        GROUP_CONCAT(CONCAT(m.nama, ' (', r.jumlah, ')') SEPARATOR ', ') AS menu,
        SUM(r.jumlah) AS total_jumlah,
        SUM(r.total_harga) AS total_harga
    FROM rekap_penjualan r
    JOIN menu m ON r.product_id = m.id
    WHERE $where AND r.status = 'LUNAS'
    GROUP BY DATE_FORMAT(r.tanggal, '%Y-%m-%d %H:%i')
    ORDER BY r.tanggal ASC
";
$result = $db->query($query);

// Export ke Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=rekap_bulanan.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";
echo "<tr>
        <th>Waktu</th>
        <th>Menu</th>
        <th>Total Jumlah</th>
        <th>Total Harga</th>
      </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>".$row['waktu']."</td>";
    echo "<td>".$row['menu']."</td>";
    echo "<td>".$row['total_jumlah']."</td>";
    echo "<td>".$row['total_harga']."</td>";
    echo "</tr>";
}
echo "</table>";
exit;
?>
