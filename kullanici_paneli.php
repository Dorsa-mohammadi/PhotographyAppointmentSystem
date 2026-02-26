<?php
session_start();

// GERİ TUŞU İLE PANELİN GÖRÜNMESİNİ ENGELLEME
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Session yoksa çıkış sayfasına gönder
if (!isset($_SESSION['kullanici_id']) || empty($_SESSION['kullanici_id'])) {
    header("Location: cikis.php");
    exit;
}

require_once 'config.php';




$kullanici_id = $_SESSION['kullanici_id'];
$popup = "";

// Kullanıcı bilgileri
$stmt = $conn->prepare("SELECT * FROM kullanicilar WHERE kullanici_id = :id LIMIT 1");
$stmt->execute([':id' => $kullanici_id]);
$kullanici = $stmt->fetch(PDO::FETCH_ASSOC);

// Paketler
$paketler = $conn->query("SELECT * FROM paketler WHERE aktif='1'")->fetchAll(PDO::FETCH_ASSOC);

// Randevu oluşturma
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['randevu_olustur'])) {
    $tarih = trim($_POST['tarih']);
    $saat = trim($_POST['saat']);
    $paket_id = !empty($_POST['paket']) ? $_POST['paket'] : NULL;
    $aciklama = trim($_POST['aciklama']);

    if (!empty($tarih) && !empty($saat)) {
        $sql = "INSERT INTO randevular (kullanici_id, paket_id, tarih, saat, aciklama, onay_durumu)
                VALUES (:kullanici_id, :paket_id, :tarih, :saat, :aciklama, 0)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':kullanici_id', $kullanici_id);
        $stmt->bindParam(':paket_id', $paket_id);
        $stmt->bindParam(':tarih', $tarih);
        $stmt->bindParam(':saat', $saat);
        $stmt->bindParam(':aciklama', $aciklama);

        if ($stmt->execute()) {
            $popup = "
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Randevunuz Oluşturuldu!',
                    text: 'Randevunuz yönetici onayına gönderildi.',
                    icon: 'success',
                    confirmButtonText: 'Tamam'
                }).then(() => { window.location.href = 'kullanici_paneli.php'; });
            });
            </script>
            ";
        } else {
            $popup = "
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Hata!',
                    text: 'Randevu oluşturulurken bir sorun oluştu.',
                    icon: 'error'
                });
            });
            </script>";
        }
    } else {
        $popup = "
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Eksik Bilgi!',
                text: 'Tarih ve saat alanları zorunludur.',
                icon: 'warning'
            });
        });
        </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Kullanıcı Paneli</title>
