<?php
include("db.php"); // Veritabanı bağlantısı
$mesaj = ""; // Durum mesajı

// Hata değişkenleri ve eski değerler (Formda geri göstermek için)
$ad_err = $tc_err = $sifre_err = $mail_err = $adres_err = "";
$ad_val = $tc_val = $sifre_val = $mail_val = $adres_val = "";

if(isset($_POST["kaydet"])) { // Form gönderildi mi?
    // 1. Değerleri Al
    $ad     = isset($_POST["ad"]) ? trim($_POST["ad"]) : ""; // Adı temizle
    $tc     = isset($_POST["tc"]) ? trim($_POST["tc"]) : ""; // TC'yi temizle
    $sifre  = isset($_POST["sifre"]) ? trim($_POST["sifre"]) : "";
    $mail   = isset($_POST["mail"]) ? trim($_POST["mail"]) : "";
    $adres  = isset($_POST["adres"]) ? trim($_POST["adres"]) : "";

    // 2. Formda göstermek için sakla (Güvenli hale getirerek)
    $ad_val = htmlspecialchars($ad);
    $tc_val = htmlspecialchars($tc);
    $mail_val = htmlspecialchars($mail);
    $adres_val = htmlspecialchars($adres);

    $eksik_alanlar = array(); // Hatalı alanları tutar

    // --- Validasyon Kontrolleri ---
    
    // 1. Ad Soyad Kontrolü (Sadece Harf ve Boşluk)
    if(empty($ad)) {
        $eksik_alanlar[] = "Ad Soyad";
        $ad_err = "is-invalid";
    } elseif(!preg_match("/^[a-zA-ZçÇğĞıİöÖşŞüÜ ]+$/", $ad)) {
        $eksik_alanlar[] = "Ad Soyad (Geçersiz Karakter)";
        $mesaj = "<div class='alert alert-danger'>Ad Soyad sadece harf içerebilir!</div>";
        $ad_err = "is-invalid";
    }

    // 2. TC Kimlik Kontrolü
    if(empty($tc)) {
        $eksik_alanlar[] = "TC Kimlik";
        $tc_err = "is-invalid";
    } elseif(!ctype_digit($tc) || strlen($tc) != 11) {
        $eksik_alanlar[] = "TC Kimlik (11 Haneli Rakam)";
        $tc_err = "is-invalid";
    }

    // 3. Şifre Kontrolü (Güçlü Şifre Zorunluluğu)
    if(empty($sifre)) {
        $eksik_alanlar[] = "Şifre";
        $sifre_err = "is-invalid";
    } else {
        $guclu_mu = true;
        if(strlen($sifre) < 8) $guclu_mu = false;
        if(!preg_match('/[A-Z]/', $sifre)) $guclu_mu = false; // Büyük harf
        if(!preg_match('/[a-z]/', $sifre)) $guclu_mu = false; // Küçük harf
        if(!preg_match('/[0-9]/', $sifre)) $guclu_mu = false; // Rakam
        
        if(!$guclu_mu) {
            $eksik_alanlar[] = "Şifre (Yetersiz Güç)";
            $mesaj = "<div class='alert alert-danger'><i class='fas fa-lock'></i> Şifreniz çok zayıf! En az 8 karakter, 1 büyük harf, 1 küçük harf ve 1 rakam içermeli.</div>";
            $sifre_err = "is-invalid";
        }
    }

    // 4. Email Kontrolü (Format)
    if(empty($mail)) {
        $eksik_alanlar[] = "E-Mail";
        $mail_err = "is-invalid";
    } elseif(!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $eksik_alanlar[] = "Geçersiz E-Mail Formatı";
        $mail_err = "is-invalid";
    }

    // 5. Adres Kontrolü (Min 10 Karakter)
    if(empty($adres)) {
        $eksik_alanlar[] = "Adres";
        $adres_err = "is-invalid";
    } elseif(strlen($adres) < 10) {
        $eksik_alanlar[] = "Adres (Çok kısa, detaylandırın)";
        $adres_err = "is-invalid";
    }

    // 4. Sonuç Değerlendirme
    if(count($eksik_alanlar) > 0) {
        // Eksik alan varsa kullanıcıya bildir
        $hata_metni = implode(", ", $eksik_alanlar);
        $mesaj = "<div class='alert alert-danger'><i class='fas fa-exclamation-triangle'></i> Lütfen şu alanları doldurun: <b>$hata_metni</b></div>";
    } else {
        // Hata yok, devam etmeden önce mükerrer kayıt kontrolü yap
        
        // SQL Injection önlemleri (kontrol sorguları için de gerekli)
        $ad = mysql_real_escape_string($ad);
        $mail_temiz = mysql_real_escape_string($mail);
        $adres = mysql_real_escape_string($adres);
        $tc_temiz = mysql_real_escape_string($tc);
        
        // --- AYNI KAYIT KONTROLÜ ---
        $hatalar = array(); // Hata mesajlarını topla
        
        // 1. Aynı E-Posta Kontrolü
        $mail_sorgu = "SELECT id FROM uyeler WHERE mail = '$mail_temiz'";
        $mail_sonuc = mysql_query($mail_sorgu);
        if(mysql_num_rows($mail_sonuc) > 0) {
            $hatalar[] = "E-posta adresi"; // Hatayı diziye ekle
            $mail_err = "is-invalid"; // E-posta alanını kırmızı yap
        }
        
        // 2. Aynı TC Kimlik Kontrolü (her zaman kontrol et)
        $tc_sorgu = "SELECT id FROM uyeler WHERE tc = '$tc_temiz'";
        $tc_sonuc = mysql_query($tc_sorgu);
        if(mysql_num_rows($tc_sonuc) > 0) {
            $hatalar[] = "TC Kimlik numarası"; // Hatayı diziye ekle
            $tc_err = "is-invalid"; // TC alanını kırmızı yap
        }
        
        // Kayıtlı bilgi varsa uyarı göster
        if(count($hatalar) > 0) {
            $hata_listesi = implode(" ve ", $hatalar); // "E-posta adresi ve TC Kimlik numarası"
            $mesaj = "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> <b>$hata_listesi</b> zaten kayıtlı! Lütfen bilgilerinizi kontrol edin.</div>";
        }
        
        // Kayıtlı değilse devam et
        if(count($hatalar) == 0) {
            // Kayıt işlemine başla
            $sifreli_sifre = md5($sifre); // Güvenlik için şifreleme
            
            // Veritabanına ekle
            $sql = "INSERT INTO uyeler (ad, sifre, tc, mail, adres) VALUES ('$ad', '$sifreli_sifre', '$tc_temiz', '$mail_temiz', '$adres')";
            $sonuc = mysql_query($sql);
        
            if($sonuc) { // Başarılıysa
                $mesaj = "<div class='alert alert-success'><i class='fas fa-check-circle'></i> Kayıt Başarılı! Yönlendiriliyorsunuz...</div>";
                header("refresh:2;url=uyegiris.php"); // 2 saniye sonra yönlendir
            } else { // Başarısızsa
                $mesaj = "<div class='alert alert-danger'>Veritabanı Hatası: " . mysql_error() . "</div>";
            }
        } // if(count($hatalar) == 0) kapanışı
    } // else (eksik alan yok) kapanışı
} // if(isset($_POST["kaydet"])) kapanışı
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="d-flex flex-column min-vh-100">

