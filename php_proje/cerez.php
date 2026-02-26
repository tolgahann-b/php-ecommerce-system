<?php
include("db.php"); // Veritabanı bağlantısı

if(!isset($_SESSION["id"])) { // Giriş yapılmamışsa
    header("Location: uyegiris.php"); // Giriş sayfasına yönlendir
    exit;
}

$user_id = $_SESSION["id"]; // Oturumdaki kullanıcı ID'sini al
$dosya_yolu = "cerezler.txt"; // Çerez verisinin tutulacağı dosya yolu

// --- DOSYADAN OKUMA VE SÜRESİ DOLANLARI SİLME ---
// Dosyayı satır satır oku, boş satırları atla
$mevcut_veriler = file($dosya_yolu, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$yeni_veriler = array(); // Güncel verileri tutacak dizi
$su_an = time(); // Şu anki zaman damgası (timestamp)

// Sadece oturum açan kullanıcıya ait verileri ekranda göstermek için dizi
$kullanici_cerezleri = array();

foreach($mevcut_veriler as $satir) { // Her satırı döngüye sok
    // Format: user_id|ad|deger|sure|bitis
    $parca = explode("|", $satir); // Veriyi parçala
    
    if(count($parca) == 5) { // 5 parçadan oluşuyorsa geçerlidir
        $kayit_user_id = $parca[0];
        $kayit_ad = $parca[1];
        $kayit_deger = $parca[2];
        $kayit_sure = $parca[3];
        $kayit_bitis = $parca[4];
        
        // --- Süre kontrolü ---
        if($kayit_bitis > $su_an) {
            // Süresi dolmamış, dosyada kalacak
            $yeni_veriler[] = $satir;
            
            // Eğer bu kullanıcıya aitse, listeye ekle
            if($kayit_user_id == $user_id) {
                $kullanici_cerezleri[] = array(
                    'ad' => $kayit_ad,
                    'deger' => $kayit_deger,
                    'sure' => $kayit_sure,
                    'bitis' => $kayit_bitis
                );
            }
        } else {
            // Süresi dolmuş! Tarayıcıdan da silelim
            if($kayit_user_id == $user_id) {
                setcookie($kayit_ad, "", time() - 3600, "/"); // Tarayıcıdan sil
            }
        }
    }
}

// Dosyayı güncel haliyle (silinenler hariç) yeniden yaz
file_put_contents($dosya_yolu, implode("\n", $yeni_veriler) . (count($yeni_veriler) > 0 ? "\n" : ""));

// --- YERLEŞİK ÇEREZLER (BUILT-IN DEMO) ---
if(!isset($_COOKIE["SistemSürümü"])) { // Yoksa oluştur
    setcookie("SistemSürümü", "v4.0_Premium", time() + (86400 * 365), "/"); // 1 Yıllık
    $_COOKIE["SistemSürümü"] = "v4.0_Premium"; // Ekranda hemen göstermek için diziye ata
}
if(!isset($_COOKIE["SiteTemasi"])) {
    setcookie("SiteTemasi", "Light", time() + (86400 * 30), "/"); // 1 Aylık
    $_COOKIE["SiteTemasi"] = "Light";
}
if(!isset($_COOKIE["DilSecimi"])) {
    setcookie("DilSecimi", "TR_TR", time() + (86400 * 7), "/"); // 1 Haftalık
    $_COOKIE["DilSecimi"] = "TR_TR";
}
if(!isset($_COOKIE["SonZiyaret"])) {
    $simdi = date("d.m.Y H:i:s");
    setcookie("SonZiyaret", $simdi, time() + 3600, "/"); // 1 Saatlik
    $_COOKIE["SonZiyaret"] = $simdi;
}

// --- EKLEME İŞLEMİ ---
if(isset($_POST["cerez_ekle"])) { // Ekleme formundan geldiyse
    $ad = trim($_POST["cerez_adi"]);
    $deger = trim($_POST["cerez_degeri"]);
    $sure = (int)$_POST["sure"]; 
    if($sure < 1) $sure = 3600; // Varsayılan 1 saat
    
    // Güvenlik: Pipe (|) karakterini temizle ki format bozulmasın
    $ad = str_replace("|", "", $ad);
    $deger = str_replace("|", "", $deger);
    
    $bitis_tarihi = time() + $sure; // Bitiş zamanını hesapla
    
    // Tarayıcıya çerez at
    setcookie($ad, $deger, $bitis_tarihi, "/");
    
    // --- Dosyaya Ekleme Mantığı ---
    // Aynı isimde varsa güncelle, yoksa ekle.
    
    $guncel_veri_listesi = array();
    $var_miydi = false;
    
    foreach($yeni_veriler as $satir) {
        $parca = explode("|", $satir);
        if($parca[0] == $user_id && $parca[1] == $ad) { // Eşleşme bulundu
            // Güncelliyoruz
            $var_miydi = true;
            $yeni_satir = "$user_id|$ad|$deger|$sure|$bitis_tarihi";
            $guncel_veri_listesi[] = $yeni_satir; // Yeni halini ekle
        } else {
            $guncel_veri_listesi[] = $satir; // Aynen koru
        }
    }
    
    if(!$var_miydi) { // Listedde yoksa
        // Sona ekle
        $yeni_satir = "$user_id|$ad|$deger|$sure|$bitis_tarihi";
        $guncel_veri_listesi[] = $yeni_satir;
    }
    
    // Dosyaya yaz
    file_put_contents($dosya_yolu, implode("\n", $guncel_veri_listesi) . "\n");
    header("Location: cerez.php"); // Sayfayı yenile
}

// --- SİLME İŞLEMİ ---
if(isset($_GET["sil"])) { // URL'de sil parametresi varsa
    $sil_ad = $_GET["sil"];
    setcookie($sil_ad, "", time() - 3600, "/"); // Tarayıcıdan sil (Geçmiş tarih vererek)
    
    $final_veriler = array();
    foreach($yeni_veriler as $satir) {
        $parca = explode("|", $satir);
        // User ID ve Çerez Adı eşleşiyorsa listeye alma (atla)
        if($parca[0] == $user_id && $parca[1] == $sil_ad) {
            continue; 
        }
        $final_veriler[] = $satir; // Diğerlerini tut
    }
    
    // Dosyayı güncelleyip yeniden yaz
    file_put_contents($dosya_yolu, implode("\n", $final_veriler) . (count($final_veriler) > 0 ? "\n" : ""));
    header("Location: cerez.php");
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Çerezler - Bülbül Motor</title>
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
        <li class="nav-item"><a class="nav-link" href="duyurular.php">Duyurular</a></li>
        <li class="nav-item"><a class="nav-link" href="kullanici_arama.php">Kullanıcı Arama</a></li>
        <li class="nav-item"><a class="nav-link active" href="cerez.php">Çerezler</a></li>
        <li class="nav-item"><a class="nav-link" href="ozel_sayfa.php">Session Bilgi</a></li>
        <li class="nav-item"><a class="nav-link" href="multi_session.php">Multi Session</a></li>
      </ul>
      <div class="d-flex align-items-center">
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
      </div>
    </div>
  </div>
</nav>

<div class="container mt-5 pt-5">
    <div class="row">
        <div class="col-md-4">
            <!-- ÇEREZ EKLEME KUTUSU SOLDA KALIYOR -->
            <div class="kutu mb-4">
                <h4 class="baslik">Çerez Ekle</h4>
                <form action="cerez.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Çerez Adı</label>
                        <input type="text" name="cerez_adi" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Çerez Değeri</label>
                        <input type="text" name="cerez_degeri" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Süre (Saniye)</label>
                        <input type="number" name="sure" class="form-control" value="3600" required>
                        <small class="text-muted">Örn: 3600 = 1 Saat (Süre bitince silinir)</small>
                    </div>
                    <button type="submit" name="cerez_ekle" class="btn btn-primary-custom w-100">KAYDET</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <!-- DOSYA ÇEREZLERİ -->
            <div class="kutu mb-4">
                <h4 class="baslik">Çerezlerim</h4>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Ad</th>
                                <th>Değer</th>
                                <th>Süre (Sn)</th>
                                <th>Kalan Süre</th>
                                <th width="100">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($kullanici_cerezleri as $c) { 
                                $kalan_sure = "Hesaplanıyor...";
                                $sure_bitti_mi = false;

                                $fark = $c["bitis"] - time(); // Kalan süreyi hesapla
                                if($fark > 0) {
                                    $kalan_sure = $fark . " sn";
                                } else {
                                    $kalan_sure = "Süresi Doldu (Yenile)";
                                    $sure_bitti_mi = true;
                                }
                            ?>
                            <tr class="<?php echo $sure_bitti_mi ? 'table-danger' : ''; ?>">
                                <td><?php echo $c["ad"]; ?></td>
                                <td><?php echo $c["deger"]; ?></td>
                                <td><span class="badge bg-secondary"><?php echo $c["sure"]; ?></span></td>
                                <td><small class="fw-bold timer-counter <?php echo $sure_bitti_mi ? 'text-danger' : 'text-success'; ?>" data-bitis="<?php echo $c['bitis']; ?>"><?php echo $kalan_sure; ?></small></td>
                                <td><a href="cerez.php?sil=<?php echo $c["ad"]; ?>" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> SİL</a></td>
                            </tr>
                            <?php } ?>
                            
                            <?php if(count($kullanici_cerezleri) == 0) { ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Hiç çerez bulunamadı.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- YERLEŞİK ÇEREZLER KUTUSU -->
            <div class="kutu bg-light border-danger">
                <h5 class="baslik text-danger"><i class="fas fa-microchip"></i> Yerleşik Çerezler</h5>

                <hr>
                
                <div class="row">
                    <!-- Hazır çerezler burada listelenir -->
                    <div class="col-md-6 mb-2">
                        <div class="d-flex justify-content-between align-items-center bg-white p-2 rounded border">
                            <span class="fw-bold">SistemSürümü</span>
                            <span class="badge bg-secondary"><?php echo isset($_COOKIE["SistemSürümü"]) ? $_COOKIE["SistemSürümü"] : "Yok"; ?></span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="d-flex justify-content-between align-items-center bg-white p-2 rounded border">
                            <span class="fw-bold">SiteTemasi</span>
                            <span class="badge bg-secondary"><?php echo isset($_COOKIE["SiteTemasi"]) ? $_COOKIE["SiteTemasi"] : "Yok"; ?></span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="d-flex justify-content-between align-items-center bg-white p-2 rounded border">
                            <span class="fw-bold">DilSecimi</span>
                            <span class="badge bg-secondary"><?php echo isset($_COOKIE["DilSecimi"]) ? $_COOKIE["DilSecimi"] : "Yok"; ?></span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="d-flex justify-content-between align-items-center bg-white p-2 rounded border">
                            <span class="fw-bold">SonZiyaret</span>
                            <span class="badge bg-secondary"><?php echo isset($_COOKIE["SonZiyaret"]) ? $_COOKIE["SonZiyaret"] : "Yok"; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript ile Geri Sayım Sayacı -->
<script>
setInterval(function() {
    var now = Math.floor(Date.now() / 1000); // Şimdiki zaman
    var timers = document.querySelectorAll('.timer-counter'); // Tüm sayaçları seç
    
    timers.forEach(function(el) {
        var bitis = parseInt(el.getAttribute('data-bitis'));
        var fark = bitis - now;
        
        if(fark > 0) {
            el.innerHTML = fark + " sn"; // Ekrana yaz
            el.className = "fw-bold timer-counter text-success";
        } else {
            el.innerHTML = "Süresi Doldu (Siliniyor...)";
            el.className = "fw-bold timer-counter text-danger";
            
            // 2 saniye sonra sayfayı yenile ki listeden silinsin
            setTimeout(function() {
                location.reload();
            }, 2000);
        }
    });
}, 1000); // 1 saniyede bir çalış
</script>
</script>
<?php include("footer.php"); ?>
</body>
</html>
