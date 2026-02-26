
<?php
session_start();
require_once 'config.php';

// Yönetici kontrolü
if (!isset($_SESSION['yonetici_id']) || empty($_SESSION['yonetici_id'])) {
    header("Location: cikis.php");
    exit;
}

$yonetici_id = $_SESSION['yonetici_id'];

// Yönetici bilgileri
$stmt = $conn->prepare("SELECT * FROM yoneticiler WHERE admin_id = :id LIMIT 1");
$stmt->execute([':id' => $yonetici_id]);
$yonetici = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$yonetici) {
    header("Location: cikis.php");
    exit;
}

$paketler = [];
try {
    $stmt = $conn->query("SELECT * FROM paketler ORDER BY paket_id DESC");
    if($stmt) {
        $paketler = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch(PDOException $e) {
    $paketler = [];
}



// ---------- OTOMATİK TAMAMLANDI GÜNCELLEME ----------
// Onaylanmış ve zamanı geçmiş randevuları "Tamamlandı" yap
try {
    $conn->prepare("
        UPDATE randevular
        SET onay_durumu = 3
        WHERE onay_durumu = 1
          AND CONCAT(tarih,' ',saat) <= NOW()
    ")->execute();
} catch (PDOException $e) {
    error_log("Otomatik Tamamlandı Güncelleme Hatası: " . $e->getMessage());
}

// Kullanıcı listeleme
$users = $conn->query("SELECT * FROM kullanicilar ORDER BY ad ASC")->fetchAll(PDO::FETCH_ASSOC);

// Randevular
$randevular = $conn->query("
    SELECT r.*, k.ad, k.soyad, p.paket_adi
    FROM randevular r
    LEFT JOIN kullanicilar k ON k.kullanici_id = r.kullanici_id
    LEFT JOIN paketler p ON p.paket_id = r.paket_id
    ORDER BY r.tarih DESC, r.saat DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Yönetici Paneli</title>
<link rel="stylesheet" href="kullanici_paneli.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
/* Sidebar */
:root{
    --sidebar-width:220px;
}
*{box-sizing:border-box;font-family:Inter, "Segoe UI", Roboto, Arial, sans-serif;}
body{margin:0;background:#f5f6fa;color:#222;}
.sidebar {
    width:var(--sidebar-width);
    height:100vh;
    background: linear-gradient(to bottom, #2c3e50, #1e2a38);
    padding:20px;
    position:fixed;
    left:0;
    top:0;
    display:flex;
    flex-direction:column;
    border-top-right-radius:20px;
    border-bottom-right-radius:20px;
    box-shadow:3px 0 15px rgba(0,0,0,0.12);
    gap:8px;
}
.sidebar .user-greeting {
    font-size:16px;
    font-weight:700;
    color:#fff;
    margin-bottom:10px;
    text-align:center;
    padding:10px 6px;
    border-radius:8px;
    background: rgba(255,255,255,0.04);
}
.sidebar button,
.sidebar form button {
    display:block;
    width:100%;
    padding:12px 14px;
    border:none;
    border-radius:10px;
    font-size:14px;
    font-weight:600;
    color:#fff;
    background: rgba(255,255,255,0.06);
    cursor:pointer;
    transition: all 0.18s ease;
    text-align:left;
}
.sidebar button:hover,
.sidebar form button:hover {
    background: rgba(255,255,255,0.12);
    transform: translateY(-2px);
}

/* Main */
.main-content {
    margin-left: calc(var(--sidebar-width) + 20px);
    padding:28px;
    min-height:100vh;
}

/* Box */
.box {
    background:#fff;
    padding:20px;
    border-radius:10px;
    box-shadow:0 6px 18px rgba(25,31,43,0.06);
    width:100%;
    margin:auto;
    margin-top:24px;
}
.box h2 {
    text-align:left;
    margin:0 0 10px 0;
    font-size:20px;
}

/* Tablo */
.table-responsive{overflow-x:auto;}
.randevu-tablo,
.kullanici-tablo {
    width:100%;
    border-collapse: collapse;
    margin-top:12px;
}
.randevu-tablo th, .randevu-tablo td,
.kullanici-tablo th, .kullanici-tablo td {
    border:1px solid #eef2f6;
    padding:10px 8px;
    text-align:center;
    vertical-align:middle;
    font-size:14px;
}
.randevu-tablo th, .kullanici-tablo th {
    background:#fbfdff;
    font-weight:700;
}

/* Butonlar */
.action-btn {
    padding:6px 10px;
    border:none;
    border-radius:6px;
    cursor:pointer;
    margin:3px 3px;
    text-decoration:none;
    display:inline-block;
    font-weight:600;
    font-size:13px;
}
.onay { background:#16a34a; color:#fff; }
.reddet { background:#ef4444; color:#fff; }
.sil { background:#6b7280; color:#fff; }
.duzenle { background:#2563eb; color:#fff; }

/* Form inside table cell */
.form-inline {
    display:flex;
    flex-direction:column;
    align-items:stretch;
    gap:8px;
    min-width:220px;
}
.form-inline textarea {
    width:100%;
    min-height:54px;
    padding:8px;
    border:1px solid #e6edf3;
    border-radius:6px;
    resize:vertical;
    font-size:13px;
}

/* Small screens */
@media (max-width:900px){
    .sidebar{position:relative;width:100%;height:auto;border-radius:0 0 12px 12px;box-shadow:none;display:flex;flex-wrap:wrap;}
    .main-content{margin-left:0;padding:16px;}
    .form-inline { min-width:160px; }
}
</style>
</head>
<body>

<div class="sidebar" role="navigation" aria-label="Yönetici menüsü">
    <div class="user-greeting">Hoş Geldiniz, <?= htmlspecialchars($yonetici['ad']) ?> </div>
    <button type="button" onclick="showPage('home')">Ana Sayfa</button>

    <button type="button" onclick="showPage('kullanicilar')">Kullanıcıları Listele</button>
    <button type="button" onclick="showPage('randevular')">Randevular</button>
    <button type="button" onclick="showPage('paketler')">Paketler</button>

    <form method="POST" action="cikis.php" style="margin-top:auto;">
        <button type="submit">Çıkış</button>
    </form>
</div>

<div class="main-content" role="main">

<!-- ANA SAYFA -->
<div id="home" class="box" data-tab="home" style="display:none;">
    <h2>Yönetici Paneline Hoş Geldiniz</h2>
    <p style="color:#475569; margin-top:8px;">
        Buradan sistem yönetimi, duyurular, kullanıcı işlemleri ve randevu yönetimini gerçekleştirebilirsiniz.
    </p>
</div>



<!-- KULLANICILAR -->
<div id="kullanicilar" class="box" data-tab="kullanicilar" style="display:none;">
    <h2>Kayıtlı Kullanıcılar</h2>
    <div class="table-responsive">
        <table class="kullanici-tablo" id="kullaniciTablo" aria-describedby="kullanici-listesi">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ad Soyad</th>
                    <th>Email</th>
                    <th>Telefon</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($users as $u): ?>
                <tr id="kullanici-<?= (int)$u['kullanici_id'] ?>">
                    <td><?= (int)$u['kullanici_id'] ?></td>
                    <td><?= htmlspecialchars($u['ad']." ".$u['soyad']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['telefon']) ?></td>
                    <td>
                        <button class="action-btn sil" type="button" onclick="silKullanici(<?= (int)$u['kullanici_id'] ?>)">Sil</button>
                        <button class="action-btn duzenle" type="button" onclick="duzenleAc(<?= (int)$u['kullanici_id'] ?>)">Düzenle</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div id="duzenlemeForm" style="display:none; margin-top:18px; padding:14px; border:1px solid #eef2f6; border-radius:10px; background:#fbfdff;">
        <h3 style="margin:0 0 10px 0;">Kullanıcı Düzenle</h3>
        <form id="formDuzenle">
            <input type="hidden" name="kullanici_id" id="duzenle_id">
            <label style="font-weight:600;">Ad:</label>
            <input type="text" name="ad" id="duzenle_ad" required style="width:100%; padding:8px; border-radius:6px; border:1px solid #e6edf3;"><br>
            <label style="font-weight:600; margin-top:8px;">Soyad:</label>
            <input type="text" name="soyad" id="duzenle_soyad" required style="width:100%; padding:8px; border-radius:6px; border:1px solid #e6edf3;"><br>
            <label style="font-weight:600; margin-top:8px;">Email:</label>
            <input type="email" name="email" id="duzenle_email" required style="width:100%; padding:8px; border-radius:6px; border:1px solid #e6edf3;"><br>
            <label style="font-weight:600; margin-top:8px;">Telefon:</label>
            <input type="text" name="telefon" id="duzenle_telefon" required style="width:100%; padding:8px; border-radius:6px; border:1px solid #e6edf3;"><br>
            <div style="margin-top:12px; display:flex; gap:8px;">
                <button type="submit" class="action-btn duzenle" style="padding:8px 12px;">Kaydet</button>
                <button type="button" class="action-btn sil" onclick="duzenlemeFormKapat()" style="padding:8px 12px;">İptal</button>
            </div>
        </form>
    </div>
</div>

<!-- RANDEVULAR -->
<div id="randevular" class="box" data-tab="randevular" style="display:none;">
    <h2>Kullanıcı Randevuları</h2>

    <?php if (!empty($cakisanlar)): ?>
    <div style="padding:12px; background:#fee2e2; color:#b91c1c; border:1px solid #fca5a5; border-radius:8px; font-weight:600; margin-bottom:12px;">
        ⚠️ Aynı tarih ve saatte çakışan randevular mevcut! Lütfen kontrol edin.
    </div>
    <?php endif; ?>

    <!-- RANDEVU FİLTRE MENÜSÜ -->
    <div style="margin-bottom: 15px;">
        <label for="randevuFiltre" style="font-weight:600;">Randevu Filtresi:</label>
        <select id="randevuFiltre" style="padding:6px 10px; border-radius:6px; border:1px solid #ccc;">
            <option value="tum">Tüm Randevular</option>
            <option value="gelecek">Gelecek Randevular</option>
            <option value="gecmis">Geçmiş Randevular</option>
        </select>
    </div>

    <div class="table-responsive">
        <table class="randevu-tablo" aria-describedby="randevu-listesi">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kullanıcı</th>
                    <th>Tarih</th>
                    <th>Saat</th>
                    <th>Paket</th>
                    <th>Durum</th>
                    <th>Mesaj</th>
                    <th>İşlemler</th>
                </tr>
            </thead>

            <tbody>
            <?php
            $randevuSaatleri = [];
            $simdi = time();
            foreach($randevular as $r):
                $randevuZamani = strtotime($r['tarih'].' '.$r['saat']);
                $key = $r['tarih'].' '.$r['saat'];
                $cakisma = isset($randevuSaatleri[$key]);
                if (!$cakisma) $randevuSaatleri[$key] = true;
            ?>
                <tr <?= $cakisma ? 'style="background:#fee2e2;" title="Bu randevu başka bir randevu ile çakışıyor"' : '' ?>>
                    <td><?= (int)$r['randevu_id'] ?></td>
                    <td><?= htmlspecialchars($r['ad']." ".$r['soyad']) ?></td>
                    <td><?= htmlspecialchars($r['tarih']) ?></td>
                    <td><?= htmlspecialchars($r['saat']) ?></td>
                    <td><?= htmlspecialchars($r['paket_adi'] ?: '—') ?></td>

                    <td>
                        <?php
                        switch($r['onay_durumu']) {
                            case 0: echo "<span style='color:orange;font-weight:700;'>Beklemede</span>"; break;
                            case 1: echo "<span style='color:green;font-weight:700;'>Onaylandı</span>"; break;
                            case 2: echo "<span style='color:red;font-weight:700;'>Reddedildi</span>"; break;
                            case 3: echo "<span style='color:blue;font-weight:700;'>Tamamlandı</span>"; break;
                            default: echo "<span>—</span>";
                        }
                        ?>
                    </td>

                    <td><?= htmlspecialchars($r['yonetici_mesaji'] ?: '—') ?></td>

                    <td>
                        <form method="POST" action="randevu_islem.php" style="margin:0;">
                            <input type="hidden" name="randevu_id" value="<?= (int)$r['randevu_id'] ?>">

                            <textarea name="yonetici_mesaji" placeholder="Mesaj..." style="width:100%; height:45px;"><?= htmlspecialchars($r['yonetici_mesaji'] ?? '') ?></textarea>

                            <div style="display:flex; gap:6px; justify-content:center; flex-wrap:wrap;">
                                <?php if ($r['onay_durumu'] == 3 || $randevuZamani <= $simdi): ?>
                                    <button type="submit" name="randevu_sil" class="action-btn sil"
                                            onclick="return confirm('Bu randevuyu silmek istediğinize emin misiniz?');">
                                        Sil
                                    </button>
                                <?php else: ?>
                                    <button type="submit" name="randevu_onayla" class="action-btn onay">Onayla</button>
                                    <button type="submit" name="randevu_reddet" class="action-btn reddet">Reddet</button>
                                    <button type="submit" name="randevu_sil" class="action-btn sil"
                                            onclick="return confirm('Bu randevuyu silmek istediğinize emin misiniz?');">
                                        Sil
                                    </button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- JS FİLTRE KODU -->
<script>
document.getElementById("randevuFiltre").addEventListener("change", function () {
    const secim = this.value;
    const suAn = new Date().getTime();

    document.querySelectorAll('.randevu-tablo tbody tr').forEach(row => {
        const tarih = row.cells[2].innerText.trim();
        const saat = row.cells[3].innerText.trim();
        const zaman = new Date(tarih + " " + saat).getTime();

        if (secim === "tum") row.style.display = "";
        else if (secim === "gelecek") row.style.display = zaman >= suAn ? "" : "none";
        else if (secim === "gecmis") row.style.display = zaman < suAn ? "" : "none";
    });
});
</script>


<!-- PAKETLER -->
<!-- PAKETLER -->
<div id="paketler" class="box" data-tab="paketler" style="display:none;">
    <h2>Paket Ekle</h2>
    <form id="paketEkleForm">
        <input type="hidden" name="paket_id" id="paket_id">
        <label>Paket Adı:</label>
        <input type="text" name="paket_adi" id="paket_adi" required style="width:100%; padding:8px; margin:5px 0;">
        
        <label>Açıklama:</label>
        <textarea name="aciklama" id="aciklama" style="width:100%; padding:8px; margin:5px 0;"></textarea>
        
        <label>Fiyat:</label>
        <input type="number" step="0.01" name="fiyat" id="fiyat" required style="width:100%; padding:8px; margin:5px 0;">
        
        <label>Süre (Saat):</label>
        <input type="number" name="sure_saat" id="sure_saat" required style="width:100%; padding:8px; margin:5px 0;">
        
        <label>Aktif mi?</label>
        <select name="aktif" id="aktif" style="width:100%; padding:8px; margin:5px 0;">
            <option value="evet">Evet</option>
            <option value="hayir">Hayır</option>
        </select>
        
        <button type="submit" style="margin-top:10px; padding:10px 14px; border:none; background:#0ea5a4; color:#fff; font-weight:bold; cursor:pointer;">Kaydet</button>
        <button type="button" onclick="iptalPaket()" style="margin-top:10px; padding:10px 14px; border:none; background:#6b7280; color:#fff; font-weight:bold; cursor:pointer;">İptal</button>
    </form>

    <h2 style="margin-top:20px;">Paketler Listesi</h2>
    <div class="table-responsive">
        <table id="paketTablo">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Adı</th>
                    <th>Açıklama</th>
                    <th>Fiyat</th>
                    <th>Süre</th>
                    <th>Aktif</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($paketler)): ?>
                    <?php foreach($paketler as $p): ?>
                    <tr id="paket-<?= $p['paket_id'] ?>">
                        <td><?= $p['paket_id'] ?></td>
                        <td><?= htmlspecialchars($p['paket_adi']) ?></td>
                        <td><?= htmlspecialchars($p['aciklama']) ?></td>
                        <td><?= $p['fiyat'] ?></td>
                        <td><?= $p['sure_saat'] ?></td>
                        <td><?= $p['aktif'] ?></td>
                        <td>
                            <button class="action-btn duzenle" onclick="duzenlePaket(<?= $p['paket_id'] ?>)">Düzenle</button>
                            <button class="action-btn sil" onclick="paketSil(<?= $p['paket_id'] ?>)">Sil</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" style="text-align:center;">Henüz paket yok</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function duzenlePaket(id){
    const row = document.getElementById('paket-' + id);
    document.getElementById('paket_id').value = id;
    document.getElementById('paket_adi').value = row.cells[1].innerText;
    document.getElementById('aciklama').value = row.cells[2].innerText;
    document.getElementById('fiyat').value = row.cells[3].innerText;
    document.getElementById('sure_saat').value = row.cells[4].innerText;
    document.getElementById('aktif').value = row.cells[5].innerText;
    window.scrollTo({top:0, behavior:'smooth'});
}

function iptalPaket(){
    document.getElementById('paketEkleForm').reset();
    document.getElementById('paket_id').value = '';
}

document.getElementById('paketEkleForm').addEventListener('submit', function(e){
    e.preventDefault();
    const form = new FormData(this);
    fetch('paket_islem.php', { method:'POST', body: form })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            Swal.fire({
                icon: 'success',
                title: 'Başarılı!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => location.reload());
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: data.message || 'Bir hata oluştu'
            });
        }
    })
    .catch(()=>{
        Swal.fire({
            icon: 'error',
            title: 'Hata!',
            text: 'Sunucuya bağlanılamadı'
        });
    });
});

function paketSil(id){
    Swal.fire({
        title: 'Emin misiniz?',
        text: "Bu paketi silmek istediğinize emin misiniz?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Evet, sil!',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('paket_sil.php', { 
                method:'POST', 
                headers:{'Content-Type':'application/x-www-form-urlencoded'}, 
                body:'paket_id=' + id 
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    document.getElementById('paket-' + id).remove();
                    Swal.fire({
                        icon: 'success',
                        title: 'Silindi!',
                        text: 'Paket silindi',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: data.message || 'Silme işlemi başarısız'
                    });
                }
            })
            .catch(()=> Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: 'Sunucuya bağlanılamadı'
            }));
        }
    });
}

