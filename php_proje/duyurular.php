<?php
include("db.php"); // Veritabanı ve genel ayarlar

$dosya_yolu = "duyurular.txt"; // Veritabanı yerine kullanılan dosya
$duyurular = array(); // Duyuruların tutulacağı dizi

if(file_exists($dosya_yolu)) { // Dosya var mı kontrolü
    $satirlar = file($dosya_yolu, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); // Dosyayı oku
    // Diziyi terse çevir (Son eklenen en üstte görünsün diye)
    $satirlar = array_reverse($satirlar);
    
    foreach($satirlar as $s) { // Satırları dön
        $p = explode("|", $s); // Veriyi parçala
        if(count($p) == 5) { // 5 parça (id,başlık,içerik,tarih,yazan) varsa geçerlidir
            $duyurular[] = array(
                'id' => $p[0],
                'baslik' => $p[1],
                'icerik' => $p[2],
                'tarih' => $p[3],
                'yazan' => $p[4]
            );
        }
    }
}

// --- DÜZENLEME MODU KONTROLÜ ---
// Eğer URL'den düzenle_id gelirse, formu düzenleme modunda açacağız
$duzenle_id = "";
$duzenle_baslik = "";
$duzenle_icerik = "";

if(isset($_GET["duzenle_id"])) { // Düzenleme isteği var mı?
    $duzenle_id = $_GET["duzenle_id"];
    // Bu ID'li duyuruyu bulmak için diziyi tara
    foreach($duyurular as $d) {
        if($d['id'] == $duzenle_id) { // Eşleşme bulundu
            $duzenle_baslik = $d['baslik']; // Bilgileri al
            $duzenle_icerik = $d['icerik'];
            break; // Döngüden çık
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duyurular - Bülbül Motor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
  <div class="container">
    <a class="navbar-brand" href="index.php"><i class="fas fa-motorcycle text-danger"></i> Bülbül Motor</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav me-auto">
            <li class="nav-item"><a class="nav-link" href="index.php">Ana Sayfa</a></li>
            <li class="nav-item"><a class="nav-link active" href="duyurular.php">Duyurular</a></li>
            <li class="nav-item"><a class="nav-link" href="kullanici_arama.php">Kullanıcı Arama</a></li>
            <?php if(isset($_SESSION["id"]) && $_SESSION["id"] > 0) { ?>
                <li class="nav-item"><a class="nav-link" href="cerez.php">Çerezler</a></li>
                <li class="nav-item"><a class="nav-link" href="ozel_sayfa.php">Session Bilgi</a></li>
                <li class="nav-item"><a class="nav-link" href="multi_session.php">Multi Session</a></li>
            <?php } ?>
        </ul>
        <div class="d-flex align-items-center">
            <?php if(isset($_SESSION["id"]) && $_SESSION["id"] > 0) { ?>
                <div class="dropdown d-inline-block me-2">
                    <button class="btn btn-outline-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle"></i> <?php echo $ad_soyad; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="multi_session.php"><i class="fas fa-users-cog me-2"></i> Hesapları Yönet</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="uyegiris.php"><i class="fas fa-sign-in-alt me-2"></i> Başka Bir Hesaba Giriş Yap</a></li>
                        <li><a class="dropdown-item" href="uyekayit.php"><i class="fas fa-user-plus me-2"></i> Kayıt Ol</a></li>
                    </ul>
                </div>
            <a href="cikis.php?id=<?php echo $user_id; ?>" class="btn btn-danger">
                <i class="fas fa-sign-out-alt me-2"></i>Çıkış Yap
            </a>
            <?php } else { ?>
                <a href="uyegiris.php" class="btn btn-outline-light me-2">Giriş</a>
                <a href="uyekayit.php" class="btn btn-primary-custom">Kayıt Ol</a>
            <?php } ?>
        </div>
    </div>
  </div>
</nav>

<div class="container mt-5 pt-5 flex-grow-1">
    <div class="row">
        <!-- SOL TARAF: Duyuru Ekleme / Düzenleme Formu -->
        <div class="col-md-4">
            <div class="kutu" id="sol-kutu" style="z-index: 1;"> <!-- ID Eklendi -->
                <?php if($duzenle_id != "") { ?>
                    <!-- DÜZENLEME MODU GÖRÜNÜMÜ -->
                    <h4 class="baslik text-warning"><i class="fas fa-edit"></i> Duyuru Düzenle</h4>
                    <form action="duyuru_islem.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $duzenle_id; ?>"> <!-- ID gizli olarak gönderilir -->
                        <div class="mb-3">
                            <label class="form-label">Başlık</label>
                            <input type="text" name="baslik" class="form-control" value="<?php echo $duzenle_baslik; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">İçerik</label>
                            <textarea name="icerik" class="form-control" rows="4" required><?php echo $duzenle_icerik; ?></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-warning w-100" type="submit" name="duzenle">GÜNCELLE</button>
                            <a href="duyurular.php" class="btn btn-secondary">İPTAL</a> <!-- Düzenlemeden vazgeç -->
                        </div>
                    </form>
                
                <?php } else { ?>
                    <!-- YENİ EKLEME MODU GÖRÜNÜMÜ -->
                    <h4 class="baslik"><i class="fas fa-edit text-danger"></i> Duyuru Yaz</h4>
                    
                    <?php if(isset($_SESSION["id"]) && $_SESSION["id"] > 0) { ?> <!-- Sadece üye olanlar yazabilir -->
                        <form action="duyuru_ekle.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Başlık</label>
                                <input type="text" name="baslik" class="form-control" placeholder="Duyuru Başlığı" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">İçerik</label>
                                <textarea name="yeni_duyuru" class="form-control" rows="4" placeholder="Duyuru metni..." required></textarea>
                            </div>
                            <button class="btn btn-primary-custom w-100" type="submit">
                                <i class="fas fa-paper-plane"></i> YAYINLA
                            </button>
                        </form>
                    <?php } else { ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-lock"></i> Duyuru eklemek için lütfen <a href="uyegiris.php" class="fw-bold text-dark">giriş yapın</a>.
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>

        <!-- SAĞ TARAF: Duyuru Listesi -->
        <div class="col-md-8">
            <div class="kutu" id="sag-kutu" style="overflow-y: auto;">
                <h4 class="baslik"><i class="fas fa-bullhorn text-danger"></i> Güncel Duyurular</h4>
                
                <?php if(count($duyurular) == 0) { ?> <!-- Hiç duyuru yoksa -->
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-envelope-open-text fa-3x mb-3"></i>
                        <p>Henüz hiç duyuru eklenmemiş.</p>
                    </div>
                <?php } else { ?>
                    
                    <?php foreach($duyurular as $d) { ?> <!-- Duyuruları listele -->
                        <div class='duyuru-kutu mb-3 p-3 border rounded bg-light position-relative'>
                            <h5 class='text-danger fw-bold'><?php echo $d["baslik"]; ?></h5>
                            <div class='text-muted small mb-2'>
                                <i class='fas fa-clock'></i> <?php echo $d["tarih"]; ?> | 
                                <i class='fas fa-user-edit'></i> <?php echo $d["yazan"]; ?>
                            </div>
                            <p class='mb-0 text-break'><?php echo $d["icerik"]; ?></p>
                            
                            <!-- İşlem Butonları (Sadece Oturum Açanlara ve belki sadece kendi duyurusuysa ama burada herkes herkese açık) -->
                            <?php if(isset($_SESSION["id"]) && $_SESSION["id"] > 0) { ?>
                                <div class="position-absolute top-0 end-0 p-3">
                                    <!-- URL parametresi ile düzenle veya sil işlemini tetikle -->
                                    <a href="duyurular.php?duzenle_id=<?php echo $d['id']; ?>" class="btn btn-sm btn-outline-warning me-1" title="Düzenle"><i class="fas fa-pen"></i></a>
                                    <a href="duyuru_islem.php?sil_id=<?php echo $d['id']; ?>" class="btn btn-sm btn-outline-danger" title="Sil" onclick="return confirm('Bu duyuruyu silmek istediğinize emin misiniz?');"><i class="fas fa-trash"></i></a>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>

                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script>
// Sayfa yüklendiğinde ve boyut değiştiğinde boyutları eşitle
function esitle() {
    var sol = document.getElementById('sol-kutu');
    var sag = document.getElementById('sag-kutu');
    
    if(sol && sag) {
        // Sol kutunun yüksekliğini al
        var yukseklik = sol.offsetHeight;
        
        // Sağ kutuya uygula (Eğer sol kutu içeriğinden dolayı büyürse sağ ona uyar)
        // Ancak sağ kutunun kendi içeriği uzunsa taşabilir, bu yüzden overflow-y zaten css'de var.
        // Önemli nokta: Sağ kutunun yüksekliği sabitlenmeli ki scroll çıksın.
        sag.style.height = yukseklik + "px";
    }
}

window.addEventListener('load', esitle);
window.addEventListener('resize', esitle);
</script>

<?php include("footer.php"); ?>
</body>
</html>
