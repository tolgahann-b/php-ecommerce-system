<?php
include("db.php");
if(!isset($user_id)) { $user_id = 0; }
$oturum_var = ($user_id > 0);

if(!$oturum_var) { die("Giriş yapmalısınız!"); } // Güvenlik
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Detayları - Bülbül Motor</title>
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
        <li class="nav-item"><a class="nav-link" href="cerez.php">Çerezler</a></li>
        <li class="nav-item"><a class="nav-link active" href="ozel_sayfa.php">Session Bilgi</a></li>
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
    <div class="kutu">
        <h3 class="baslik">PHP Session Değişkenleri</h3>
        <p>Arkaplanda tutulan ham session verisi:</p>
        <div class="alert alert-secondary text-dark">
            <pre><?php print_r($_SESSION); // Debug amaçlı session içeriğini bas ?></pre>
        </div>
        
        <p><b>Aktif Session ID:</b> <?php echo session_id(); ?></p>
        <a href="index.php" class="btn btn-primary-custom">Geri Dön</a>
    </div>
</div>
</div>
<?php include("footer.php"); ?>
</body>
</html>
