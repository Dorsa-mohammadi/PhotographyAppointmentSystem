<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if(!isset($_SESSION['yonetici_id'])) {
    echo json_encode(['success'=>false]);
    exit;
}

if(isset($_POST['kullanici_id'])) {
    $kullanici_id = intval($_POST['kullanici_id']);

    try {
        $conn->beginTransaction();

        // Kullanıcının tüm randevularını sil
        $stmt1 = $conn->prepare("DELETE FROM randevular WHERE kullanici_id = :id");
        $stmt1->execute([':id' => $kullanici_id]);

        // Kullanıcıyı sil
        $stmt2 = $conn->prepare("DELETE FROM kullanicilar WHERE kullanici_id = :id");
        $stmt2->execute([':id' => $kullanici_id]);

        $conn->commit();
        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success'=>false]);
?>
