<?php
session_start();

// Hapus semua session
session_unset(); //menghancukan variabel
session_destroy(); //menghancurkan data login

// log out berhasil Arahkan ke halaman login
header("Location: login.php");
exit();
