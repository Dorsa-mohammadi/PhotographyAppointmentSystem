<?php
session_start();
require_once 'config.php';

// Yönetici kontrolü
if (!isset($_SESSION['yonetici_id']) || empty($_SESSION['yonetici_id'])) {
    header("HTTP/1.1 403 Forbidden");
    exit("Yetkisiz erişim");
}

$yonetici_id = $_SESSION['yonetici_id'];

// ---------------------------
// 1. OTOMATİK TAMAMLANDI GÜNCELLEME
// ---------------------------
// Onaylanmış (1) ve zamanı geçmiş randevuları Tamamlandı (3) yap
try {
    $stmt = $conn->prepare("
        UPDATE randevular
        SET onay_durumu = 3
        WHERE onay_durumu = 1 AND CONCAT(tarih,' ',saat) <= NOW()
    ");
    $stmt->execute();
} catch (PDOException $e) {
    error_log("Otomatik tamamlandı hatası: " . $e->getMessage());
}

// ---------------------------
// 2. ADMIN İŞLEMLERİ
// ---------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['randevu_id']) || !is_numeric($_POST['randevu_id'])) {
        exit("Geçersiz istek");
    }

    $r_id  = intval($_POST['randevu_id']);
    $mesaj = trim($_POST['yonetici_mesaji'] ?? '');

    try {
        // Durum belirleme
        $durum = null;
        if (isset($_POST['randevu_onayla'])) {
            $durum = 1; // Onaylandı
        } elseif (isset($_POST['randevu_reddet'])) {
            $durum = 2; // Reddedildi
        } elseif (isset($_POST['randevu_sil'])) {
            $durum = 'sil';
        }

        if ($durum === 'sil') {
            // Randevuyu sil
            $stmt = $conn->prepare("DELETE FROM randevular WHERE randevu_id = :id");
            $stmt->execute([':id' => $r_id]);
        } else {
            // Onay veya Red işlemi
            $stmt = $conn->prepare("
                UPDATE randevular
                SET onay_durumu = :durum, yonetici_mesaji = :mesaj
                WHERE randevu_id = :id
            ");
            $stmt->execute([
                ':durum' => $durum,
                ':mesaj' => $mesaj,
                ':id'    => $r_id
            ]);
        }

        // İşlem tamamlandıktan sonra aynı sekmede kal
        header("Location: yonetici_paneli.php?tab=randevular");
        exit;

    } catch (PDOException $e) {
        error_log("Randevu işlem hatası: " . $e->getMessage());
        exit("Veritabanı hatası oluştu.");
    }
}
?>