<div class="flex-grow-1 d-flex align-items-center justify-content-center">
    <div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <h2 class="text-center text-danger fw-bold mb-5 display-6" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">BÜLBÜL MOTOR</h2>
            <div class="kutu p-5">
                <div class="text-center mb-4">
                    <i class="fas fa-user-plus text-danger fa-2x mb-2"></i>
                    <h3 class="baslik baslik-center border-0 pb-0 mb-0">Hemen Kayıt Ol</h3>
                    <p class="text-muted">Aramıza katılmak için formu doldur.</p>
                </div>
                
                <?php echo $mesaj; ?> <!-- Durum mesajını göster -->
                
                <form action="uyekayit.php" method="POST" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Ad Soyad</label>
                        <div class="input-group has-validation">
                            <span class="input-group-text bg-dark border-secondary text-secondary"><i class="fas fa-user"></i></span>
                            <input type="text" name="ad" class="form-control <?php echo $ad_err; ?>" value="<?php echo $ad_val; ?>" placeholder="Tam Adınız">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">TC Kimlik</label>
                        <input type="text" name="tc" class="form-control <?php echo $tc_err; ?>" value="<?php echo $tc_val; ?>" placeholder="11 Haneli No">
                    </div>
                        <div class="mb-3">
                            <label class="form-label">Şifre</label>
                            <div class="input-group">
                                <input type="password" name="sifre" id="sifreInput" class="form-control <?php echo $sifre_err; ?>" placeholder="******">
                                <button type="button" class="btn btn-outline-secondary" onclick="var inp=document.getElementById('sifreInput'); var ico=this.querySelector('i'); if(inp.type==='password'){inp.type='text';ico.className='fas fa-eye-slash';}else{inp.type='password';ico.className='fas fa-eye';}">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            
                            <!-- Şifre Güç Göstergesi -->
                            <div class="mt-2">
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar" id="sifreBar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted" id="sifreText">Şifre Gücü: Girilmedi</small>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <i class="fas fa-info-circle"></i> Min 8 Karakter, Büyük/Küçük Harf, Rakam
                                    </small>
                                </div>
                            </div>
                        </div>

                    
                    <script>
                    document.getElementById('sifreInput').addEventListener('input', function() {
                        var val = this.value;
                        var bar = document.getElementById('sifreBar');
                        var text = document.getElementById('sifreText');
                        
                        var guc = 0;
                        
                        // Kriterler
                        if(val.length >= 8) guc += 25;
                        if(val.match(/[a-z]/)) guc += 25;
                        if(val.match(/[A-Z]/)) guc += 25;
                        if(val.match(/[0-9]/)) guc += 25;
                        
                        // Renk ve Genişlik Ayarı
                        bar.style.width = guc + "%";
                        
                        if(guc < 50) {
                            bar.className = "progress-bar bg-danger";
                            text.innerHTML = "Şifre Gücü: <span class='text-danger fw-bold'>Zayıf</span>";
                        } else if(guc < 100) {
                            bar.className = "progress-bar bg-warning";
                            text.innerHTML = "Şifre Gücü: <span class='text-warning fw-bold'>Orta</span>";
                        } else {
                            bar.className = "progress-bar bg-success";
                            text.innerHTML = "Şifre Gücü: <span class='text-success fw-bold'>Güçlü</span>";
                        }
                    });
                    </script>

                    <div class="mb-3">
                        <label class="form-label">E-Mail Adresi</label>
                        <div class="input-group has-validation">
                            <span class="input-group-text bg-dark border-secondary text-secondary"><i class="fas fa-at"></i></span>
                            <input type="email" name="mail" class="form-control <?php echo $mail_err; ?>" value="<?php echo $mail_val; ?>" placeholder="mail@ornek.com">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Adres</label>
                        <textarea name="adres" class="form-control <?php echo $adres_err; ?>" rows="2" placeholder="Açık adresiniz..."><?php echo $adres_val; ?></textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" name="kaydet" class="btn btn-primary-custom btn-lg">KAYIT OL</button>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="uyegiris.php" class="text-danger text-decoration-none fw-bold">Zaten üye misin? Giriş Yap</a>
                        <br>
                        <a href="index.php" class="text-muted small text-decoration-none mt-2 d-inline-block">Ana Sayfa</a>
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
