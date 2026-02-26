<?php
session_start();
require_once "config.php"; // $conn burada tanımlı

$hata = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $sifre = trim($_POST["sifre"]);

    // PDO bağlantı değişkeni $conn
    $sorgu = $conn->prepare("SELECT * FROM yoneticiler WHERE email = ?");
    $sorgu->execute([$email]);
    $yonetici = $sorgu->fetch(PDO::FETCH_ASSOC);

    if ($yonetici) {
        if (password_verify($sifre, $yonetici["sifre"])) {
            // Session atama
            $_SESSION["yonetici_id"] = $yonetici["admin_id"];
            $_SESSION["yonetici_ad"] = $yonetici["ad"];
            $_SESSION["yonetici_email"] = $yonetici["email"];

            // Yönlendirme
            header("Location: yonetici_paneli.php");
            exit;
        } else {
            $hata = "Hatalı şifre!";
        }
    } else {
        $hata = "Bu e-posta ile kayıtlı yönetici bulunamadı.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yönetici Girişi</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="admin-body">

<div class="admin-container">

    <!-- Sol taraf: Fotoğraf -->
    <div class="admin-left"></div>

    <!-- Sağ taraf: Form -->
    <div class="admin-right">
        <div class="admin-form-box">

            <div class="logo">
                <img src="assets/icon/camera.svg" alt="Neva Foto Logo">
            </div>

            <h2>Yönetici Girişi</h2>
            <p class="subtitle">Lütfen bilgilerinizi giriniz</p>

            <?php if (!empty($hata)) echo "<p class='admin-error'>$hata</p>"; ?>

            <form method="POST">
                <input type="email" name="email" class="admin-input" placeholder="E-posta" required>
                <input type="password" name="sifre" class="admin-input" placeholder="Şifre" required>
                <button type="submit" class="admin-btn">Giriş Yap</button>
            </form>

            <p class="admin-back"><a href="index.php">← Ana Sayfaya Dön</a></p>

        </div>
    </div>

</div>

</body>
</html>
