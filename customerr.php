<?php
session_start();
include 'koneksi/connect_db.php';
// Kalau belum login atau bukan admin → kembali ke index.php
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['level']) || $_SESSION['level'] != 1) {
    echo "Akses ditolak!";
    exit;
}

// Hapus user
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $db->prepare("DELETE FROM user_project WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: customerr.php");
    exit;
}

// Update user
if (isset($_POST['update'])) {
    $id       = intval($_POST['id']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $level    = intval($_POST['level']);

    $stmt = $db->prepare("UPDATE user_project SET username=?, password=?, level=? WHERE id=?");
    $stmt->bind_param("ssii", $username, $password, $level, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: customerr.php");
    exit;
}

// Ambil semua data user
$sql = "SELECT id, username, password, level FROM user_project ORDER BY id ASC";
$result = $db->query($sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Elkusa Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="assets/image/logo_cafe.png">
</head>

<body>
    <?php
    include 'header.php';
    ?>

    <div class="container-lg mt-5  ">
        <a href="register.php" class="btn btn-primary ">Tambah User</a>
    </div>

    <!-- Tabel Data User -->
    <div class="bg-light">
        <div class="container-lg bg-light mt-4">
            <div class="row">
                <div class="card shadow">
                    <div class="card-header bg-light text-dark mt-4 text-center">
                        <h4 class="mb-0">📋 Data Login Karyawan</h4>
                    </div>
                    <div class="card-body" style="display: flex;">

                        <table class="table table-bordered table-striped table-hover align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Level</th>
                                    <th width="180px">Aksi</th>
                                </tr>
                            </thead>
                            <!--Tabel menampilkan data user dari database-->
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    $no = 1;
                                    while ($row = $result->fetch_assoc()) {
                                ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= htmlspecialchars($row['username']) ?></td>
                                            <td><?= htmlspecialchars($row['password']) ?></td>
                                            <td><?= ($row['level'] == 1 ? 'Admin' : 'User') ?></td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-outline-warning btn-sm flex-fill"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editModal<?= $row['id'] ?>">
                                                        <i class="bi bi-pencil-square"></i> Edit
                                                    </button>
                                                    <a href="?delete=<?= $row['id'] ?>"
                                                        class="btn btn-outline-danger btn-sm flex-fill"
                                                        onclick="return confirm('Yakin ingin menghapus user ini?')">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Modal Edit -->
                                        <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="post">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit User</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                            <div class="mb-3">
                                                                <label>Username</label>
                                                                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($row['username']) ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label>Password</label>
                                                                <input type="password" name="password" class="form-control" value="<?= htmlspecialchars($row['password']) ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label>Level</label>
                                                                <select name="level" class="form-control">
                                                                    <option value="1" <?= $row['level'] == 1 ? 'selected' : '' ?>>Admin</option>
                                                                    <option value="2" <?= $row['level'] == 2 ? 'selected' : '' ?>>User</option>
                                                                </select>
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
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center'>Tidak ada data user</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End -->
    <!-- Footer -->
    <hr>
    <footer class="bg-light text-center py-4">
        <p class="mb-0">© 2025 masdhanar | Elkusa Cafe </p>
    </footer>
</body>

</html>