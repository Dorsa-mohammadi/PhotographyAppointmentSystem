<?php
session_start();
require_once 'config.php'; // Veritabanı bağlantısı

$mesaj = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? '');
    $sifre = trim($_POST["sifre"] ?? '');

    if ($email === '' || $sifre === '') {
        $mesaj = "Lütfen tüm alanları doldurun.";
    } else {

        // Kullanıcıyı veritabanından çek
        $sql = "SELECT * FROM kullanicilar WHERE email = :email LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $kullanici = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($kullanici) {

            // Şifre doğrulama
            if (password_verify($sifre, $kullanici['sifre'])) {

                // SESSION KAYITLARI  ---- DÜZELTİLDİ ----
                $_SESSION['kullanici_id'] = $kullanici['kullanici_id'];
                $_SESSION['kullanici_ad'] = $kullanici['ad'];
                $_SESSION['kullanici_email'] = $kullanici['email'];

                // Yönlendirme
                header("Location: kullanici_paneli.php");
                exit();

            } else {
                $mesaj = "⚠️ Şifre hatalı. Lütfen tekrar deneyin.";
            }

        } else {
            $mesaj = "⚠️ Bu e-posta adresiyle kayıtlı bir kullanıcı bulunamadı.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kullanıcı Girişi - Neva Foto Stüdyosu</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">

  <div class="left-side"></div>

  <div class="right-side">

    <div class="form-container">

      <div class="logo">
        <img src="assets/icon/camera.svg" alt="Neva Foto Logo">
      </div>

      <h2>Kullanıcı Girişi</h2>
      <p class="subtitle">Hesabınıza giriş yapın</p>

      <?php if ($mesaj !== ''): ?>
        <div style="color:red; margin-bottom:10px;">
          <?= htmlspecialchars($mesaj); ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="kullanici_girisi.php">
        <input type="email" name="email" placeholder="E-posta Adresi" required>
        <input type="password" name="sifre" placeholder="Şifre" required>
        <button type="submit" class="btn-register">Giriş Yap</button>
      </form>

      <p class="back-link">
        Henüz hesabınız yok mu? <a href="kayit_ol.php">Kayıt Ol</a>
      </p>

    </div>

  </div>

</div>

<footer>
  <p>© 2025 Neva Foto Stüdyosu | Tüm Hakları Saklıdır</p>
</footer>

</body>
</html>
