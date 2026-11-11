<?php
session_start();

if (isset($_SESSION['login'])) {
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
    
    echo "<h1>Anda sudah berhasil LOGOUT</h1>";
    echo "<h2>Klik <a href='session1.php'>di sini</a> untuk LOGIN kembali</h2>";  
    echo "<h2>Anda sekarang tidak bisa masuk ke halaman <a href='session2.php'>session2.php</a> lagi</h2>";
} else {
    echo "<h3>Anda belum login atau sudah melakukan logout sebelumnya.</h3>";
    echo "<h2>Silakan login kembali melalui <a href='session1.php'>halaman login</a>.</h2>";
}
?>