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
    <title>Tambah Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/image/logo_cafe.png">
    <style>
        .preview-img {
            max-width: 100px;
            max-height: 100px;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-left: 10px;
            object-fit: cover;
        }
    </style>
</head>

<body class="bg-light">
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow rounded-3" style="max-width: 500px; width:100%;">
            <div class="card-body">
                <!-- Judul -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Tambah Menu</h4>
                    <a href="product_admin.php" class="btn-close"></a>
                </div>

                <!-- Form -->
                <form action="koneksi/connect_cread_menu.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input type="number" name="harga" class="form-control" required>
                    </div>

                    <!-- Tambahan: Kategori -->
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="kategori" class="form-select" required>
                            <option value="">Pilih Kategori</option>
                            <option value="Makanan">Makanan</option>
                            <option value="Minuman">Minuman</option>
                        </select>
                    </div>

                    <!-- Tambahan: Deskripsi -->
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <input type="text" name="deskripsi" class="form-control" required>
                    </div>

                    <!-- Tambahan: Stok -->
                    <div class="mb-3">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stok" class="form-control" min="0" value="0" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Gambar</label>
                        <div class="d-flex align-items-center">
                            <input type="file" name="gambar" class="form-control" accept="image/*" onchange="previewImage(event)">
                            <img id="preview" class="preview-img d-none">
                        </div>
                    </div>

                    <!-- Tombol -->
                    <button type="submit" class="btn btn-primary w-100">Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <!--menampilkan previews gambar yang sudah di tambahkan-->
    <script>
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('preview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = "";
                preview.classList.add('d-none');
            }
        }
    </script>
</body>

</html>