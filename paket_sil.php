<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if(!isset($_SESSION['yonetici_id'])){
    echo json_encode(['success'=>false,'message'=>'Yetkisiz erişim']); exit;
}

$id = $_POST['paket_id'] ?? null;
if(!$id){
    echo json_encode(['success'=>false,'message'=>'ID eksik']); exit;
}

try{
    $stmt = $conn->prepare("DELETE FROM paketler WHERE paket_id=:id");
    $stmt->execute([':id'=>$id]);
    echo json_encode(['success'=>true]);
}catch(Exception $e){
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
