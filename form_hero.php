<?php
include "koneksi/connect_hero.php";

// === PROSES TAMBAH DATA ===
if (isset($_POST['add'])) {
    $title = mysqli_real_escape_string($db, $_POST['title']);
    $subtitle = mysqli_real_escape_string($db, $_POST['subtitle']);
    mysqli_query($db, "INSERT INTO hero_text (title, subtitle) VALUES ('$title','$subtitle')");
    header("Location: hero.php"); // setelah tambah kembali ke hero.php
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Hero Text</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/image/logo_cafe.png">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            max-width: 500px;
            margin: 60px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 20px 25px;
        }
        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .form-header h4 {
            margin: 0;
            font-weight: 600;
        }
        .btn-close {
            width: 0.9rem;
            height: 0.9rem;
            background-size: 0.9rem;
        }
        .btn-primary {
            width: 100%;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <div class="form-header">
            <h4>Tambah Hero Text</h4>
            <a href="hero.php" class="btn-close" aria-label="Close"></a>
        </div>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Judul (Title)</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Sub Judul (Subtitle)</label>
                <textarea name="subtitle" class="form-control" rows="2" required></textarea>
            </div>
            <button type="submit" name="add" class="btn btn-primary">Simpan</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
