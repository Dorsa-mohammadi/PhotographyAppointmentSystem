<?php
session_start();
require_once 'config.php';

// PDO hata modunu aktif et
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Kullanıcı giriş kontrolü
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: kullanici_girisi.php");
    exit();
}

$kullanici_id = $_SESSION['kullanici_id'];
$mesaj = "";
$popup = "";

// Kullanıcı bilgilerini çek
$kullanici = null;
if (isset($_SESSION['kullanici_id'])) {
    $stmt = $conn->prepare("SELECT * FROM kullanicilar WHERE kullanici_id = :id LIMIT 1");
    $stmt->execute([':id' => $_SESSION['kullanici_id']]);
    $kullanici = $stmt->fetch(PDO::FETCH_ASSOC);
}


// Paket listesi
$paketler = $conn->query("SELECT * FROM paketler WHERE aktif = 'evet'")->fetchAll(PDO::FETCH_ASSOC);

// Form gönderildiyse
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tarih = trim($_POST['tarih'] ?? '');
    $saat = trim($_POST['saat'] ?? '');
    $paket_id = !empty($_POST['paket']) ? intval($_POST['paket']) : NULL;
    $aciklama = trim($_POST['aciklama'] ?? '');

    if (empty($tarih) || empty($saat)) {
        $mesaj = "Lütfen tarih ve saat alanlarını doldurunuz.";
    } else {

        // Seçilen paketin süresini al
        $sure_saat = 1; // Varsayılan 1 saat
        if ($paket_id) {
            $paket = $conn->prepare("SELECT sure_saat FROM paketler WHERE paket_id = :id AND aktif='evet' LIMIT 1");
            $paket->execute([':id' => $paket_id]);
            $paket = $paket->fetch(PDO::FETCH_ASSOC);
            if ($paket) {
                $sure_saat = intval($paket['sure_saat']);
            }
        }

        // Yeni randevunun başlangıç ve bitiş zamanlarını datetime olarak oluştur
        $baslangic = new DateTime("$tarih $saat");
        $bitis = clone $baslangic;
        $bitis->modify("+$sure_saat hour");

        // Tüm onaylanmış randevular
        $kontrol = $conn->prepare("SELECT tarih, saat, paket_id FROM randevular WHERE onay_durumu=1");
        $kontrol->execute();
        $randevular = $kontrol->fetchAll(PDO::FETCH_ASSOC);

        $cakisma = false;
        foreach ($randevular as $r) {
            $paket_r = $conn->prepare("SELECT sure_saat FROM paketler WHERE paket_id = :id LIMIT 1");
            $paket_r->execute([':id' => $r['paket_id']]);
            $paket_r = $paket_r->fetch(PDO::FETCH_ASSOC);
            $sure_r = $paket_r ? intval($paket_r['sure_saat']) : 1;

            $r_bas = new DateTime($r['tarih'] . ' ' . $r['saat']);
            $r_bit = clone $r_bas;
            $r_bit->modify("+$sure_r hour");

            // Çakışma kontrolü: herhangi bir kesişme varsa
            if (($baslangic < $r_bit) && ($bitis > $r_bas)) {
                $cakisma = true;
                break;
            }
        }

        if ($cakisma) {
            // SweetAlert ile uyarı
            $popup = "
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: 'Hata!',
                            text: 'Seçtiğiniz tarih ve saat, mevcut onaylanmış randevularla çakışıyor.',
                            icon: 'error',
                            confirmButtonText: 'Tamam'
                        });
                    });
                </script>
            ";
        } else {
            // Randevuyu veritabanına ekle
            $sql = "INSERT INTO randevular 
                    (kullanici_id, paket_id, tarih, saat, aciklama, onay_durumu)
                    VALUES (:kullanici_id, :paket_id, :tarih, :saat, :aciklama, 0)";
            $stmt = $conn->prepare($sql);

            $stmt->bindValue(':kullanici_id', $kullanici_id, PDO::PARAM_INT);

            if ($paket_id === NULL) {
                $stmt->bindValue(':paket_id', NULL, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':paket_id', $paket_id, PDO::PARAM_INT);
            }

            $stmt->bindValue(':tarih', $tarih);
            $stmt->bindValue(':saat', $saat);
            $stmt->bindValue(':aciklama', $aciklama);

            if ($stmt->execute()) {
                // Başarılı işlem için popup mesajı
                $popup = "
                    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                title: 'Randevunuz Oluşturuldu!',
                                text: 'Onay için yöneticiyi bekleyiniz.',
                                icon: 'success',
                                confirmButtonText: 'Tamam'
                            }).then(() => {
                                window.location.href = 'randevularim.php';
                            });
                        });
                    </script>
                ";
            } else {
                $mesaj = "Randevu oluşturulurken bir hata meydana geldi.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Randevu Oluştur</title>
<link rel="stylesheet" href="kullanici_paneli.css">
<style>
/* Sidebar Tasarım */
.sidebar {
    width: 220px;
    height: 100vh;
    background: linear-gradient(to bottom, #004d40, #026857ff);
    padding: 20px;
    box-sizing: border-box;
    float: left;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    border-top-right-radius: 20px;
    border-bottom-right-radius: 20px;
    box-shadow: 3px 0 10px rgba(0,0,0,0.2);
}

.sidebar .user-greeting {
    font-size: 18px;
    font-weight: bold;
    color: #fff;
    margin-bottom: 20px;
    text-align: center;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}


.sidebar button, .sidebar a button, .sidebar form button {
    display: block;
    width: 100%;
    padding: 15px;
    margin-bottom: 15px;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: bold;
    color: #fff;
    background: rgba(255,255,255,0.2);
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: left;
}

.sidebar button:hover, .sidebar a button:hover, .sidebar form button:hover {
    background: rgba(255,255,255,0.4);
    transform: scale(1.05);
}

.main-content {
    margin-left: 240px;
    padding: 30px;
    background: #f5f6fa;
    min-height: 100vh;
}

/* Randevu Form */
.randevu-container {
    width: 60%;
    margin: auto;
    margin-top: 40px;
    background: #fff;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 0 15px #ccc;
}

.randevu-container h2 {
    text-align: center;
    margin-bottom: 20px;
}

.randevu-container label {
    font-weight: bold;
    display: block;
    margin-top: 15px;
}

.randevu-container input, 
.randevu-container select,
.randevu-container textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #aaa;
    border-radius: 6px;
    margin-top: 5px;
}

button.submit-btn {
    margin-top: 20px;
    width: 100%;
    padding: 12px;
    background: #004d40;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 17px;
    cursor: pointer;
}

button.submit-btn:hover {
    background: #004d40;
}

.mesaj {
    background: #ffdddd;
    padding: 10px;
    border-left: 5px solid #ff4444;
    margin-bottom: 10px;
    border-radius: 5px;
    color: #b70000;
}

.geri-link {
    display: block;
    text-align: center;
    margin-top: 20px;
    color: #444;
    text-decoration: none;
}
</style>
</head>
<body>

<?= $popup ?>

<div class="sidebar">
    <div class="user-greeting">
        Hoşgeldiniz, <?= htmlspecialchars($kullanici['ad'] ?? '') ?> 
    </div>
    <a href="kullanici_paneli.php"><button>Profil</button></a>
    <a href="kullanici_randevu_olustur.php"><button>Randevu Oluştur</button></a>
    <a href="randevularim.php"><button>Randevularım</button></a>
    <form method="POST" action="cikis.php"><button type="submit">Çıkış</button></form>
</div>



<div class="main-content">

    <div class="randevu-container">
        <h2>Yeni Randevu Oluştur</h2>

        <?php if (!empty($mesaj)): ?>
            <div class="mesaj"><?= $mesaj ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            
            <label for="tarih">Tarih</label>
            <input type="date" id="tarih" name="tarih" required min="<?= date('Y-m-d') ?>">


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

            <button type="submit" class="submit-btn">Randevu Oluştur</button>

        </form>

        <a href="kullanici_paneli.php" class="geri-link">← Panele Geri Dön</a>
    </div>

</div>

<script>
// Saat listesini dinamik doldur
document.getElementById("tarih").addEventListener("change", function() {
    let saatSec = document.getElementById("saat");
    saatSec.innerHTML = ""; 

    let secilenTarih = this.value;
    let bugun = new Date().toISOString().split("T")[0];
    let simdi = new Date();

    for (let h = 7; h <= 21; h++) { // 07:00 - 21:00 saatler
        for (let m of [0, 30]) { // 0 ve 30 dakika
            let hh = h.toString().padStart(2, "0");
            let mm = m.toString().padStart(2, "0");
            let saatStr = `${hh}:${mm}`;

            let option = document.createElement("option");
            option.value = saatStr;
            option.textContent = saatStr;

            // Eğer bugün seçildiyse geçmiş saat/dakikeleri kapat
            if (secilenTarih === bugun) {
                let nowHH = simdi.getHours();
                let nowMM = simdi.getMinutes();
                if (h < nowHH || (h === nowHH && m <= nowMM)) {
                    option.disabled = true;
                    option.style.color = "gray";
                }
            }

            saatSec.appendChild(option);
        }
    }
});

</script>

</body>
</html>
