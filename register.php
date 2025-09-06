<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
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

  <!-- Tombol toggle tema -->
  <div class="theme-toggle">
    <button id="toggleTheme" class="btn btn-outline-secondary btn-sm">
      🌙 Dark
    </button>
  </div>

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card p-4">
          <div class="card-body text-center">
            <h3 class="mb-4">📝 Register User</h3>
            <form action="koneksi/connect_cread.php" method="POST">
              <div class="mb-3 text-start">
                <label class="form-label">Username</label>
                <input type="text" placeholder="Username" name="username" class="form-control" required>
              </div>
              <div class="mb-3 text-start">
                <label class="form-label">Password</label>
                <input type="password" placeholder="Password" name="password" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-success w-100 mb-4">Daftar</button>
            </form>
            <hr>
            <p class="small">
              Sudah punya akun?
              <a href="login.php">Login di sini</a>
            </p>
            <footer class="bg-light text-center py-4">
              <p class="mb-0">© 2025 masdhanar | Elkusa Cafe</p>
            </footer>
          </div>
        </div>
      </div>
    </div>
  </div>


  <script>
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