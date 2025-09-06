<?php
include "koneksi/connect_hero.php";
session_start();

// === PROSES UPDATE DATA ===
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $title = mysqli_real_escape_string($db, $_POST['title']);
    $subtitle = mysqli_real_escape_string($db, $_POST['subtitle']);
    mysqli_query($db, "UPDATE hero_text SET title='$title', subtitle='$subtitle' WHERE id=$id");
    header("Location: hero.php");
    exit;
}

// === PROSES HAPUS DATA ===
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($db, "DELETE FROM hero_text WHERE id=$id");
    header("Location: hero.php");
    exit;
}

// === AMBIL DATA ===
$result = getHeroTexts($db);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Hero Text</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="assets/image/logo_cafe.png">
    <style>
        /* Samakan panjang tombol Edit & Hapus */
        .btn-action {
            min-width: 90px;
            /* bisa diubah sesuai kebutuhan */
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <?php include "header.php"; ?>

    <div class="container-lg">
        <div class="row">
          <!-- Konten -->
            <div class="mt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Kelola Hero Text</h2>
                    <a href="form_hero.php" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Tambah Hero Text
                    </a>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-striped align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Title</th>
                                        <th>Subtitle</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                        <tr>
                                            <td><?= $row['title'] ?></td>
                                            <td><?= $row['subtitle'] ?></td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button class="btn btn-outline-warning btn-sm flex-fill"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#edit<?= $row['id'] ?>">
                                                        <i class="bi bi-pencil-square"></i> Edit
                                                    </button>
                                                    <a href="hero.php?delete=<?= $row['id'] ?>"
                                                        onclick="return confirm('Yakin hapus data ini?')"
                                                        class="btn btn-outline-danger btn-sm flex-fill">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Modal Edit -->
                                        <div class="modal fade" id="edit<?= $row['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form method="POST">
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Hero Text</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                            <div class="mb-3">
                                                                <label class="form-label">Judul (Title)</label>
                                                                <input type="text" name="title" class="form-control" value="<?= $row['title'] ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Sub Judul (Subtitle)</label>
                                                                <textarea name="subtitle" class="form-control" rows="2" required><?= $row['subtitle'] ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" name="update" class="btn btn-success">Simpan</button>
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>