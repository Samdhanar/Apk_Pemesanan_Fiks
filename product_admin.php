<?php
include "koneksi/connect_db.php";
session_start();

// Kalau belum login atau bukan admin → kembali ke index.php
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// ================== PROSES HAPUS PRODUK ==================
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $query = mysqli_query($db, "SELECT gambar FROM menu WHERE id=$id");
    $row = mysqli_fetch_assoc($query);

    if ($row && !empty($row['gambar']) && file_exists("koneksi/unggahan/" . $row['gambar'])) {
        unlink("koneksi/unggahan/" . $row['gambar']);
    }

    mysqli_query($db, "DELETE FROM menu WHERE id=$id");
    header("Location: product_admin.php?msg=deleted");
    exit;
}

// ================== PROSES UPDATE PRODUK ==================
if (isset($_POST['update'])) {
    $id       = intval($_POST['id']);
    $nama     = $_POST['nama'];
    $harga    = $_POST['harga'];
    $stok     = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];

    // Ambil data lama
    $qOld = mysqli_query($db, "SELECT * FROM menu WHERE id=$id");
    $produk = mysqli_fetch_assoc($qOld);
    $gambar  = $produk['gambar'];

    // Jika ada upload gambar baru
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

    // update database
    $sql = "UPDATE menu 
            SET nama='$nama', harga='$harga', stok='$stok', gambar='$gambar', deskripsi='$deskripsi' 
            WHERE id=$id";
    mysqli_query($db, $sql);

    header("Location: product_admin.php?msg=updated");
    exit;
}

// ================== SEARCH & FILTER KATEGORI ==================
$search   = isset($_GET['search']) ? trim($_GET['search']) : "";
$kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : "";

$whereSQL = "";

if ($search) {
    $whereSQL = "WHERE nama LIKE '%" . mysqli_real_escape_string($db, $search) . "%'";
} elseif ($kategori) {
    $whereSQL = "WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "'";
}

// Ambil semua produk sesuai kondisi
$produk = mysqli_query($db, "SELECT * FROM menu $whereSQL ORDER BY id DESC");