</script>



<!-- JS FİLTRE KODU -->
<script>
document.getElementById("randevuFiltre").addEventListener("change", function () {
    const secim = this.value;
    const suAn = new Date().getTime();

    document.querySelectorAll('.randevu-tablo tbody tr').forEach(row => {
        const tarih = row.cells[2].innerText.trim();
        const saat = row.cells[3].innerText.trim();
        const zaman = new Date(tarih + " " + saat).getTime();

        if (secim === "tum") row.style.display = "";
        else if (secim === "gelecek") row.style.display = zaman >= suAn ? "" : "none";
        else if (secim === "gecmis") row.style.display = zaman < suAn ? "" : "none";
    });
});
</script>

</div> <!-- main-content sonu -->

<script>
function showPage(id) {
    const pages = ['home','duyuru','kullanicilar','randevular','paketler'];

    pages.forEach(p => {
        const el = document.getElementById(p);
        if (!el) return;
        el.style.display = (p === id) ? 'block' : 'none';
    });
    try {
        const url = new URL(window.location);
        url.searchParams.set('tab', id);
        window.history.replaceState({}, '', url);
    } catch(e){}
}

function silKullanici(kullanici_id) {
    if(!confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')) return;
    fetch('kullanici_sil_ajax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'kullanici_id=' + encodeURIComponent(kullanici_id)
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const row = document.getElementById('kullanici-' + kullanici_id);
            if(row) row.remove();
            Swal.fire('Başarılı', 'Kullanıcı silindi.', 'success');
        } else {
            Swal.fire('Hata', data.message || 'Kullanıcı silinemedi.', 'error');
        }
    })
    .catch(() => Swal.fire('Hata', 'Sunucuya bağlanılamadı.', 'error'));
}

