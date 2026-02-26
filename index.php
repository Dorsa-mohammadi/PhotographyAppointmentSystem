<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Neva Fotoğrafçılık</title>
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Open Sans', sans-serif; background: #ffffff; color: #333; display: flex; flex-direction: column; }

    /* Header */
    header { display: flex; justify-content: space-between; align-items: center; padding: 0 20px; height: 90px; background-color: #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.1); position: relative; }
    .logo { display: flex; align-items: center; }
    .logo img { width: 80px; height: 80px; object-fit: contain; }

    /* Nav */
    nav { display: flex; gap: 30px; }
    nav a { text-decoration: none; color: #00796b; font-weight: 600; font-size: 18px; position: relative; padding: 5px 0; transition: color 0.3s; }
    nav a.active, nav a:hover { color: #004d40; }
    nav a.active::after, nav a:hover::after {
      content: '';
      position: absolute; left: 0; bottom: -3px; width: 100%; height: 3px; background-color: #004d40; border-radius: 2px;
    }

    /* Hamburger */
    .hamburger { font-size: 32px; cursor: pointer; background: none; border: none; color: #00796b; z-index: 101; display: block; }
    .slide-menu { display: none; flex-direction: column; position: absolute; top: 90px; right: 20px; background: #b2dfdb; border-radius: 8px; box-shadow: 0 2px 12px rgba(0,0,0,0.2); min-width: 200px; z-index: 100; }
    .slide-menu a { padding: 12px 16px; text-decoration: none; color: #004d40; border-bottom: 1px solid #00796b; transition: background 0.3s, color 0.3s; }
    .slide-menu a:hover { background: #004d40; color: #b2dfdb; }

    /* Hero */
    .hero { display: flex; align-items: center; justify-content: center; min-height: 500px; background: url('assets/images/hero.jpg') no-repeat center center; background-size: contain; padding: 60px 20px; text-align: center; position: relative; }
    .hero-content { background-color: rgba(223, 216, 216, 0.85); padding: 60px; border-radius: 12px; max-width: 600px; }
    .hero-content h1 { font-family: 'Great Vibes', cursive; font-size: 60px; margin-bottom: 20px; color: #004d40; }
    .hero-content p { font-size: 24px; margin-bottom: 30px; color: #00796b; }
    .hero-content .btn { background: #00796b; color: #fff; padding: 18px 30px; font-size: 20px; border-radius: 20px; text-decoration: none; display: inline-block; transition: all 0.3s ease; }
    .hero-content .btn:hover { transform: scale(1.05); background: #004d40; }

    section { padding: 80px 20px; }
    .section-title { font-size: 36px; color: #00796b; margin-bottom: 20px; text-align: center; }
    .section-content { max-width: 1000px; margin: 0 auto; display: flex; flex-wrap: wrap; gap: 40px; align-items: center; }
    .section-content img { max-width: 100%; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    ul.features { list-style-type: disc; padding-left: 20px; font-size: 20px; color: #004d40; line-height: 1.8; }

    /* Paketler bölümü */
    #paketler .section-content { display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-start; }
    #paketler .paket-list { flex: 1 1 45%; font-size: 20px; color: #004d40; line-height: 2; }
    #paketler .paket-slider { flex: 1 1 50%; display: flex; overflow-x: auto; gap: 15px; padding-bottom: 10px; }
    #paketler .paket-slider img { height: 200px; border-radius: 12px; flex-shrink: 0; transition: transform 0.3s; }
    #paketler .paket-slider img:hover { transform: scale(1.05); }

    /* Galeri bölümü */
    #galeri .gallery-slider { display: flex; overflow-x: auto; gap: 15px; padding-bottom: 10px; max-width: 1000px; margin: 0 auto; }
    #galeri .gallery-slider img { height: 250px; border-radius: 12px; flex-shrink: 0; transition: transform 0.3s; cursor: pointer; }
    #galeri .gallery-slider img:hover { transform: scale(1.05); }

    /* Lightbox */
    .lightbox {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.9);
      justify-content: center;
      align-items: center;
      z-index: 999;
    }
    .lightbox img { max-width: 90%; max-height: 90%; border-radius: 12px; }
    .lightbox span { position: absolute; color: white; font-size: 50px; font-weight: bold; cursor: pointer; user-select: none; padding: 10px; transition: 0.3s; }
    .lightbox span:hover { color: #ccc; }
    .lightbox-close { top: 20px; right: 40px; font-size: 60px; }
    .lightbox-prev { top: 50%; left: 30px; transform: translateY(-50%); }
    .lightbox-next { top: 50%; right: 30px; transform: translateY(-50%); }

    footer { text-align: center; padding: 20px; background: #b2dfdb; font-size: 14px; color: #004d40; }

    @media (max-width: 768px) {
      nav { display: none; }
      #paketler .section-content { flex-direction: column; }
      #paketler .paket-slider { overflow-x: scroll; }
      #galeri .gallery-slider { overflow-x: scroll; }
    }
  </style>

</head>
<body>

  <header>
    <div class="logo"><img src="assets/icon/camera.svg" alt="Neva Fotoğrafçılık Logo"></div>
    <nav>
      <a href="#" class="active">Anasayfa</a>
      <a href="#hakkimizda">Hakkımızda</a>
      <a href="#paketler">Paketler</a>
      <a href="#galeri">Galeri</a>
      <a href="#iletisim">İletişim</a>
    </nav>
    <button class="hamburger" onclick="toggleMenu()">☰</button>
    <div class="slide-menu" id="slideMenu">
      <a href="kullanici_girisi.php">Kullanıcı Girişi</a>
      <a href="yonetici_girisi.php">Yönetici Girişi</a>
      <a href="kayit_ol.php">Kayıt Ol</a>
    </div>
  </header>

  <section class="hero">
    <div class="hero-content">
      <h1>Neva Fotoğrafçılık</h1>
      <p>Anılarınızı ölümsüzleştiriyoruz, estetik ve profesyonel dokunuşlarla.</p>
      <a href="kayit_ol.php" class="btn">Hemen Başla</a>
    </div>
  </section>

  <section id="hakkimizda" style="background-color: #ffffff;">
    <h2 class="section-title">Hakkımızda</h2>
    <div class="section-content">

      <div style="flex:1 1 45%; text-align:center;">
        <img src="assets/images/hakkimizda.jpg" alt="Hakkımızda" 
            style="max-width:70%; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
      </div>

      <div style="flex:1 1 50%;">
        <p><strong>Neva Fotoğrafçılık</strong> olarak, en değerli anlarınızı ölümsüzleştirmeyi bir sanat olarak görüyor ve hər çekimde bu anlayışla hareket ediyoruz. Yılların kazandırdığı tecrübe, modern ekipmanlar ve yaratıcı bakış açımızla; her çiftin, her ailenin ve her bireyin hikâyesini en doğal ve özel haliyle kadrajımıza yansıtmayı hedefliyoruz.</p>

        <p>Fotoğrafçılık bizim için yalnızca bir meslek değil; insanların en mutlu, en anlamlı ve en özel anlarına eşlik etme fırsatı sunan bir tutku. Her çekimde samimiyete, doğal akışa ve kişisel beklentilere önem veriyor, ortaya sadece güzel kareler değil; yıllar sonra bile aynı duyguyu yaşatacak hatıralar çıkarıyoruz.</p>

        <p>Düğün, nişan, etkinlik ve konsept çekimlerinde her detayı titizlikle planlayarak hem profesyonel hem de keyifli bir çekim deneyimi sunuyoruz. Çekim öncesinde sizleri tanımaya özen göstererek tarzınıza, beklentilerinize ve hayallerinize en uygun fotoğraf hikâyesini oluşturuyoruz.</p>

        <p>Kullandığımız son teknoloji ekipmanlar, özenli düzenleme sürecimiz ve estetik anlayışımız ile her projede beklentilerin üzerine çıkmayı amaçlıyoruz. Hayallerinizi gerçeğe dönüştürmek için buradayız.</p>

        <p style="font-weight:600; font-style:italic; color:#004d40;">Siz hayal edin, biz o hayali kadraja sığdıralım.</p>
      </div>

    </div>
  </section>


  <!-- Paketler -->
  <section id="paketler" style="background-color: #ffffff;">
    <h2 class="section-title">Paketler</h2>
    <div class="section-content">
      <div class="paket-list">
        <ul>
          <li>Düğün Fotoğrafçılığı</li>
          <li>Doğum Günü & Etkinlik Çekimleri</li>
          <li>Profesyonel Portre Çekimleri</li>
          <li>Kurumsal Fotoğraf Çekimleri</li>
          <li>Mezuniyet Çekimleri</li>
          <li>Nişan & Kına Çekimleri</li>
        </ul>

      </div>
      <div class="paket-slider" >
        <img src="assets/images/paket1.jpg" alt="Paket 1">
        <img src="assets/images/paket2.jpg" alt="Paket 2">
        <img src="assets/images/paket3.jpg" alt="Paket 3">
        <img src="assets/images/paket4.jpg" alt="Paket 4">
        <img src="assets/images/paket5.jpg" alt="Paket 5">
        <img src="assets/images/paket6.jpg" alt="Paket 6">
      </div>
    </div>
  </section>

  <!-- Galeri -->
  <section id="galeri" style="background-color: #ffffff;">
    <h2 class="section-title">Galeri</h2>
    <div class="gallery-slider">
      <img src="assets/images/galeri1.jpg" alt="Galeri 1">
      <img src="assets/images/galeri2.jpg" alt="Galeri 2">
      <img src="assets/images/galeri3.jpg" alt="Galeri 3">
      <img src="assets/images/galeri4.jpg" alt="Galeri 4">
      <img src="assets/images/galeri5.jpg" alt="Galeri 5">
      <img src="assets/images/galeri6.jpg" alt="Galeri 6">
      <img src="assets/images/galeri7.jpg" alt="Galeri 7">
      <img src="assets/images/galeri8.jpg" alt="Galeri 8">
      <img src="assets/images/galeri9.jpg" alt="Galeri 9">
    </div>
  </section>

  <!-- Lightbox -->
  <div class="lightbox" id="lightbox">
    <span class="lightbox-close" id="lightboxClose">&times;</span>
    <span class="lightbox-prev" id="lightboxPrev">&#10094;</span>
    <img id="lightboxImg" src="">
    <span class="lightbox-next" id="lightboxNext">&#10095;</span>
  </div>

  <section id="iletisim" style="background-color: #ffffff;">
    <h2 class="section-title">İletişim</h2>
    <div class="section-content">
      <div style="flex:1 1 48%;">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3048.0000000000005!2d27.147000000000004!3d38.423700000000006!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14bbd7a000000001%3A0x0000000000000000!2sİzmir%2C%20Turkey!5e0!3m2!1str!2str!4v1747739495120!5m2!1str!2str" width="100%" height="350" style="border:0; border-radius:12px;" allowfullscreen="" loading="lazy"></iframe>
      </div>
      <div style="flex:1 1 48%;">
        <p><strong>Adres:</strong><br>İzmir, Türkiye</p>
        <p><strong>Instagram:</strong><br><a href="https://instagram.com/neva_fotografcilik" target="_blank" style="color:#00796b;text-decoration:none;">@neva_fotografcilik</a></p>
        <p style="font-style:italic; color:#004d40;">Bize ulaşın ve en özel anlarınızı ölümsüzleştirin!</p>
      </div>
    </div>
  </section>

  <footer>
    &copy; 2025 Neva Fotoğrafçılık. Tüm hakları saklıdır.
  </footer>

  <!-- jQuery -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

  <!-- Bootstrap JS -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

  <script>
    // Hamburger menü
    function toggleMenu() {
      const menu = document.getElementById('slideMenu');
      menu.style.display = menu.style.display === 'flex' ? 'none' : 'flex';
    }

    // Lightbox fonksiyonları
    const galleryImages = document.querySelectorAll("#galeri .gallery-slider img");
    const lightbox = document.getElementById("lightbox");
    const lightboxImg = document.getElementById("lightboxImg");
    const lightboxClose = document.getElementById("lightboxClose");
    const lightboxPrev = document.getElementById("lightboxPrev");
    const lightboxNext = document.getElementById("lightboxNext");

    let currentIndex = 0;

    function showLightbox(index) {
      currentIndex = index;
      lightboxImg.src = galleryImages[currentIndex].src;
      lightbox.style.display = "flex";
    }

    galleryImages.forEach((img, index) => {
      img.addEventListener("click", () => showLightbox(index));
    });

    lightboxClose.addEventListener("click", () => {
      lightbox.style.display = "none";
    });

    lightboxPrev.addEventListener("click", (e) => {
      e.stopPropagation();
      currentIndex = (currentIndex === 0) ? galleryImages.length - 1 : currentIndex - 1;
      lightboxImg.src = galleryImages[currentIndex].src;
    });

    lightboxNext.addEventListener("click", (e) => {
      e.stopPropagation();
      currentIndex = (currentIndex === galleryImages.length - 1) ? 0 : currentIndex + 1;
      lightboxImg.src = galleryImages[currentIndex].src;
    });

    lightbox.addEventListener("click", (e) => {
      if(e.target === lightbox) lightbox.style.display = "none";
    });
  </script>
</body>
</html>
