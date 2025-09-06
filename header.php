        <nav class="navbar navbar-expand-lg bg-light border sticky-top mt-0">
            <div class="container-lg">
                <a class="navbar-brand d-flex align-items-center" href="halaman_admin.php">
                    <img src="assets/image/logo_cafe.png" alt="Elkusa Cafe" height="70" class="me-2">
                    <span>Elkusa Cafe</span>
                </a>

                <!-- Tombol Burger -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                    aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'halaman_admin.php') ? 'active' : ''; ?>"
                                href="../halaman_admin.php"><i class="bi bi-house-door-fill"></i> Beranda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'customerr.php') ? 'active' : ''; ?>"
                                href="customerr.php"><i class="bi bi-bar-chart-steps"></i> Data</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'hero.php') ? 'active' : ''; ?>"
                                href="hero.php"><i class="bi bi-sliders"></i> Hero</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'product_admin.php') ? 'active' : ''; ?>"
                                href="product_admin.php"><i class="bi bi-box-seam"></i> Produk</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Penjualan
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end mt-2">
                                <li><a class="dropdown-item" href="rekap_pesanan_lunas.php"> Lunas</a></li>
                                <li><a class="dropdown-item" href="rekap_penjualan.php"> Belum Lunas</a></li>
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo $_SESSION['username']; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end mt-2">
                                <li><a class="dropdown-item" href="#"><i class="bi bi-person-circle"></i> Profile</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-gear-fill"></i> Settings</a></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-left"></i> Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- CSS untuk highlight -->
        <style>
            .dropdown-menu {
                z-index: 9999 !important;
            }

            /* Style default link navbar */
            .navbar .nav-link {
                color: #000000ff !important;
                /* default hitam */
                font-weight: 500;
                padding-bottom: 4px;
                border-bottom: 3px solid transparent;
                /* untuk efek garis bawah */
                transition: all 0.3s ease;
            }

            /* Hover link */
            .navbar .nav-link:hover {
                color: #0d6efd !important;
            }

            /* Link aktif */
            .navbar .nav-link.active {
                color: #0d6efd !important;
                /* biru */
                border-bottom: 3px solid #0d6efd;
                /* garis bawah */
                background: none !important;
                /* hilangkan background biru kotak */
            }
        </style>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>