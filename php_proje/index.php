<?php
include("db.php"); // Veritabanı ve Session ayarlarını çağır
$oturum_var = ($user_id > 0); // Kullanıcı giriş kontrolü (True/False)
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ana Sayfa - Bülbül Motor</title>
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
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link active" href="index.php">Ana Sayfa</a></li>
        <li class="nav-item"><a class="nav-link" href="duyurular.php">Duyurular</a></li>
        <li class="nav-item"><a class="nav-link" href="kullanici_arama.php">Kullanıcı Arama</a></li>
        <?php if ($oturum_var) { ?> <!-- Oturum varsa gizli menüleri göster -->
            <li class="nav-item"><a class="nav-link" href="cerez.php">Çerezler</a></li>
            <li class="nav-item"><a class="nav-link" href="ozel_sayfa.php">Session Bilgi</a></li>
            <li class="nav-item"><a class="nav-link" href="multi_session.php">Multi Session</a></li>
        <?php } ?>
      </ul>
      <div class="d-flex align-items-center">
        <?php if ($oturum_var) { ?>
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

<div class="container mt-5 pt-5">
    <div class="row">
            <?php
            $duyuru_txt = "duyurular.txt"; // Duyuru dosyasının yolu
            if(file_exists($duyuru_txt)) { // Dosya var mı kontrolü
                $satirlar = file($duyuru_txt, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); // Dosyayı satır satır oku
                
                if(count($satirlar) > 0) { // Dolu satır varsa
                    $son_satir = end($satirlar); // Son satırı al (En güncel duyuru)
                    $parca = explode("|", $son_satir); // Veriyi parçala (Pipe ile ayrılmış)
                    
                    if(count($parca) == 5) { // Format doğruysa (5 parça)
                        $marquee_baslik = $parca[1]; // Başlık
                        $marquee_icerik = $parca[2]; // İçerik
            ?>
            <!-- Kayan Yazı (Marquee Efekti) -->
            <div class="alert alert-danger p-2 d-flex align-items-center" role="alert" style="overflow: hidden;">
                <span class="badge bg-danger me-3 flex-shrink-0">SON DUYURU</span>
                <div style="overflow: hidden; white-space: nowrap; width: 100%;">
                    <div style="display: inline-block; white-space: nowrap; animation: seamlessLoop 60s linear infinite;">
                        <span class="d-inline-block">
                            <span class="fw-bold me-2"><?php echo $marquee_baslik; ?>:</span> 
                            <?php echo $marquee_icerik; ?> 
                            <span class="mx-5">***</span> 
                        </span>
                        <span class="d-inline-block"> <!-- Kesintisiz döngü için kopya -->
                            <span class="fw-bold me-2"><?php echo $marquee_baslik; ?>:</span> 
                            <?php echo $marquee_icerik; ?> 
                            <span class="mx-5">***</span> 
                        </span>
                    </div>
                </div>
            </div>
            
            <style>
            @keyframes seamlessLoop {
                0% { transform: translateX(0); }
                100% { transform: translateX(-50%); } /* Sona doğru kaydır */
            }
            </style>
            <?php 
                    }
                }
            } 
            ?>

        <!-- Hero Alanı -->
        <div class="col-md-12 text-center mb-5">
            <div class="kutu">
                <h1 class="display-4 fw-bold text-danger">Bülbül Motor</h1>
                <p class="lead text-muted">Motosikletiniz için en kaliteli yedek parça, aksesuar ve ekipmanların tek adresi.</p>
                <hr class="my-4 mx-auto" style="width: 50%; opacity: 0.2;">
                
                <?php if (!$oturum_var) { ?>
                    <p class="mb-4">Size özel kampanyalardan yararlanmak ve sipariş vermek için aramıza katılın.</p>
                    <a class="btn btn-primary-custom btn-lg px-5 me-2" href="uyegiris.php"><i class="fas fa-sign-in-alt"></i> Giriş Yap</a>
                    <a class="btn btn-outline-secondary btn-lg px-5" href="uyekayit.php">Kayıt Ol</a>
                <?php } else { ?>
                    <p class="mb-2">Hoşgeldin <b><?php echo $ad_soyad; ?></b>, yollara çıkmaya hazır mısın?</p>
                    <div class="alert alert-success d-inline-block px-4 py-2">
                        <i class="fas fa-motorcycle"></i> Keyifli Sürüşler!
                    </div>
                <?php } ?>
            </div>
        </div>
        
        <!-- Bilgilendirme Kartları -->
        <div class="col-md-4 mb-4">
            <div class="kutu h-100">
                <div class="text-center mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center bg-danger text-white rounded-circle" style="width: 60px; height: 60px;">
                        <i class="fas fa-users-cog fa-2x"></i>
                    </span>
                </div>
                <h4 class="text-center">Multi-Session Desteği</h4>
                <p class="text-secondary text-center small">Tek tarayıcıda birden fazla hesap yönetimi.</p>
                <hr>
                <p class="small">
                    Bu sistem, standart PHP oturum yönetimine ek olarak <b>çoklu kullanıcı</b> desteği sunar. 
                    Aynı tarayıcı üzerinden birden fazla hesapla giriş yapabilir, tek tıkla hesaplar arasında geçiş yapabilirsiniz.
                </p>
                <?php if($oturum_var) { ?>
                    <div class="text-center mt-3">
                        <a href="multi_session.php" class="btn btn-sm btn-outline-danger w-100">Yönetim Paneli</a>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="kutu h-100">
                <div class="text-center mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center bg-dark text-white rounded-circle" style="width: 60px; height: 60px;">
                        <i class="fas fa-database fa-2x"></i>
                    </span>
                </div>
                <h4 class="text-center">Çerezler</h4>
                <p class="text-secondary text-center small">Gelişmiş veri saklama ve senkronizasyon.</p>
                <hr>
                <p class="small">
                    Çerezleriniz (Cookies) sadece tarayıcıda değil, aynı zamanda <b>metin dosyasında (txt)</b> senkronize olarak saklanır. 
                    Belirlenen süre boyunca verileriniz güvenle korunur. Yerleşik sistem çerezleri de otomatik tanımlanır.
                </p>
                <?php if($oturum_var) { ?>
                    <div class="text-center mt-3">
                        <a href="cerez.php" class="btn btn-sm btn-outline-dark w-100">Çerezleri İncele</a>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="kutu h-100">
                <div class="text-center mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center bg-secondary text-white rounded-circle" style="width: 60px; height: 60px;">
                        <i class="fas fa-bullhorn fa-2x"></i>
                    </span>
                </div>
                <h4 class="text-center">Duyuru Sistemi</h4>
                <p class="text-secondary text-center small">Dosya tabanlı hızlı içerik yönetimi.</p>
                <hr>
                <p class="small">
                    Veritabanı bağımlılığı olmadan, <b>dosya sistemi (File I/O)</b> kullanılarak çalışan duyuru modülü. 
                    Başlık, içerik ve yazar bilgisi <code>.txt</code> formatında güvenli bir şekilde saklanır ve anında yayınlanır.
                </p>
                <div class="text-center mt-3">
                    <a href="duyurular.php" class="btn btn-sm btn-outline-secondary w-100">Duyurulara Git</a>
                </div>
            </div>
        </div>
    </div>
    
</div>

<?php include("footer.php"); ?>
</body>
</html>
