# 📸 Fotoğrafçı Randevu ve Yönetim Otomasyon Web Uygulaması

Web tabanlı, tam kapsamlı bir randevu ve yönetim otomasyon sistemidir.

Bu sistem sayesinde:

- 👤 Kullanıcılar online kayıt olabilir
- ✅ Yönetici onayı sonrası sisteme giriş yapabilir
- 📅 Randevu oluşturabilir
- 📦 Paket seçimi yapabilir
- ⏱ Çakışmasız randevu planlaması sağlanır
- 🛠 Yönetici tüm sistemi panel üzerinden kontrol edebilir

---

# 🚀 Kullanılan Teknolojiler

- PHP  
- MySQL  
- HTML5  
- CSS3  
- Bootstrap  
- JavaScript  
- jQuery  

---

# 🏠 Ana Sayfa (index.php)

### 🏠 Anasayfa

![Anasayfa](screenshots/Anasayfa1.PNG)
![Anasayfa](screenshots/Anasayfa2.PNG)
![Anasayfa](screenshots/Anasayfa3.PNG)
![Anasayfa](screenshots/Anasayfa4.PNG)
![Anasayfa](screenshots/Anasayfa5.PNG)

Ana sayfa uygulamanın ziyaretçilere açık olan bölümüdür. Kullanıcılar bu sayfa üzerinden sistem hakkında bilgi alabilir, paketleri inceleyebilir ve kayıt veya giriş işlemlerine ulaşabilir.

**Özellikler:**

- Responsive tasarım  
- Hamburger menü  
- Kullanıcı Girişi  
- Yönetici Girişi  
- Kayıt Ol  
- Hakkımızda bölümü  
- Paketler slider alanı  
- Galeri (Lightbox destekli)  
- İletişim ve Harita  

**Teknik Detaylar:**

- Galeri için özel Lightbox sistemi  
- Yatay scroll destekli paket ve galeri slider  
- Bootstrap 3.4.1 kullanıldı  
- Mobil uyumlu yapı  

---

# 👤 Kullanıcı Kayıt Sayfası

![Kayıt Ol](screenshots/KayitOl.PNG)

Bu sayfa yeni kullanıcıların sisteme kayıt olmasını sağlar. Kullanıcılar gerekli bilgileri girerek hesap oluşturabilir.

**Özellikler:**

- Ad Soyad  
- Telefon  
- E-posta  
- Şifre  
- Yönetici onayı gereklidir  

**Sistem Mantığı:**

1. Kullanıcı kayıt olur  
2. Durumu: Onay Bekliyor  
3. Yönetici panelinden onaylanır  
4. Onay sonrası giriş yapabilir  

Bu yapı sayesinde sistemde yalnızca **onaylanmış kullanıcılar işlem yapabilir**.

---

# 🔐 Kullanıcı Giriş Sayfası

![Kullanıcı Girişi](screenshots/KullaniciGirisi.PNG)

Kullanıcılar bu sayfa üzerinden sisteme giriş yapabilir. Sistem giriş sırasında kullanıcı bilgilerini doğrular ve onay durumunu kontrol eder.

**Özellikler:**

- E-posta  
- Şifre  
- Güvenli oturum başlatma  
- Onaylanmamış kullanıcı giriş yapamaz  

Başarılı giriş sonrası kullanıcı **kendi paneline yönlendirilir**.

---

# 👩‍💼 Kullanıcı Paneli

![Kullanıcı Paneli](screenshots/KullaniciPaneli.PNG)

Kullanıcı paneli, giriş yapan kullanıcıların sistem içerisindeki işlemlerini yönetebildiği ana kontrol alanıdır.

**Kullanıcı İşlemleri:**

- Profil bilgilerini güncelleme  
  - Ad Soyad  
  - Telefon  
  - E-posta  
  - Şifre  
- Randevu oluşturma  
- Randevularımı görüntüleme  
- Çıkış yapma  

Bu panel sayesinde kullanıcılar **kendi hesaplarını ve randevularını kolayca yönetebilir**.

---

# 📅 Randevu Oluşturma Sistemi

![Randevu Oluştur](screenshots/RandevuOlustur.PNG)

Bu sayfa kullanıcıların fotoğraf çekimi için randevu oluşturmasını sağlar.

**Randevu Özellikleri:**

- Tarih seçimi  
- Saat seçimi  
- Paket seçimi  
- Açıklama alanı  

**Akıllı Çakışma Kontrolü:**

- Paket süresi dikkate alınır  
- Yönetici onayladıktan sonra saat aralığı dolu olur  
- Aynı zaman aralığında başka kullanıcı randevu alamaz  
- Geçmiş tarih ve saat için randevu oluşturulamaz  

Bu mekanizma sayesinde sistem **çakışmasız randevu planlaması** sağlar.

---

# 📋 Randevularım Sayfası

![Randevularım](screenshots/Randevularim.PNG)

Kullanıcılar bu sayfa üzerinden oluşturdukları tüm randevuları görüntüleyebilir.

**Görüntüleme Türleri:**

- Beklemede  
- Onaylandı  
- Reddedildi  
- Geçmiş randevular  
- Gelecek randevular  

Bu sayede kullanıcılar randevularının **durumunu kolayca takip edebilir**.

