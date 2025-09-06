<?php
session_start();
// Kalau belum login atau bukan admin → kembali ke index.php
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.php");
    exit;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eklusa Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="assets/image/logo_cafe.png">
</head>

<body style="margin-top: 0;">
    <?php
    include 'header.php';

    // 🔹 Tambahan: cek stok habis
    include 'koneksi/connect_db.php';
    $result = mysqli_query($db, "SELECT nama FROM menu WHERE stok = 0");
    $menu_habis = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $menu_habis[] = $row['nama'];
    }

    if (!empty($menu_habis)) {
        echo '<div class="alert alert-danger text-center m-3">
                <strong>Perhatian!</strong> Stok habis untuk menu: ' . implode(", ", $menu_habis) . '
              </div>';
    }
    ?>

   <!--content-->
    <?php
    include 'diagram.php';
    ?>
    <!--content end-->

    <!-- Footer -->
    <hr>
    <footer class="bg-light text-center py-4">
        <p class="mb-0">© 2025 masdhanar | Elkusa Cafe </p>
    </footer>
</body>

</html>
