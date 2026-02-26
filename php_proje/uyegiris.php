<?php
include("db.php"); // DB bağlantısını dahil et
$mesaj = ""; // Hata mesajı değişkenini başlat

// Form hata değişkenleri
$mail_err = $sifre_err = "";
$mail_val = ""; // Form tekrar dolduğunda mail silinmesin diye

if(isset($_POST["giris"])) { // Giriş butonuna basıldıysa
    $mail = isset($_POST["mail"]) ? trim($_POST["mail"]) : ""; // Mail'i al, boşlukları temizle
    $sifre = isset($_POST["sifre"]) ? trim($_POST["sifre"]) : ""; // Şifreyi al
    
    $mail_val = htmlspecialchars($mail); // XSS koruması için özel karakterleri dönüştür
    
    $eksik_alanlar = array(); // Eksik alanlar listesi
    
    if(empty($mail)) { 
        $eksik_alanlar[] = "E-Mail";
        $mail_err = "is-invalid"; 
    } elseif(!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $eksik_alanlar[] = "Geçersiz E-Mail Formatı";
        $mail_err = "is-invalid";
    }
    
    if(empty($sifre)) { // Şifre boş mu
        $eksik_alanlar[] = "Şifre";
        $sifre_err = "is-invalid";
    }
    
    if(count($eksik_alanlar) > 0) { // Eksik varsa
        $hata_metni = implode(", ", $eksik_alanlar);
        $mesaj = "<div class='alert alert-danger'><i class='fas fa-exclamation-triangle'></i> Lütfen şu alanları doldurun: <b>$hata_metni</b></div>";
    } else {
        // --- GİRİŞ KONTROLÜ ---
        $mail = mysql_real_escape_string($mail); // SQL Injection temizliği
        $sifre = md5($sifre); // Şifreyi MD5 ile şifrele
        
        $sql = "SELECT * FROM uyeler WHERE mail='$mail' AND sifre='$sifre'"; // Kullanıcıyı ara
        $sonuc = mysql_query($sql);
        
        if(mysql_num_rows($sonuc) > 0) { // Kullanıcı bulunduysa
            $uye = mysql_fetch_array($sonuc); // Verileri çek
            $id = $uye["id"];
            
            // --- ZATEN AKTİF HESAP KONTROLÜ ---
            if(isset($_SESSION['hesaplar'][$id])) {
                // Bu hesap zaten aktif oturumda
                $mesaj = "<div class='alert alert-warning mb-3'><i class='fas fa-exclamation-circle'></i> <b>" . $uye['ad'] . "</b> hesabı zaten aktif! <a href='multi_session.php' class='fw-bold'>Hesapları Yönet</a> sayfasından geçiş yapabilirsiniz.</div>";
            } else {
                // --- MULTI SESSION ---
                $_SESSION['hesaplar'][$id] = array( // Session dizisine ekle
                    'id' => $uye['id'],
                    'ad' => $uye['ad'],
                    'mail' => $uye['mail']
                );
                $_SESSION['aktif_hesap_id'] = $id; // Aktif ID yap
                
                header("Location: index.php"); // Ana sayfaya yönlendir
                exit;
            }
        } else {
            $mesaj = "<div class='alert alert-danger mb-3'><i class='fas fa-exclamation-triangle'></i> Hatalı E-Mail veya Şifre!</div>";
        }
    }
}

if(!isset($user_id)) $user_id = 0; // User ID yoksa 0 yap
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link href="style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="d-flex flex-column min-vh-100">

<div class="flex-grow-1 d-flex align-items-center justify-content-center"> <!-- Sayfa ortalama -->
    <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <h2 class="text-center text-danger fw-bold mb-5 display-6" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">BÜLBÜL MOTOR</h2>
            
            <div class="kutu text-center p-5">
                <i class="fas fa-motorcycle text-danger fa-3x mb-3"></i>
                <h3 class="baslik mb-4">Üye Girişi</h3>
                
                <?php 
                if($user_id > 0) { // Zaten giriş yapmışsa bilgi ver
                    echo "<div class='alert alert-info mb-3'><i class='fas fa-user-check'></i> Şu an <b>$ad_soyad</b> olarak bağlısınız. Yeni hesap ekleyebilirsiniz.</div>";
                }
                echo $mesaj; // Hata mesajını bas
                ?>
                
                <form action="uyegiris.php" method="POST" novalidate>
                    <div class="mb-3 text-start">
                        <label class="form-label">Email Adresi</label>
                        <div class="input-group has-validation">
                            <span class="input-group-text bg-dark border-secondary text-secondary"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="mail" class="form-control <?php echo $mail_err; ?>" value="<?php echo $mail_val; ?>" placeholder="ornek@mail.com">
                        </div>
                    </div>
                    
                    <div class="mb-4 text-start">
                        <label class="form-label">Şifre</label>
                        <div class="input-group has-validation">
                            <span class="input-group-text bg-dark border-secondary text-secondary"><i class="fas fa-lock"></i></span>
                            <input type="password" name="sifre" class="form-control <?php echo $sifre_err; ?>" placeholder="******">
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" name="giris" class="btn btn-primary-custom btn-lg shadow-sm">GİRİŞ YAP <i class="fas fa-arrow-right ms-2"></i></button>
                    </div>
                    
                    <div class="text-center mt-4">
                        <p class="mb-1 text-muted">Hesabın yok mu?</p>
                        <a href="uyekayit.php" class="text-danger text-decoration-none fw-bold">Kayıt Ol</a>
                        <span class="mx-2 text-muted">|</span>
                        <a href="index.php" class="text-muted text-decoration-none">Ana Sayfa</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</div>
</div>


</body>
</html>
