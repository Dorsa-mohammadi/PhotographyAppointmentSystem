<?php
// Site bilgileri (localhost için)
$site_url = "http://localhost/NevaFoto/"; // Proje klasör adı 'NevaFoto' ise doğru

define("SITE_URL", $site_url);
define("TITLE", "Neva Foto Stüdyosu");
define("RESIMLER", $site_url . "assets/images/");

// Fonksiyon ve DB dosyaları
require_once 'includes/db.php';
require_once 'includes/fonksiyonlar.php';

// Fonksiyon sınıfı
$FONK = new FONK();
?>
