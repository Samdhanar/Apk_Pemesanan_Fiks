<?php
session_start();
include "connect_db.php";

$username = $_POST['username'];
$password = $_POST['password'];

// Query user
$sql = "SELECT * FROM user_project WHERE username='$username' AND password='$password'";
$result = mysqli_query($db, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    // Set session
    $_SESSION['username'] = $row['username'];
    $_SESSION['level'] = $row['level']; // ini angka (1,2,3,4)

    // Redirect sesuai level
    switch ($_SESSION['level']) {
        case 1: // admin
            header("Location: ../halaman_admin.php");
            exit;
        case 3: // kasir
            header("Location: ../halaman_kasir.php");
            exit;
        case 4: // pelayan
            header("Location: ../halaman_pelayan.php");
            exit;
        default:
            header("Location: ../index.php");
            exit;
    }
} else {
    header("Location: ../login.php?error=1");
    exit;
}
