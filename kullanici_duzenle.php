<?php
session_start();
require_once 'config.php';

// Yönetici kontrolü
if (!isset($_SESSION['yonetici_id'])) {
    header("Location: cikis.php");
    exit;
}

// Kullanıcı ID al
if (!isset($_GET['kullanici_id'])) {
    header("Location: yonetici_paneli.php");
    exit;
}
$kullanici_id = intval($_GET['kullanici_id']);

// Kullanıcı bilgilerini çek
$stmt = $conn->prepare("SELECT * FROM kullanicilar WHERE kullanici_id = :id LIMIT 1");
$stmt->execute([':id' => $kullanici_id]);
$kullanici = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kullanici) {
    header("Location: yonetici_paneli.php");
    exit;
}

// Form gönderildiyse kaydet
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ad = trim($_POST['ad']);
    $soyad = trim($_POST['soyad']);
    $email = trim($_POST['email']);
    $telefon = trim($_POST['telefon']);

    $stmt = $conn->prepare("UPDATE kullanicilar SET ad=:ad, soyad=:soyad, email=:email, telefon=:telefon WHERE kullanici_id=:id");
    $stmt->execute([
        ':ad' => $ad,
        ':soyad' => $soyad,
        ':email' => $email,
        ':telefon' => $telefon,
        ':id' => $kullanici_id
    ]);

    header("Location: yonetici_paneli.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Kullanıcı Düzenle</title>
<style>
form { max-width:400px; margin:auto; margin-top:50px; background:#fff; padding:20px; border-radius:10px; box-shadow:0 0 10px #ccc; }
input { width:100%; padding:10px; margin:10px 0; border:1px solid #ccc; border-radius:5px; }
button { padding:10px 20px; background:#3498db; color:#fff; border:none; border-radius:5px; cursor:pointer; }
</style>
</head>
<body>

<form method="POST">
    <h2 style="text-align:center;">Kullanıcı Düzenle</h2>
    <label>Ad</label>
    <input type="text" name="ad" value="<?= htmlspecialchars($kullanici['ad']) ?>" required>
    <label>Soyad</label>
    <input type="text" name="soyad" value="<?= htmlspecialchars($kullanici['soyad']) ?>" required>
    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($kullanici['email']) ?>" required>
    <label>Telefon</label>
    <input type="text" name="telefon" value="<?= htmlspecialchars($kullanici['telefon']) ?>">
    <button type="submit">Kaydet</button>
</form>

</body>
</html>
