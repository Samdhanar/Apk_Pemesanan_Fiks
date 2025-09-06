<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "db_tugasakhir";

$db = new mysqli($hostname, $username, $password, $database);

if ($db->connect_error) {
    die("Koneksi gagal: " . $db->connect_error);
}

// Set timezone untuk menghindari warning
date_default_timezone_set('Asia/Jakarta');
?>