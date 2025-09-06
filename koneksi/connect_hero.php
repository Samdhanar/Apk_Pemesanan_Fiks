<?php
include "connect_db.php"; // koneksi utama

// ambil semua data dari tabel hero_text
function getHeroTexts($db) {
    $query = "SELECT * FROM hero_text ORDER BY id DESC";
    return mysqli_query($db, $query);
}

// ambil data 1 baris berdasarkan id
function getHeroById($db, $id) {
    $id = intval($id);
    $query = "SELECT * FROM hero_text WHERE id=$id LIMIT 1";
    return mysqli_query($db, $query);
}
?>
