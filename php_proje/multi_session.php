<?php
include("db.php"); // Veritabanı ve session başlatır
if(!isset($user_id)) { $user_id = 0; } // Hata önleme
$oturum_var = ($user_id > 0); // Giriş yapmış mı?

if(!$oturum_var) { // Yetkisiz giriş engelleme
    header("Location: uyegiris.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi Session Yönetimi - Bülbül Motor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        html { height: 100%; }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
        }
        footer {
            margin-top: auto !important;
            margin-bottom: 0 !important;
        }
        .flex-grow-1 {
            flex-grow: 1 !important;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
  <div class="container">
    <a class="navbar-brand" href="index.php"><i class="fas fa-motorcycle text-danger"></i> Bülbül Motor</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php">Ana Sayfa</a></li>
        <li class="nav-item"><a class="nav-link" href="duyurular.php">Duyurular</a></li>
        <li class="nav-item"><a class="nav-link" href="kullanici_arama.php">Kullanıcı Arama</a></li>
        <li class="nav-item"><a class="nav-link" href="cerez.php">Çerezler</a></li>
        <li class="nav-item"><a class="nav-link" href="ozel_sayfa.php">Session Bilgi</a></li>
        <li class="nav-item"><a class="nav-link active" href="multi_session.php">Multi Session</a></li>
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

<div class="container mt-5 pt-5 flex-grow-1">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="kutu">
                <h3 class="baslik">Multi Session Yönetimi</h3>
                <p>Şu an tarayıcıda açık olan tüm oturumların listesi:</p>
                
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ad Soyad</th>
                            <th>E-Mail</th>
                            <th>Durum</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($_SESSION['hesaplar'] as $h_id => $h_bilgi) { // Session dizisindeki tüm hesapları dön
                            $aktif_mi = ($h_id == $user_id); // Bu satırdaki hesap aktif mi?
                        ?>
                        <tr class="<?php echo $aktif_mi ? 'table-active' : ''; ?>"> <!-- Aktifse satırı vurgula -->
                            <td>#<?php echo $h_id; ?></td>
                            <td><?php echo $h_bilgi['ad']; ?></td>
                            <td><?php echo $h_bilgi['mail']; ?></td>
                            <td>
                                <?php if($aktif_mi) { ?>
                                    <span class="badge bg-success">AKTİF HESAP</span>
                                <?php } else { ?>
                                    <span class="badge bg-secondary">Arkaplanda</span>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if(!$aktif_mi) { ?> <!-- Aktif değilse geçiş yap butonu koy -->
                                    <a href="hesap_degistir.php?id=<?php echo $h_id; ?>" class="btn btn-sm btn-primary-custom px-3">GEÇİŞ YAP</a>
                                <?php } ?>
                                <a href="cikis.php?id=<?php echo $h_id; ?>" class="btn btn-sm btn-outline-danger ms-2">OTURUMU KAPAT</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                
                <div class="mt-4">
                    <a href="uyegiris.php" class="btn btn-outline-light"><i class="fas fa-plus"></i> Yeni Hesap Ekle</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include("footer.php"); ?>
</body>
</html>
