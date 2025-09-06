<?php
include "koneksi/connect_db.php";

$query = "
    SELECT 
        x.meja,
        GROUP_CONCAT(CONCAT(m.nama, ' (', x.jumlah, ')') SEPARATOR ', ') AS daftar_pesanan,
        SUM(x.total_harga) AS total_harga
    FROM (
        SELECT 
            p.meja,
            p.product_id,
            SUM(p.jumlah) AS jumlah,
            SUM(p.total_harga) AS total_harga
        FROM pesanan p
        GROUP BY p.meja, p.product_id
    ) x
    JOIN menu m ON m.id = x.product_id
    GROUP BY x.meja
    ORDER BY x.meja ASC
";

$result = $db->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td class='text-center'>{$row['meja']}</td>
                <td>{$row['daftar_pesanan']}</td>
                <td>Rp " . number_format($row['total_harga'], 0, ',', '.') . "</td>
                <td class='text-center'>
                    <a href='?selesai={$row['meja']}'
                        class='btn btn-success btn-sm'
                        onclick=\"return confirm('Pesanan meja {$row['meja']} sudah selesai?')\">
                        ✅ Selesai
                    </a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='4' class='text-center'>Belum ada pesanan</td></tr>";
}
