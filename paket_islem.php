<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if(!isset($_SESSION['yonetici_id'])){
    echo json_encode(['success'=>false,'message'=>'Yetkisiz erişim']); exit;
}

$id = $_POST['paket_id'] ?? null;
$paket_adi = $_POST['paket_adi'];
$aciklama = $_POST['aciklama'] ?? null;
$fiyat = $_POST['fiyat'];
$sure_saat = $_POST['sure_saat'];
$aktif = $_POST['aktif'];

try{
    if($id){ // Güncelle
        $stmt = $conn->prepare("UPDATE paketler SET paket_adi=:paket_adi, aciklama=:aciklama, fiyat=:fiyat, sure_saat=:sure_saat, aktif=:aktif WHERE paket_id=:id");
        $stmt->execute([':paket_adi'=>$paket_adi,':aciklama'=>$aciklama,':fiyat'=>$fiyat,':sure_saat'=>$sure_saat,':aktif'=>$aktif,':id'=>$id]);
        echo json_encode(['success'=>true,'message'=>'Paket güncellendi']);
    } else { // Ekle
        $stmt = $conn->prepare("INSERT INTO paketler (paket_adi,aciklama,fiyat,sure_saat,aktif) VALUES (:paket_adi,:aciklama,:fiyat,:sure_saat,:aktif)");
        $stmt->execute([':paket_adi'=>$paket_adi,':aciklama'=>$aciklama,':fiyat'=>$fiyat,':sure_saat'=>$sure_saat,':aktif'=>$aktif]);
        echo json_encode(['success'=>true,'message'=>'Paket eklendi']);
    }
}catch(Exception $e){
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