// Ambil daftar kategori unik
$kategori_list = mysqli_query($db, "SELECT DISTINCT kategori FROM menu WHERE kategori IS NOT NULL AND kategori <> '' ORDER BY kategori ASC");
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Elkusa Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="assets/image/logo_cafe.png">
    <style>
        /* 🔹 Search Box Besar */
        #searchInput {
            height: 60px;
            font-size: 1.2rem;
            padding: 12px 18px;
            border: 2px solid #000000ff;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        #searchInput:focus {
            border: 3px solid #3858c2ff;
            box-shadow: 0 0 8px rgba(0, 91, 187, 0.5);
            outline: none;
        }

        /* 🔹 Dropdown kategori besar */
        .form-select-lg {
            height: 55px;
            font-size: 1.1rem;
            margin-top: 8px;
        }

        /* 🔹 Kartu Produk */
        .card-produk {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-produk:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(36, 104, 194, 0.2);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
            border-top-left-radius: .5rem;
            border-top-right-radius: .5rem;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container my-5 mt-5">
        <h2 class="text-center mb-4">Daftar Menu Produk</h2>

        <!-- 🔹 Tombol Tambah & Search -->
        <div class="row mb-4 mt-4 align-items-start">
            <!-- Tombol Tambah -->
            <div class="col-md-3 col-12 mb-2 mb-md-0 mt-5">
                <a href="form_menu.php" class="btn btn-success btn-lg w-100">
                    <i class="bi bi-plus-circle"></i> Tambah Produk
                </a>
            </div>

            <!-- Search + Kategori -->
            <div class="col-md-6 col-12 ms-auto mt-5">
                <!-- Box Search -->
                <input type="text"
                    class="form-control form-control-lg mb-2"
                    placeholder="Cari produk..."
                    id="searchInput">

                <!-- Box Kategori -->
                <div class="d-flex justify-content-end mt-2">
                    <select id="kategoriFilter" class="form-select w-auto">
                        <option value="">Semua Kategori</option>
                        <option value="makanan">Makanan</option>
                        <option value="minuman">Minuman</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 🔹 JavaScript Filter -->
        <script>
            const searchInput = document.getElementById("searchInput");
            const kategoriFilter = document.getElementById("kategoriFilter");

            function filterProduk() {
                let keyword = searchInput.value.toLowerCase();
                let kategori = kategoriFilter.value.toLowerCase();

                document.querySelectorAll("#daftarMenu .produk-item").forEach(item => {
                    let nama = item.querySelector(".card-title").textContent.toLowerCase();
                    let kategoriItem = item.getAttribute("data-kategori")?.toLowerCase();

                    let cocokNama = nama.includes(keyword);
                    let cocokKategori = !kategori || kategoriItem === kategori;

                    item.style.display = (cocokNama && cocokKategori) ? "block" : "none";
                });
            }

            searchInput.addEventListener("input", filterProduk);
            kategoriFilter.addEventListener("change", filterProduk);
        </script>

        <!-- 🔹 Card Produk -->
        <div class="row g-4 mt-3" id="daftarMenu">
            <?php if (mysqli_num_rows($produk) > 0) {
                while ($p = mysqli_fetch_assoc($produk)) { ?>
                    <div class="col-md-4 col-lg-3 produk-item" data-kategori="<?= strtolower($p['kategori']); ?>">
                        <div class="card card-produk h-100 shadow-sm">
                            <img src="koneksi/unggahan/<?= $p['gambar']; ?>"
                                class="card-img-top"
                                alt="<?= $p['nama']; ?>">
                            <div class="card-body text-center d-flex flex-column">
                                <h5 class="card-title"><?= $p['nama']; ?></h5>
                                <p class="card-text text-primary fw-bold">
                                    Rp. <?= number_format($p['harga'], 0, ',', '.'); ?>
                                </p>
                                <p class="btn btn-primary btn-stock">
                                    Stok: <?= $p['stok']; ?>
                                </p>
                                <div class="mt-auto d-flex justify-content-between">
                                    <!-- Tombol Update buka modal -->
                                    <button class="btn btn-warning btn-sm w-50 me-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal<?= $p['id']; ?>">
                                        Update
                                    </button>
                                    <a href="product_admin.php?delete=<?= $p['id']; ?>"
                                        class="btn btn-danger btn-sm w-50 ms-1"
                                        onclick="return confirm('Yakin mau hapus produk ini?')">Hapus</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 🔹 Modal Edit Produk -->
                    <div class="modal fade" id="editModal<?= $p['id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Produk <?= $p['nama']; ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $p['id']; ?>">

                                        <div class="mb-3">
                                            <label>Nama Produk</label>
                                            <input type="text" name="nama" class="form-control" value="<?= $p['nama']; ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label>Harga</label>
                                            <input type="number" name="harga" class="form-control" value="<?= $p['harga']; ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label>Deskripsi</label>
                                            <input type="text" name="deskripsi" class="form-control" value="<?= $p['deskripsi']; ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label>Stok</label>
                                            <input type="number" name="stok" class="form-control" value="<?= $p['stok']; ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label>Gambar Sekarang</label><br>
                                            <img src="koneksi/unggahan/<?= $p['gambar']; ?>" alt="<?= $p['nama']; ?>" width="120" class="rounded mb-2">
                                            <input type="file" name="gambar" accept="image/*" class="form-control">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" name="update" class="btn btn-primary">Update</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php }
            } else { ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center">
                        Tidak ada produk ditemukan.
                    </div>
                </div>
            <?php } ?>
        </div>

    </div>

    <hr>
    <footer class="bg-light text-center py-4">
        <p class="mb-0">© 2025 masdhanar | Elkusa Cafe </p>
    </footer>
</body>

</html>
