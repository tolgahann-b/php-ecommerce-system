<?php
session_start(); // Kullanıcı oturumunu başlatır.

// --- VERİTABANI BAĞLANTI AYARLARI ---
$host = "localhost"; // Veritabanı sunucusu (Localhost)
$kullanici = "root"; // Veritabanı kullanıcı adı
$sifre = ""; // Veritabanı şifresi
$veritabani = "uyelik_sistemi"; // Bağlanılacak veritabanı adı

// @mysql_connect: Eski PHP sürümleri için bağlantı kurar. (@ hatayı gizler)
$baglanti = @mysql_connect($host, $kullanici, $sifre); 

if (!$baglanti) { // Bağlantı başarısızsa
    die("Veritabani Baglantisi Kurulamadi: " . mysql_error()); // Hata mesajı bas ve durdur
}

// Türkçe karakter sorunları için ayarlar
mysql_query("SET NAMES 'utf8'"); // Gelen veri seti
mysql_query("SET CHARACTER SET utf8"); // Bağlantı seti
mysql_query("SET COLLATION_CONNECTION = 'utf8_turkish_ci'"); // Sıralama ayarı

if(!@mysql_select_db($veritabani, $baglanti)) { // Veritabanını seçmeyi dene
    // DB yoksa install.php halleder
}

/* -------------------------------------------------------------
   MULTI-SESSION YÖNETİMİ (KÜRESEL DEĞİŞKENLER)
   Burada oturum dizisini inceliyoruz ve 'aktif' olan kullanıcının
   bilgilerini $user_id, $ad_soyad gibi global değişkenlere atıyoruz.
------------------------------------------------------------- */
if(!isset($_SESSION['hesaplar'])) { // Session'da hesaplar dizisi yoksa
    $_SESSION['hesaplar'] = array(); // Boş dizi oluştur
}

if(!isset($_SESSION['aktif_hesap_id'])) { // Aktif hesap ID yoksa
    $_SESSION['aktif_hesap_id'] = 0; // Sıfırla
}

// Global değişkenleri varsayılan olarak boşalt
$user_id = 0; // Kullanıcı ID (0 = Giriş yok)
$ad_soyad = ""; // Kullanıcı Adı Soyadı
$_SESSION['id'] = 0; // Eski kod uyumluluğu için sıfırla

$aktif_id = $_SESSION['aktif_hesap_id']; // Mevcut aktif ID'yi al

// Eğer geçerli bir aktif hesap seçiliyse
if($aktif_id > 0 && isset($_SESSION['hesaplar'][$aktif_id])) {
    $user_id = $aktif_id; // Global ID'yi güncelle
    $ad_soyad = $_SESSION['hesaplar'][$aktif_id]['ad']; // Ad soyadı çek
    
    // Eski sayfalardaki kontroller için senkronizasyon
    $_SESSION['id'] = $user_id; 
    $_SESSION['ad'] = $ad_soyad;
    $_SESSION['mail'] = $_SESSION['hesaplar'][$aktif_id]['mail'];
}
?>
