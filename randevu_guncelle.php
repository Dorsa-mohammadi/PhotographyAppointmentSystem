<?php
require_once 'config.php';

// Şu anki tarih ve saat
$now = date('Y-m-d H:i');

// Henüz tamamlanmamış (onay = 1, tamamlanmadı) randevuları al ve durum güncelle
$stmt = $conn->prepare("
    UPDATE randevular
    SET onay_durumu = 3 -- 3 = Tamamlandı
    WHERE onay_durumu = 1 
      AND CONCAT(tarih, ' ', saat) <= :now
");
$stmt->execute([':now' => $now]);
?>
