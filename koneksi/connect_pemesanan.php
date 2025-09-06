<?php
session_start();
include "connect_db.php";

// pastikan data dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produk_id'])) {
    $meja   = $_POST['meja'];
    $produk = $_POST['produk_id'];
    $jumlah = $_POST['jumlah'];
    $harga  = $_POST['harga_satuan'];

    $pesanan_sukses = true; // flag untuk cek apakah ada yang gagal

    foreach ($produk as $i => $id_produk) {
        $jml   = intval($jumlah[$i]);
        $harga_satuan = intval($harga[$i]);
        $total = $jml * $harga_satuan;

        // === Ambil nama produk dulu ===
        $stmtNama = $db->prepare("SELECT nama FROM menu WHERE id = ?");
        $stmtNama->bind_param("i", $id_produk);
        $stmtNama->execute();
        $resultNama = $stmtNama->get_result();
        $rowNama = $resultNama->fetch_assoc();
        $nama = $rowNama['nama'] ?? "menu tidak dikenal";

        // === UPDATE stok di tabel menu dulu ===
        $stmt2 = $db->prepare("UPDATE menu SET stok = stok - ? 
                               WHERE id = ? AND stok >= ?");
        $stmt2->bind_param("iii", $jml, $id_produk, $jml);
        $stmt2->execute();

        if ($stmt2->affected_rows > 0) {
            // stok cukup → lanjut simpan pesanan
            $stmt = $db->prepare("INSERT INTO pesanan (meja, product_id, jumlah, total_harga, tanggal) 
                                  VALUES (?,?,?,?,NOW())");
            $stmt->bind_param("iiii", $meja, $id_produk, $jml, $total);
            $stmt->execute();
        } else {
            // stok tidak cukup → kasih notif gagal (MERAH)
            $_SESSION['notif'] = "Pesanan gagal: stok untuk menu '$nama' tidak mencukupi.";
            $_SESSION['notif_type'] = "danger"; // 🔴 merah
            header("Location: ../form_pemesanan.php");
            exit();
        }
    } // tutup foreach

    // kalau semua berhasil → notif hijau
    if ($pesanan_sukses) {
        $_SESSION['notif'] = "Pesanan berhasil, terimakasih atas pesanannya! jangan lupa bahagia";
        $_SESSION['notif_type'] = "success"; // 🟢 hijau
        header("Location: ../form_pemesanan.php");
        exit();
    }
} // tutup if request POST
