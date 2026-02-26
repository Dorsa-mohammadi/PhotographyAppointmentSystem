<?php
session_start();

// Tüm session verilerini temizle
session_unset();

// Session'ı tamamen sonlandır
session_destroy();

// Session cookie'sini sıfırla
setcookie(session_name(), '', time() - 3600);

// Çıkıştan sonra ana sayfaya yönlendir
header("Location: index.php");
exit;
