<?php
session_start();
require_once "config.php";

// Yönetici değilse silme ve düzenleme butonlarını gösterme
$yonetici_mi = isset($_SESSION['yetki']) && $_SESSION['yetki'] === 'admin';

// Kullanıcıları çek
$sorgu = $db->prepare("SELECT id, kullanici_adi, email, kayit_tarihi FROM kullanicilar ORDER BY id DESC");
$sorgu->execute();
$kullanicilar = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Kullanıcı Listesi</title>
<style>
table { width: 100%; border-collapse: collapse; background:white; }
th, td { padding: 10px; border: 1px solid #ddd; }
.sil-btn { padding: 6px 12px; background:#e74c3c; color:#fff; border:none; border-radius:5px; cursor:pointer; }
.duzenle-btn { padding: 6px 12px; background:#3498db; color:#fff; border:none; border-radius:5px; cursor:pointer; }
</style>
</head>
<body>

<table>
    <tr>
        <th>ID</th>
        <th>Kullanıcı Adı</th>
        <th>E-posta</th>
        <th>Kayıt Tarihi</th>
        <?php if ($yonetici_mi): ?>
        <th>İşlemler</th>
        <?php endif; ?>
    </tr>

    <?php foreach ($kullanicilar as $k): ?>
    <tr>
        <td><?= $k['id'] ?></td>
        <td><?= htmlspecialchars($k['kullanici_adi']) ?></td>
        <td><?= htmlspecialchars($k['email']) ?></td>
        <td><?= $k['kayit_tarihi'] ?></td>

        <?php if ($yonetici_mi): ?>
        <td>
            <form action="kullanici_sil.php" method="POST" style="display:inline;" onsubmit="return confirm('Silinsin mi?')">
                <input type="hidden" name="id" value="<?= $k['id'] ?>">
                <button class="sil-btn">Sil</button>
            </form>
            <a href="kullanici_duzenle.php?kullanici_id=<?= $k['id'] ?>" class="duzenle-btn">Düzenle</a>
        </td>
        <?php endif; ?>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
