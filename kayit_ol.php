<?php
require_once 'config.php';

$ad = $soyad = $email = $telefon = '';
$mesaj = '';
$hatalar = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ad = trim($_POST["ad"]);
    $soyad = trim($_POST["soyad"]);
    $email = trim($_POST["email"]);
    $telefon = trim($_POST["telefon"]);
    $sifre = trim($_POST["sifre"]);
    $sifre_tekrar = trim($_POST["sifre_tekrar"]);

    // Hata kontrolleri
    if (empty($ad)) $hatalar['ad'] = "Ad alanı boş bırakılamaz.";
    if (empty($soyad)) $hatalar['soyad'] = "Soyad alanı boş bırakılamaz.";
    if (empty($email)) $hatalar['email'] = "E-posta alanı boş bırakılamaz.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $hatalar['email'] = "Geçerli bir e-posta adresi giriniz.";

    if (empty($telefon)) $hatalar['telefon'] = "Telefon alanı boş bırakılamaz.";
    elseif (!preg_match("/^[0-9]{10,11}$/", $telefon)) $hatalar['telefon'] = "Telefon 10 veya 11 rakam olmalı.";

    if (empty($sifre)) $hatalar['sifre'] = "Şifre alanı boş bırakılamaz.";
    if ($sifre !== $sifre_tekrar) $hatalar['sifre_tekrar'] = "Şifreler birbiriyle eşleşmiyor.";

    // Eğer hata yoksa veritabanına ekle
    if (empty($hatalar)) {
        $hashliSifre = password_hash($sifre, PASSWORD_DEFAULT);

        try {
            $sorgu = $FONK->db->prepare("INSERT INTO kullanicilar (ad, soyad, email, telefon, sifre) VALUES (?, ?, ?, ?, ?)");
            $ekle = $sorgu->execute([$ad, $soyad, $email, $telefon, $hashliSifre]);

            if ($ekle) {
                $mesaj = "Kayıt başarılı! Giriş sayfasına yönlendiriliyorsunuz...";
                header("refresh:2;url=kullanici_girisi.php");
            } else {
                $mesaj = "Kayıt sırasında bir hata oluştu. Lütfen tekrar deneyiniz.";
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $mesaj = "Bu e-posta adresi zaten kayıtlı.";
            } else {
                $mesaj = "Veritabanı hatası: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kayıt Ol - Neva Foto Stüdyosu</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <script>
    function validateInput(field, messageId, regex=null) {
      const value = field.value.trim();
      const mesaj = document.getElementById(messageId);

      if (value === "") {
        mesaj.textContent = "Bu alan boş bırakılamaz.";
        return false;
      } else if (regex && !regex.test(value)) {
        mesaj.textContent = "Girilen değer geçersiz.";
        return false;
      } else {
        mesaj.textContent = "";
        return true;
      }
    }

    function validateForm() {
      const telefonRegex = /^[0-9]{10,11}$/;
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      const sifre = document.getElementById("sifre").value;
      const sifreTekrar = document.getElementById("sifre_tekrar").value;

      let valid = true;
      valid &= validateInput(document.getElementById("ad"), "adError");
      valid &= validateInput(document.getElementById("soyad"), "soyadError");
      valid &= validateInput(document.getElementById("email"), "emailError", emailRegex);
      valid &= validateInput(document.getElementById("telefon"), "telefonError", telefonRegex);
      valid &= validateInput(document.getElementById("sifre"), "sifreError");

      if (sifre !== sifreTekrar) {
        document.getElementById("sifreTekrarError").textContent = "Şifreler eşleşmiyor.";
        valid = false;
      } else {
        document.getElementById("sifreTekrarError").textContent = "";
      }

      return Boolean(valid);
    }
  </script>
</head>
<body>

  <div class="container">
    <div class="left-side"></div>

    <div class="right-side">
      <div class="form-container">
        <div class="logo">
          <img src="assets/icon/camera.svg" alt="Neva Foto Logo">
        </div>

        <h2>Yeni Hesap Oluştur</h2>
        <p class="subtitle">Anılarını güvenle saklamak için hemen üye ol</p>

        <?php if (!empty($mesaj)) echo "<div class='mesaj'>$mesaj</div>"; ?>

        <form method="POST" autocomplete="off" onsubmit="return validateForm()">
          <input type="text" id="ad" name="ad" placeholder="Ad" value="<?= htmlspecialchars($ad) ?>">
          <div class="mesaj" id="adError"><?= $hatalar['ad'] ?? '' ?></div>

          <input type="text" id="soyad" name="soyad" placeholder="Soyad" value="<?= htmlspecialchars($soyad) ?>">
          <div class="mesaj" id="soyadError"><?= $hatalar['soyad'] ?? '' ?></div>

          <input type="email" id="email" name="email" placeholder="E-posta Adresi" value="<?= htmlspecialchars($email) ?>">
          <div class="mesaj" id="emailError"><?= $hatalar['email'] ?? '' ?></div>

          <input type="text" id="telefon" name="telefon" placeholder="Telefon Numarası (05XXXXXXXXX)" value="<?= htmlspecialchars($telefon) ?>">
          <div class="mesaj" id="telefonError"><?= $hatalar['telefon'] ?? '' ?></div>

          <input type="password" id="sifre" name="sifre" placeholder="Şifre">
          <div class="mesaj" id="sifreError"><?= $hatalar['sifre'] ?? '' ?></div>

          <input type="password" id="sifre_tekrar" name="sifre_tekrar" placeholder="Şifre Tekrar">
          <div class="mesaj" id="sifreTekrarError"><?= $hatalar['sifre_tekrar'] ?? '' ?></div>

          <button type="submit" class="btn-register">Kayıt Ol</button>
        </form>

        <p class="back-link">
          Zaten hesabınız var mı? <a href="kullanici_girisi.php">Giriş Yap</a>
        </p>
      </div>
    </div>
  </div>

  <footer>
    <p>© 2025 Neva Foto Stüdyosu | Tüm Hakları Saklıdır</p>
  </footer>

</body>
</html>