<link rel="stylesheet" href="kullanici_paneli.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
/* Sidebar stil */
.sidebar {
    width:220px;
    height:100vh;
    background: linear-gradient(to bottom, #004d40, #067e6aff);
    padding:20px;
    box-sizing: border-box;
    float:left;
    display:flex;
    flex-direction:column;
    align-items:stretch;
    border-top-right-radius:20px;
    border-bottom-right-radius:20px;
    box-shadow:3px 0 15px rgba(0,0,0,0.2);
}

/* Kullanıcı adı kısmı */
.sidebar .user-greeting {
    font-size:18px;
    font-weight:bold;
    color:#fff;
    margin-bottom:30px;
    text-align:center;
}

/* Sidebar butonlar */
.sidebar button, .sidebar a button, .sidebar form button {
    display:block;
    width:100%;
    padding:15px;
    border:none;
    border-radius:12px;
    font-size:16px;
    font-weight:bold;
    color:#fff;
    background: rgba(255,255,255,0.2);
    cursor:pointer;
    transition: all 0.3s ease;
    text-align:left;
    margin-bottom:15px;
}

.sidebar button:hover, .sidebar a button:hover, .sidebar form button:hover {
    background: rgba(255,255,255,0.4);
    transform: scale(1.05);
}

/* Ana içerik alanı */
.main-content {
    margin-left:240px;
    padding:30px;
    background:#f5f6fa;
    min-height:100vh;
}

/* Form tasarımı */
.randevu-container, .container {
    background:#fff;
    padding:25px;
    border-radius:10px;
    box-shadow:0 0 10px #ccc;
    width:60%;
    margin:auto;
    margin-top:40px;
}

.randevu-container h2, .container h2 {
    text-align:center;
    margin-bottom:20px;
}

.randevu-container label, .container label {
    font-weight:bold;
    display:block;
    margin-top:15px;
}

.randevu-container input, .randevu-container select, .randevu-container textarea,
.container input, .container select, .container textarea {
    width:100%;
    padding:10px;
    border:1px solid #aaa;
    border-radius:6px;
    margin-top:5px;
}

button[type="submit"] {
    margin-top:20px;
    width:100%;
    padding:12px;
    background:#2b7cff;
    color:white;
    border:none;
    border-radius:6px;
    font-size:17px;
    cursor:pointer;
}

button[type="submit"]:hover {
    background:#1f5fcc;
}

.mesaj {
    background:#ffdddd;
    padding:10px;
    border-left:5px solid #ff4444;
    margin-bottom:10px;
    border-radius:5px;
    color:#b70000;
}
</style>
</head>
<body>

<?= $popup ?>

<div class="sidebar">
    <div class="user-greeting">Hoşgeldiniz, <?= htmlspecialchars($kullanici['ad']) ?> </div>
    <button onclick="showContent('profile')">Profil</button>
    <button onclick="showContent('randevu')">Randevu Oluştur</button>
    <a href="randevularim.php"><button>Randevularım</button></a>
    <form method="POST" action="cikis.php"><button type="submit">Çıkış</button></form>
</div>

<div class="main-content">

<div id="profile">
    <h2>Merhaba, <?= htmlspecialchars($kullanici['ad']) ?> </h2>
    <div class="container">
        <form method="POST" action="update_profile.php">
            <label>Ad</label>
            <input type="text" name="ad" value="<?= htmlspecialchars($kullanici['ad']) ?>" required>

            <label>Soyad</label>
            <input type="text" name="soyad" value="<?= htmlspecialchars($kullanici['soyad']) ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($kullanici['email']) ?>" required>

            <label>Telefon</label>
            <input type="text" name="telefon" value="<?= htmlspecialchars($kullanici['telefon']) ?>">

            <label>Yeni Şifre</label>
            <input type="password" name="sifre" placeholder="Yeni şifre (opsiyonel)">

            <button type="submit">Bilgileri Güncelle</button>
        </form>
    </div>
</div>

<div id="randevu" style="display:none;">
    <div class="randevu-container">
        <h2>Yeni Randevu Oluştur</h2>

        <form method="POST" action="">
            
            <label for="tarih">Tarih</label>
            <input type="date" id="tarih" name="tarih" required>

            <label for="saat">Saat</label>
            <select id="saat" name="saat" required>
                <option value="">Önce tarih seçiniz</option>
            </select>

            <label for="paket">Paket Seçimi</label>
            <select id="paket" name="paket">
                <option value="">Seçiniz</option>

                <?php foreach ($paketler as $paket): ?>
                    <option value="<?= $paket['paket_id'] ?>">
                        <?= htmlspecialchars($paket['paket_adi']) ?> - <?= $paket['fiyat'] ?> ₺ / <?= $paket['sure_saat'] ?> saat
                    </option>
                <?php endforeach; ?>

            </select>

            <label for="aciklama">Açıklama</label>
            <textarea id="aciklama" name="aciklama" rows="4" placeholder="İsteğe bağlı"></textarea>

            <button type="submit" class="submit-btn" name="randevu_olustur">Randevu Oluştur</button>

        </form>
    </div>
</div>


</div>

<script>
function showContent(id) {
    document.getElementById('profile').style.display = 'none';
    document.getElementById('randevu').style.display = 'none';
    document.getElementById(id).style.display = 'block';
}
</script>

</body>
</html>
