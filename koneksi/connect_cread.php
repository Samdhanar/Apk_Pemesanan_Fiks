<?php
include "connect_db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Tentukan level otomatis berdasarkan username
    switch (strtolower($username)) {
        case 'admin':
            $level = 1;
            break;
        case 'kasir':
            $level = 3;
            break;
        case 'pelayan':
            $level = 4;
            break;
        default:
            $level = 5;
            break;
    }

    // Simpan password polos (tanpa hash)
    $stmt = $db->prepare("INSERT INTO user_project (username, password, level) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $username, $password, $level);

    if ($stmt->execute()) {
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='../login.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
