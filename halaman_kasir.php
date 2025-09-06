<?php
include "koneksi/connect_db.php";
date_default_timezone_set('Asia/Jakarta');
session_start();

// Kalau belum login atau bukan kasir → kembali ke index.php
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'kasir') {
    header("Location: index.php");
    exit;
}

// ================== PROSES PEMBAYARAN ==================
if (isset($_POST['aksi']) && $_POST['aksi'] == "bayar") {
    $meja = $_POST['meja'];
    $jam_menit = $_POST['jam_menit'];
    $bayar = intval($_POST['bayar']);
    $diskon = intval($_POST['diskon']); 

    $q = $db->query("
        SELECT SUM(total_harga) as total_harga, MAX(tanggal) as tanggal
        FROM rekap_penjualan 
        WHERE meja='$meja' AND DATE_FORMAT(tanggal, '%H:%i')='$jam_menit' AND status='BELUM'
    ");
    $d = $q->fetch_assoc();
    $total = $d['total_harga'];

    $total_setelah_diskon = $total - ($total * $diskon / 100);

    if ($bayar < $total_setelah_diskon) {
        echo "<script>alert('Uang bayar kurang dari total belanja!'); window.location='kasir.php';</script>";
        exit;
    }

    $db->query("UPDATE rekap_penjualan 
            SET status='LUNAS'
            WHERE meja='$meja' AND DATE_FORMAT(tanggal, '%H:%i')='$jam_menit' AND status='BELUM'");

    header("Location: struk.php?meja=$meja&jam_menit=$jam_menit&bayar=$bayar&diskon=$diskon");
    exit;
}

// ================== Query TAMPILAN KASIR ==================
$query = "
    SELECT 
        r.meja,
        DATE_FORMAT(r.tanggal, '%H:%i') AS jam_menit,
        GROUP_CONCAT(CONCAT(m.nama, ' (', r.jumlah, ')') SEPARATOR ', ') AS daftar_pesanan,
        SUM(r.total_harga) AS total_harga
    FROM rekap_penjualan r
    JOIN menu m ON r.product_id = m.id
    WHERE r.status = 'BELUM'
    GROUP BY r.meja, jam_menit
    ORDER BY MAX(r.tanggal) DESC
";
$result = $db->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Elkusa Cafe</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="assets/image/logo_cafe.png">
    <script>
        function pembayaran(meja, jam_menit, total) {
            let diskon = prompt("Masukkan diskon (%) jika ada:", "0");
            if (diskon === null || isNaN(diskon) || diskon < 0) diskon = 0;

            let total_setelah_diskon = total - (total * diskon / 100);

            alert("Total belanja: Rp" + total.toLocaleString() +
                "\nDiskon: " + diskon + "%" +
                "\nTotal setelah diskon: Rp" + total_setelah_diskon.toLocaleString());

            let bayar = prompt("Masukkan jumlah uang bayar (Total: Rp" + total_setelah_diskon.toLocaleString() + "):", "");
            if (bayar && !isNaN(bayar)) {
                if (parseInt(bayar) < total_setelah_diskon) {
                    alert("Uang bayar kurang dari total belanja!");
                    return;
                }
                let form = document.getElementById("formBayar");
                form.meja.value = meja;
                form.jam_menit.value = jam_menit;
                form.bayar.value = bayar;
                form.diskon.value = diskon;
                form.submit();
            }
        }

        // 🔎 Fungsi pencarian tabel
        function cariData() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll("#kasirTable tbody tr");

            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                if (text.includes(input)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }
    </script>
</head>
<body>
    <?php include "head_kasir_pelayan.php"; ?>

    <div class="bg-light">
        <form method="POST" id="formBayar" style="display:none;">
            <input type="hidden" name="aksi" value="bayar">
            <input type="hidden" name="meja">
            <input type="hidden" name="jam_menit">
            <input type="hidden" name="bayar">
            <input type="hidden" name="diskon">
        </form>

        <div class="container-lg mt-4 mb-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-start">
                    <h4 class="mb-0"> Kasir</h4>
                </div>
                <div class="card-body">

                    <!-- 🔎 Input Pencarian -->
                    <div class="mb-3">
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari meja, jam, atau pesanan..." onkeyup="cariData()">
                    </div>

                    <table class="table table-bordered table-striped align-middle" id="kasirTable">
                        <thead>
                            <tr>
                                <th class="text-center">Meja</th>
                                <th class="text-center">Jam Pesan</th>
                                <th class="text-center">Daftar Pesanan</th>
                                <th class="text-center">Total Harga</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-center"><?= $row['meja']; ?></td>
                                        <td class="text-center"><?= $row['jam_menit']; ?></td>
                                        <td><?= $row['daftar_pesanan']; ?></td>
                                        <td class="text-start">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-success btn-sm"
                                                onclick="pembayaran('<?= $row['meja']; ?>', '<?= $row['jam_menit']; ?>', <?= $row['total_harga']; ?>)">
                                                Pembayaran
                                            </button>
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
            </div>
        </div>

        <hr>
        <footer class="bg-light text-center py-4">
            <p class="mb-0">© 2025 masdhanar | Elkusa Cafe </p>
        </footer>
    </div>
</body>
</html>
