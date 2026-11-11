<?php
session_start();

if (isset($_SESSION['login'])) {
    echo "<h1>Selamat Datang " . htmlspecialchars($_SESSION['login']) . "</h1>";
    echo "<h2>Halaman ini hanya bisa diakses jika Anda sudah login</h2>";
    echo "<h2>Klik <a href='session3.php'>di sini (session3.php)</a> untuk LOGOUT</h2>";
} else {
    die("Anda belum login! Anda tidak berhak masuk ke halaman ini. Silahkan login <a href='session1.php'>di sini</a>");
}
?>