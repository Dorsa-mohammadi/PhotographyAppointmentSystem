<?php
session_start();
require_once 'config.php';

// Yönetici kontrolü
if (!isset($_SESSION['yonetici_id'])) {
    header("Location: cikis.php");
    exit;
}

if(isset($_POST['kullanici_id'])) {
    $kullanici_id = intval($_POST['kullanici_id']);
    $stmt = $conn->prepare("DELETE FROM kullanicilar WHERE kullanici_id = :id");
    $stmt->execute([':id' => $kullanici_id]);
}

header("Location: yonetici_paneli.php");
exit;
?>