function duzenleAc(kid) {
    const row = document.getElementById('kullanici-' + kid);
    if (!row) return;
    document.getElementById('duzenlemeForm').style.display = 'block';
    document.getElementById('duzenle_id').value = kid;
    const fullname = row.cells[1].innerText.trim();
    const parts = fullname.split(' ');
    document.getElementById('duzenle_ad').value = parts.shift() || '';
    document.getElementById('duzenle_soyad').value = parts.join(' ') || '';
    document.getElementById('duzenle_email').value = row.cells[2].innerText.trim();
    document.getElementById('duzenle_telefon').value = row.cells[3].innerText.trim();
    document.getElementById('duzenlemeForm').scrollIntoView({behavior:'smooth', block:'center'});
}
function duzenlemeFormKapat() {
    document.getElementById('duzenlemeForm').style.display = 'none';
}

document.getElementById('formDuzenle').addEventListener('submit', function(e){
    e.preventDefault();
    const form = e.target;
    const data = new URLSearchParams(new FormData(form));
    fetch('kullanici_duzenle_ajax.php', {
        method:'POST',
        body: data
    })
    .then(res => res.json())
    .then(resp => {
        if(resp.success){
            const row = document.getElementById('kullanici-' + form.kullanici_id.value);
            if(row){
                row.cells[1].innerText = form.ad.value + " " + form.soyad.value;
                row.cells[2].innerText = form.email.value;
                row.cells[3].innerText = form.telefon.value;
            }
            Swal.fire('Başarılı','Kullanıcı güncellendi','success');
            duzenlemeFormKapat();
        } else {
            Swal.fire('Hata', resp.message || 'Güncelleme başarısız','error');
        }
    })
    .catch(()=> Swal.fire('Hata','Sunucuya bağlanılamadı','error'));
});

window.addEventListener('DOMContentLoaded', function() {
    try {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab && document.getElementById(tab)) {
            showPage(tab);
        } else {
            showPage('home');
        }
    } catch(e){
        showPage('home');
    }
});

document.getElementById("randevuFiltre").addEventListener("change", function() {
    const secim = this.value;
    const suAn = new Date().getTime();

    document.querySelectorAll('.randevu-tablo tbody tr').forEach(row => {
        const tarih = row.cells[2].innerText.trim();
        const saat = row.cells[3].innerText.trim();
        const zaman = new Date(tarih + " " + saat).getTime();

        if (secim === "tum") {
            row.style.display = "";
        }
        else if (secim === "gelecek") {
            row.style.display = (zaman >= suAn) ? "" : "none";
        }
        else if (secim === "gecmis") {
            row.style.display = (zaman < suAn) ? "" : "none";
        }
    });
});





</script>


</body>
</html>
