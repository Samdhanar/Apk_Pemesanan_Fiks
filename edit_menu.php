<?php
include "koneksi/connect_db.php";
session_start();
// Kalau belum login atau bukan admin → kembali ke index.php
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.php");
    exit;
}
$id = intval($_GET['id']); // ambil id produk
$query = mysqli_query($db, "SELECT * FROM menu WHERE id=$id");
$produk = mysqli_fetch_assoc($query);

if (!$produk) {
    echo "Produk tidak ditemukan!";
    exit;
}

// === PROSES UPDATE ===
if (isset($_POST['update'])) {
    $nama   = $_POST['nama'];
    $harga  = $_POST['harga'];
    $stok   = $_POST['stok'];
    $gambar = $produk['gambar']; // default pakai gambar lama
    $deskripsi = $_POST['deskripsi']; // deskripsi produk

    // jika ada upload gambar baru
    if (!empty($_FILES['gambar']['name'])) {
        $targetDir = "koneksi/unggahan/";
        $fileName  = time() . "_" . basename($_FILES["gambar"]["name"]);
        $targetFile = $targetDir . $fileName;

        // hapus gambar lama
        if (file_exists($targetDir . $produk['gambar'])) {
            unlink($targetDir . $produk['gambar']);
        }

        move_uploaded_file($_FILES["gambar"]["tmp_name"], $targetFile);
        $gambar = $fileName;
    }

    // update ke database
    $sql = "UPDATE menu SET nama='$nama', harga='$harga', stok='$stok', gambar='$gambar', deskripsi='$deskripsi' WHERE id=$id";
    mysqli_query($db, $sql);

    header("Location: product_admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Produk</title>
    <link rel="icon" type="image/png" href="assets/image/logo_cafe.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }

        .container {
            width: 400px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            position: relative;
        }

        input,
        button {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
        }

        img {
            width: 120px;
            margin: 10px 0;
            border-radius: 6px;
        }

        button {
            background: #3498db;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 6px;
        }

        button:hover {
            background: #2980b9;
        }

        .close-btn {
            position: absolute;
            right: 15px;
            top: 15px;
            text-decoration: none;
            font-size: 22px;
            font-weight: bold;
            color: #888;
        }

        .close-btn:hover {
            color: red;
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="product_admin.php" class="close-btn">✖</a>
        <h2>Edit Produk</h2>
        <form method="POST" enctype="multipart/form-data">

            <!--input Nama Product baru-->
            <label>Nama Produk</label>
            <input type="text" name="nama" value="<?php echo $produk['nama']; ?>" required>

            <!--input Harga Product baru-->
            <label>Harga</label>
            <input type="number" name="harga" value="<?php echo $produk['harga']; ?>" required>

            <!-- Tambahan: Deskripsi -->
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <input type="text" name="deskripsi" class="form-control" min="0" value="<?php echo $produk['deskripsi']; ?>" required>
            </div>

            <!-- Tambahan: Stok -->
            <div class="mb-3">
                <label class="form-label">Stok</label>
                <input type="number" name="stok" class="form-control" min="0" value="<?php echo $produk['stok']; ?>" required>
            </div>

            <!--previews gambar lama-->
            <label>Gambar Sekarang</label><br>
            <img id="preview" src="koneksi/unggahan/<?php echo $produk['gambar']; ?>" alt="Gambar Produk"><br>

            <!--Upload Gambar-->
            <input type="file" name="gambar" id="gambarInput" accept="image/*">

            <!-- Tombol Update -->
            <button type="submit" name="update">Update</button>
        </form>
    </div>

    <script>
        // Script untuk ubah preview gambar baru ketik sudah di ganti
        document.getElementById("gambarInput").addEventListener("change", function(event) {
            const [file] = event.target.files;
            if (file) {
                document.getElementById("preview").src = URL.createObjectURL(file);
            }
        });
    </script>
</body>

</html>