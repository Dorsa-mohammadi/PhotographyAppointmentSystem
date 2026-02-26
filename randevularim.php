<?php
session_start();
require_once 'config.php';

// Giriş kontrolü
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: kullanici_girisi.php");
    exit();
}

$kullanici_id = $_SESSION['kullanici_id'];
$popup = "";

// Kullanıcı bilgilerini çek (isim için)
$stmt = $conn->prepare("SELECT ad, soyad FROM kullanicilar WHERE kullanici_id = :id LIMIT 1");
$stmt->execute([':id' => $kullanici_id]);
$kullanici = $stmt->fetch(PDO::FETCH_ASSOC);

// Randevu silme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['randevu_sil_id'])) {
    $randevu_id = intval($_POST['randevu_sil_id']);
    $stmt = $conn->prepare("DELETE FROM randevular WHERE randevu_id = :id AND kullanici_id = :kullanici_id");
    if ($stmt->execute([':id'=>$randevu_id, ':kullanici_id'=>$kullanici_id])) {
        $popup = "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Randevu Silindi!',
                text: 'Seçtiğiniz randevu başarıyla silindi.',
                icon: 'success',
                confirmButtonText: 'Tamam'
            }).then(() => { window.location.href = 'randevularim.php'; });
        });
        </script>
        ";
    }
}

// ---------------------------
// OTOMATİK TAMAMLANDI GÜNCELLEME
// ---------------------------
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

// Kullanıcı randevuları
$randevu_sorgu = $conn->prepare("
    SELECT r.*, p.paket_adi
    FROM randevular r
    LEFT JOIN paketler p ON p.paket_id = r.paket_id
    WHERE r.kullanici_id = :id
    ORDER BY r.tarih DESC, r.saat DESC
");
$randevu_sorgu->execute([':id' => $kullanici_id]);
$randevular = $randevu_sorgu->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Randevularım</title>
<link rel="stylesheet" href="kullanici_paneli.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
/* Tablo ve sidebar stilleri aynı */
.randevu-tablo { width:100%; border-collapse: collapse; margin-top:20px; }
.randevu-tablo th, .randevu-tablo td { border:1px solid #ddd; padding:10px; text-align:center; }
.randevu-tablo th { background:#f3f3f3; }
.sil-btn { background:#ff4444; color:#fff; border:none; padding:5px 10px; border-radius:5px; cursor:pointer; }
.sil-btn:hover { background:#cc0000; }
.sidebar { width:220px; height:100vh; background: linear-gradient(to bottom, #004d40, #04816cff); padding:20px; float:left; display:flex; flex-direction:column; border-top-right-radius:20px; border-bottom-right-radius:20px; box-shadow:3px 0 15px rgba(0,0,0,0.2); }
.sidebar .user-greeting { font-size:18px; font-weight:bold; color:#fff; margin-bottom:30px; text-align:center; }
.sidebar a, .sidebar form { margin-bottom:15px; }
.sidebar button, .sidebar a button, .sidebar form button { display:block; width:100%; padding:15px; border:none; border-radius:12px; font-size:16px; font-weight:bold; color:#fff; background: rgba(255,255,255,0.2); cursor:pointer; transition: all 0.3s ease; text-align:left; }
.sidebar button:hover, .sidebar a button:hover, .sidebar form button:hover { background: rgba(255,255,255,0.4); transform: scale(1.05); }
.main-content { margin-left:240px; padding:30px; background:#f5f6fa; min-height:100vh; }
</style>
</head>
<body>

<?= $popup ?>

<div class="sidebar">
    <div class="user-greeting">Hoşgeldiniz, <?= htmlspecialchars($kullanici['ad']) ?> </div>
    <a href="kullanici_paneli.php"><button>Profil</button></a>
    <a href="kullanici_randevu_olustur.php"><button>Randevu Oluştur</button></a>
    <a href="randevularim.php"><button>Randevularım</button></a>
    <form method="POST" action="cikis.php"><button type="submit">Çıkış</button></form>
</div>

<div class="main-content">
<h2>Randevularım</h2>

<table class="randevu-tablo">
<tr>
    <th>Tarih</th>
    <th>Saat</th>
    <th>Paket</th>
    <th>Durum</th>
    <th>Yönetici Mesajı</th>
    <th>İşlem</th>
</tr>

<?php foreach ($randevular as $r): 
    // Durum kontrolü
    switch($r['onay_durumu']) {
        case 0: $durum = "<span style='color:orange;font-weight:700;'>Beklemede</span>"; break;
        case 1: $durum = "<span style='color:green;font-weight:700;'>Onaylandı</span>"; break;
        case 2: $durum = "<span style='color:red;font-weight:700;'>Reddedildi</span>"; break;
        case 3: $durum = "<span style='color:blue;font-weight:700;'>Tamamlandı</span>"; break;
        default: $durum = "<span>—</span>"; break;
    }

    // İşlem butonu
    $islemButon = '
        <form method="POST" style="display:inline;" onsubmit="return confirm(\'Bu randevuyu silmek istediğinize emin misiniz?\');">
            <input type="hidden" name="randevu_sil_id" value="'. $r['randevu_id'] .'">
            <button type="submit" class="sil-btn">Sil</button>
        </form>
    ';
?>
<tr>
    <td><?= $r['tarih'] ?></td>
    <td><?= $r['saat'] ?></td>
    <td><?= $r['paket_adi'] ?: '—' ?></td>
    <td><?= $durum ?></td>
    <td><?= htmlspecialchars($r['yonetici_mesaji'] ?: '—') ?></td>
    <td><?= $islemButon ?></td>
</tr>
<?php endforeach; ?>

</table>

</div>
</body>
</html>
