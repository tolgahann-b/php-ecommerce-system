<?php
include("db.php"); // Veritabanı bağlantısını dahil et
$oturum_var = ($user_id > 0); // Kullanıcı giriş yapmış mı kontrol et
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8"> <!-- Türkçe karakter desteği -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Arama - Bülbül Motor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
    <link href="style.css" rel="stylesheet"> <!-- Özel Stil Dosyası -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- İkon Seti -->
</head>
<body class="d-flex flex-column min-vh-100"> <!-- Footer'ı alta itmek için flex yapı -->

<nav class="navbar navbar-expand-lg navbar-dark fixed-top"> <!-- Üst Menü -->
  <div class="container">
    <a class="navbar-brand" href="index.php"><i class="fas fa-motorcycle text-danger"></i> Bülbül Motor</a> <!-- Logo -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php">Ana Sayfa</a></li>
        <li class="nav-item"><a class="nav-link" href="duyurular.php">Duyurular</a></li>
        <li class="nav-item"><a class="nav-link active" href="kullanici_arama.php">Kullanıcı Arama</a></li> <!-- Aktif Sayfa -->
        <?php if ($oturum_var) { ?> <!-- Sadece üyeler görebilir -->
            <li class="nav-item"><a class="nav-link" href="cerez.php">Çerezler</a></li>
            <li class="nav-item"><a class="nav-link" href="ozel_sayfa.php">Session Bilgi</a></li>
            <li class="nav-item"><a class="nav-link" href="multi_session.php">Multi Session</a></li>
        <?php } ?>
      </ul>
      <div class="d-flex align-items-center">
        <?php if ($oturum_var) { ?> <!-- Giriş yapılmışsa -->
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
        <?php } else { ?> <!-- Giriş yapılmamışsa -->
            <a href="uyegiris.php" class="btn btn-outline-light me-2">Giriş</a>
            <a href="uyekayit.php" class="btn btn-primary-custom">Kayıt Ol</a>
        <?php } ?>
      </div>
    </div>
  </div>
</nav>

<div class="flex-grow-1 d-flex align-items-center"> <!-- İçerik Alanı (Footer'ı iter) -->
    <div class="container mt-5 pt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="kutu">
                    <h3 class="baslik text-center"><i class="fas fa-search text-danger"></i> Kullanıcı Arama</h3>
                    <p class="text-center text-muted">Sistemdeki kullanıcıları isme göre arayabilirsiniz.</p>
                    <hr>

                    <!-- Arama Formu -->
                    <form action="kullanici_arama.php" method="POST" class="mb-5">
                        <div class="input-group input-group-lg">
                            <input type="text" name="kelime" class="form-control" placeholder="Aranacak isim..." required>
                            <button type="submit" name="arama" class="btn btn-danger">ARAMA YAP</button>
                        </div>
                    </form>

                    <?php 
                    if(isset($_POST["arama"])) // Arama butonuna basıldı mı?
                    {
                        if(isset($_POST["kelime"]) && !empty($_POST["kelime"])) // Kelime girildi mi?
                        {
                            $kelime = mysql_real_escape_string($_POST["kelime"]); // Güvenlik temizliği (SQL Injection)
                            $sorgu = mysql_query("select * from uyeler where ad like '%".$kelime."%'"); // Benzer isimleri ara
                            
                            $bulunan_sayisi = mysql_num_rows($sorgu); // Sonuç sayısı
                            
                            if($bulunan_sayisi > 0) // Sonuç varsa
                            {
                                echo "<div class='alert alert-success'><b>$bulunan_sayisi</b> sonuç bulundu.</div>";
                                echo "<div class='list-group'>";
                                while($dizi = mysql_fetch_array($sorgu)) // Sonuçları döngüye sok
                                {
                                    // Sonuçları listele
                                    echo "<div class='list-group-item list-group-item-action d-flex justify-content-between align-items-center'>";
                                    echo "<span><i class='fas fa-user me-2'></i> " . $dizi["ad"] . "</span>";
                                    echo "<span class='badge bg-secondary rounded-pill'>" . $dizi["mail"] . "</span>";
                                    echo "</div>"; 
                                }
                                echo "</div>";
                            }
                            else
                            {
                                echo "<div class='alert alert-warning text-center'><i class='fas fa-exclamation-circle'></i> Aradığınız kriterlere uygun kullanıcı bulunamadı.</div>"; // Sonuç yoksa
                            }
                        }
                        else
                        {
                            echo "<div class='alert alert-danger text-center'>Lütfen bir arama kelimesi giriniz..!</div>"; // Boş arama yapıldıysa
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("footer.php"); ?> <!-- Footer dosyasını çağır -->
</body>
</html>
