<?php
session_start();

// Jika sudah login, arahkan ke halaman sesuai level
if (isset($_SESSION['username']) && isset($_SESSION['level'])) {
    switch ($_SESSION['level']) {
        case 1: // admin
            header("Location: halaman_admin.php"); //admin di arahkan ke halaman_admin.php
            exit;
        case 3: // Kasir
            header("Location: halaman_kasir.php"); //kasir di arahkan ke halaman_kasir.php
            exit;
        case 4: // Pelayan
            header("Location: halaman_pelayan.php"); //pelayan di arahkan ke halaman_pelayan.php
            exit;
        default:
            header("Location: index.php"); //jika user tidak di kenal login maka akan di arahkan ke index
            exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/image/logo_cafe.png">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: background 0.3s, color 0.3s;
        }

        .card {
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .theme-toggle {
            position: absolute;
            top: 15px;
            right: 15px;
        }
    </style>
</head>

<body class="bg-light text-dark">
    <!--tema halaman-->
    <div class="theme-toggle">
        <button id="toggleTheme" class="btn btn-outline-secondary btn-sm">
            🌙 Dark
        </button>
    </div>
    <!--form login-->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card p-4">
                    <div class="card-body text-center">
                        <h3 class="mb-4">🔐 Login</h3>
                        <form method="POST" action="koneksi/connect_login.php">
                            <div class="mb-3 text-start">
                                <label class="form-label">Username</label>
                                <input type="text" placeholder="Username" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3 text-start">
                                <label class="form-label">Password</label>
                                <input type="password" placeholder="Password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Masuk</button>
                        </form>
                        <hr>
                        <p class="small">
                            Home 
                            <a href="index.php">Kembali</a>
                        </p>
                        <small class="text-muted">© <?= date('Y'); ?> Dhanar | Elkusa Cafe </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        //js tema
        const btn = document.getElementById("toggleTheme");
        const body = document.body;

        btn.addEventListener("click", () => {
            if (body.classList.contains("bg-light")) {
                body.classList.replace("bg-light", "bg-dark");
                body.classList.replace("text-dark", "text-light");
                btn.textContent = "☀️ Light";
                btn.classList.replace("btn-outline-secondary", "btn-outline-light");
            } else {
                body.classList.replace("bg-dark", "bg-light");
                body.classList.replace("text-light", "text-dark");
                btn.textContent = "🌙 Dark";
                btn.classList.replace("btn-outline-light", "btn-outline-secondary");
            }
        });
    </script>

</body>

</html>