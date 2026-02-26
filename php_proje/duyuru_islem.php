<?php
session_start(); // Oturum başlat

if(!isset($_SESSION["id"]) || $_SESSION["id"] < 1) { // Güvenlik kontrolü
    die("Yetkisiz erişim."); // Giriş yapmamışsa durdur
}

$dosya_yolu = "duyurular.txt"; // Veri dosyası

// --- SİLME İŞLEMİ ---
if(isset($_GET["sil_id"])) { // URL'den sil ID'si geldiyse
    $sil_id = $_GET["sil_id"];
    
    $mevcut = file($dosya_yolu, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); // Dosyayı oku
    $yeni_liste = array(); // Silinmiş halini tutacak liste
    
    foreach($mevcut as $satir) {
        $parca = explode("|", $satir);
        if($parca[0] != $sil_id) { // ID eşleşmiyorsa listeye ekle (Silinen hariç hepsini al)
            $yeni_liste[] = $satir;
        }
    }
    
    // Dosyayı yeniden yaz (Silinen satır hariç)
    file_put_contents($dosya_yolu, implode("\n", $yeni_liste) . (count($yeni_liste) > 0 ? "\n" : ""));
    header("Location: duyurular.php"); // Geri yönlendir
}

/* --- DÜZENLEME İŞLEMİ ---
   Formdan gelen verilerle mevcut satırı bulup güncelleyeceğiz.
   Aslında sil + ekle mantığı da kullanılabilir ama güncelleme daha temizdir.
*/
if(isset($_POST["duzenle"])) { // Düzenle butonu geldiyse
    $id = $_POST["id"]; // Hangi duyuru?
    $baslik = str_replace("|", "", strip_tags($_POST["baslik"])); // Temizlik
    $icerik = str_replace("|", "", strip_tags($_POST["icerik"]));
    $icerik = str_replace(array("\r", "\n"), " ", $icerik); // Satır sonlarını boşluğa çevir (Dosya yapısı bozulmasın)
    
    $mevcut = file($dosya_yolu, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $yeni_liste = array();
    
    foreach($mevcut as $satir) {
        $parca = explode("|", $satir);
        if($parca[0] == $id) { // Düzenlenecek satır bulundu
            // Eski verileri koru veya güncelle
            $eski_tarih = $parca[3];
            $eski_yazan = $parca[4];
            
            $yeni_tarih = date("d.m.Y H:i"); // Güncelleme tarihini şimdi yap
            
            // Yeni satırı oluştur format: id|baslik|icerik|tarih|yazan
            $yeni_satir = "$id|$baslik|$icerik|$yeni_tarih|$eski_yazan";
            $yeni_liste[] = $yeni_satir; // Güncel hali ekle
        } else {
            $yeni_liste[] = $satir; // Diğerlerini aynen ekle
        }
    }
    
    // Dosyayı kaydet
    file_put_contents($dosya_yolu, implode("\n", $yeni_liste) . (count($yeni_liste) > 0 ? "\n" : ""));
    header("Location: duyurular.php");
}
?>
