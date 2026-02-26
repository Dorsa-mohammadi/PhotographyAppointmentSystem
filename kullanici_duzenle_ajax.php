<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

// Yönetici kontrolü
if (!isset($_SESSION['yonetici_id'])) {
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim']);
    exit;
}

// POST verilerini al
$kullanici_id = intval($_POST['kullanici_id'] ?? 0);
$ad = trim($_POST['ad'] ?? '');
$soyad = trim($_POST['soyad'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefon = trim($_POST['telefon'] ?? '');

// Basit doğrulama
if (!$kullanici_id || !$ad || !$soyad || !$email || !$telefon) {
    echo json_encode(['success' => false, 'message' => 'Tüm alanları doldurun']);
    exit;
}

// Güncelleme sorgusu
$stmt = $conn->prepare("UPDATE kullanicilar SET ad = :ad, soyad = :soyad, email = :email, telefon = :telefon WHERE kullanici_id = :id");
$update = $stmt->execute([
    ':ad' => $ad,
    ':soyad' => $soyad,
    ':email' => $email,
    ':telefon' => $telefon,
    ':id' => $kullanici_id
]);

if ($update) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Güncelleme sırasında hata oluştu']);
}
