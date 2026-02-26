<?php
session_start(); // Oturum başlat

if(!isset($_SESSION["id"]) || $_SESSION["id"] < 1) {
    die("Yetkisiz erişim."); // Giriş kontrolü
}

if(isset($_POST["yeni_duyuru"])) { // Formdan veri geldi mi
    $baslik = strip_tags($_POST["baslik"]); // HTML etiketlerini temizle
    $icerik = strip_tags($_POST["yeni_duyuru"]);
    
    // Güvenlik ve Format Temizliği
    $baslik = str_replace("|", "", $baslik); // Dosya ayıracı olan pipe karakterini sil
    $icerik = str_replace("|", "", $icerik);
    $icerik = str_replace(array("\r", "\n"), " ", $icerik); // Alt satıra geçişleri temizle (Tek satır olmalı)
    
    $tarih = date("d.m.Y H:i"); // Şimdiki tarih
    $yazan = $_SESSION["ad"]; // Oturumdaki isim
    
    // Benzersiz ID oluştur (Zaman damgası + rastgele sayı)
    $id = time() . rand(100,999);
    
    // Dosyaya yazılacak format
    $satir = "$id|$baslik|$icerik|$tarih|$yazan\n";
    
    // Dosyayı 'a' (append/ekleme) modunda aç
    $dosya = fopen("duyurular.txt", "a");
    if($dosya) {
        fwrite($dosya, $satir); // Yaz
        fclose($dosya); // Kapat
        header("Location: duyurular.php"); // Yönlendir
    } else {
        echo "Dosya hatası.";
    }
}
?>