---

# 🛠 Yönetici Giriş Sayfası

![Yönetici Girişi](screenshots/YoneticiGirisi.PNG)

Yönetici sisteme özel giriş ekranı üzerinden erişir. Bu sayfa yalnızca yetkili kullanıcıların yönetim paneline ulaşmasını sağlar.

---

# 🖥 Yönetici Paneli

![Yönetici Paneli](screenshots/YoneticiPaneli.PNG)

Yönetici paneli sistemin tüm yönetim işlemlerinin gerçekleştirildiği ana kontrol merkezidir.

**Yönetici Yetkileri:**

- Kullanıcıları listeleme  
- Kullanıcı silme  
- Kullanıcı düzenleme  
- Randevuları onaylama / reddetme  
- Paket ekleme  
- Paket düzenleme  
- Paket silme  
- Paket aktif/pasif yapma  

Bu panel sayesinde sistem **merkezi olarak yönetilebilir**.

---

# 👥 Kullanıcı Yönetimi

![Kullanıcıları Listele](screenshots/KullanicilariListele.PNG)

Bu bölüm yöneticinin sistemde kayıtlı kullanıcıları kontrol ettiği alandır.

Yönetici bu sayfa üzerinden:

- Sisteme kayıt olan kullanıcıları görüntüleyebilir  
- Kullanıcıları onaylayabilir  
- Gerekirse kullanıcıları silebilir veya düzenleyebilir  

Bu mekanizma sayesinde sistemde yalnızca **geçerli ve onaylı kullanıcılar** aktif olur.

---

# 📅 Randevu Yönetimi

![Randevular](screenshots/Randevular.PNG)

Bu bölüm yönetici panelinde yer alır ve sistemde oluşturulan tüm randevuların kontrol edilmesini sağlar.

Yönetici bu ekrandan:

- Kullanıcıların oluşturduğu randevu taleplerini görüntüleyebilir  
- Randevuları onaylayabilir  
- Randevuları reddedebilir  
- Gerekirse randevuları silebilir  

Sistem ayrıca randevuların **paket süresi ve saat aralıklarına göre çakışmasını otomatik olarak kontrol eder**.

---

# 📦 Paket Yönetim Sistemi

![Paketler](screenshots/Paketler.PNG)

Bu bölüm yöneticinin fotoğraf çekimi paketlerini oluşturduğu ve yönettiği alandır.

**Paket Alanları:**

- Paket Adı  
- Açıklama  
- Fiyat  
- Süre (dakika)  
- Aktif / Pasif durumu  

**Mantık:**

- Sadece aktif paketler kullanıcıya görünür  
- Paket süresi randevu çakışma kontrolünde kullanılır  

Bu yapı sayesinde paketler sistem içinde **esnek şekilde yönetilebilir**.

---

# 🗄 Veritabanı Yapısı (Örnek)

## Kullanıcılar Tablosu

| Alan | Açıklama |
|------|----------|
| id | Kullanıcı ID |
| ad_soyad | Ad Soyad |
| telefon | Telefon |
| email | Email |
| sifre | Hashlenmiş şifre |
| durum | Onay durumu |

## Paketler Tablosu

| Alan | Açıklama |
|------|----------|
| id | Paket ID |
| paket_adi | Paket adı |
| aciklama | Açıklama |
| fiyat | Fiyat |
| sure | Süre |
| aktif | 1/0 |

## Randevular Tablosu

| Alan | Açıklama |
|------|----------|
| id | Randevu ID |
| kullanici_id | Kullanıcı |
| paket_id | Paket |
| tarih | Tarih |
| saat | Saat |
| durum | Beklemede / Onaylandı / Reddedildi |

---

# 🔒 Güvenlik Özellikleri

Sistem temel güvenlik prensipleri dikkate alınarak geliştirilmiştir.

- Oturum kontrolü (Session kontrolü)  
- Yetki bazlı sayfa erişimi  
- Onaysız kullanıcı giriş engeli  
- Geçmiş tarih kontrolü  
- Çakışma engelleme algoritması  

Bu sayede sistem **yetkisiz erişimlere karşı korunur**.

---

# 📱 Responsive Tasarım

Uygulama farklı ekran boyutlarına uyum sağlayacak şekilde geliştirilmiştir.

- Mobil uyumlu  
- Tablet uyumlu  
- Masaüstü uyumlu  
- Hamburger menü  
- Esnek slider yapısı  

---



# 📂 Proje Klasör Yapısı

```
/assets
   /images
   /icon
/screenshots
index.php
kayit_ol.php
kullanici_girisi.php
yonetici_girisi.php
style.css
database.sql
```

---

# ⚙️ Kurulum

1. Projeyi klonlayın:  
```
git clone https://github.com/kullaniciadi/PhotographyAppointmentSystem.git
```

2. Veritabanını içe aktarın  
3. `config.php` dosyasına veritabanı bilgilerinizi girin  
4. Localhost üzerinde çalıştırın  

---

# 🎯 Projenin Amacı

Bu proje:

- Gerçek hayata yönelik  
- Randevu planlama sistemli  
- Yetki bazlı giriş kontrollü  
- Yönetim paneli içeren  
- Çakışma engelleyen  
- Tam kapsamlı bir otomasyon örneğidir
