<?php
session_start();
require_once 'config.php';

// Yönetici kontrolü
if (!isset($_SESSION['yonetici_id']) || empty($_SESSION['yonetici_id'])) {
    header("Location: cikis.php");
    exit;
}
$yonetici_id = $_SESSION['yonetici_id'];
$stmt = $conn->prepare("SELECT * FROM yoneticiler WHERE admin_id = :id LIMIT 1");
$stmt->execute([':id' => $yonetici_id]);
$yonetici = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$yonetici) {
    header("Location: cikis.php");
    exit;
}

// Paketleri çek
$paketler = $conn->query("SELECT * FROM paketler ORDER BY paket_id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Paket Yönetimi</title>
<link rel="stylesheet" href="kullanici_paneli.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
body{font-family:Arial, sans-serif; background:#f5f6fa; margin:0; padding:0;}
.sidebar{width:220px; height:100vh; background:#2c3e50; color:#fff; float:left; padding:20px; box-sizing:border-box; border-top-right-radius:20px; border-bottom-right-radius:20px;}
.sidebar button{width:100%; padding:12px; margin-bottom:10px; border:none; border-radius:10px; cursor:pointer; background: rgba(255,255,255,0.1); color:#fff; font-weight:600;}
.sidebar button:hover{background: rgba(255,255,255,0.2);}
.main-content{margin-left:240px; padding:20px;}
.box{background:#fff; padding:20px; border-radius:10px; box-shadow:0 6px 18px rgba(0,0,0,0.06); margin-top:20px;}
.table-responsive{overflow-x:auto;}
table{width:100%; border-collapse:collapse;}
th, td{border:1px solid #eee; padding:10px; text-align:center;}
th{background:#f9f9f9;}
.action-btn{padding:6px 10px; border:none; border-radius:6px; cursor:pointer; margin:2px; font-weight:600;}
.duzenle{background:#2563eb; color:#fff;}
.sil{background:#ef4444; color:#fff;}
</style>
</head>
<body>

<div class="sidebar">
    <div style="font-weight:bold; margin-bottom:20px; text-align:center;">Hoşgeldiniz, <?= htmlspecialchars($yonetici['ad']) ?></div>
    <button onclick="window.location.href='yonetici_paneli.php'">Ana Sayfa</button>
    <button onclick="window.location.href='paketler.php'">Paketler</button>
    <form method="POST" action="cikis.php" style="margin-top:auto;">
        <button type="submit">Çıkış</button>
    </form>
</div>

<div class="main-content">

<div class="box">
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
        <button type="button" onclick="iptal()" style="margin-top:10px; padding:10px 14px; border:none; background:#6b7280; color:#fff; font-weight:bold; cursor:pointer;">İptal</button>
    </form>
</div>

<div class="box">
    <h2>Paketler Listesi</h2>
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
                <?php foreach($paketler as $p): ?>
                <tr id="paket-<?= $p['paket_id'] ?>">
                    <td><?= $p['paket_id'] ?></td>
                    <td><?= htmlspecialchars($p['paket_adi']) ?></td>
                    <td><?= htmlspecialchars($p['aciklama']) ?></td>
                    <td><?= $p['fiyat'] ?></td>
                    <td><?= $p['sure_saat'] ?></td>
                    <td><?= $p['aktif'] ?></td>
                    <td>
                        <button class="action-btn duzenle" onclick="duzenle(<?= $p['paket_id'] ?>)">Düzenle</button>
                        <button class="action-btn sil" onclick="paketSil(<?= $p['paket_id'] ?>)">Sil</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</div>

<script>
function duzenle(id){
    const row = document.getElementById('paket-' + id);
    document.getElementById('paket_id').value = id;
    document.getElementById('paket_adi').value = row.cells[1].innerText;
    document.getElementById('aciklama').value = row.cells[2].innerText;
    document.getElementById('fiyat').value = row.cells[3].innerText;
    document.getElementById('sure_saat').value = row.cells[4].innerText;
    document.getElementById('aktif').value = row.cells[5].innerText;
    window.scrollTo({top:0, behavior:'smooth'});
}

function iptal(){
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
            Swal.fire('Başarılı', data.message, 'success').then(()=>location.reload());
        } else {
            Swal.fire('Hata', data.message || 'Bir hata oluştu', 'error');
        }
    })
    .catch(()=> Swal.fire('Hata','Sunucuya bağlanılamadı','error'));
});

function paketSil(id){
    if(!confirm('Bu paketi silmek istediğinize emin misiniz?')) return;
    fetch('paket_sil.php', { 
        method:'POST', 
        headers:{'Content-Type':'application/x-www-form-urlencoded'}, 
        body:'paket_id=' + id 
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            document.getElementById('paket-' + id).remove();
            Swal.fire('Başarılı','Paket silindi','success');
        } else {
            Swal.fire('Hata', data.message || 'Silme işlemi başarısız','error');
        }
    })
    .catch(()=> Swal.fire('Hata','Sunucuya bağlanılamadı','error'));
}
</script>

</body>
</html>
