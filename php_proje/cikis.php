<?php
include("db.php"); // session_start() burada

if(isset($_GET['id'])) { // Belirli bir ID'den mi çıkılacak?
    $silinecek_id = (int)$_GET['id'];
    
    // Listeden sil
    if(isset($_SESSION['hesaplar'][$silinecek_id])) {
        unset($_SESSION['hesaplar'][$silinecek_id]); // Diziden kaldır
    }
    
    // Eğer silinen hesap şu anki aktif hesap ise
    if($_SESSION['aktif_hesap_id'] == $silinecek_id) {
        // Başka hesap var mı bak
        if(count($_SESSION['hesaplar']) > 0) {
            // İlk bulduğunu aktif yap
            $yeni_aktif = current($_SESSION['hesaplar']); // Array'in ilk elemanı
            $_SESSION['aktif_hesap_id'] = $yeni_aktif['id'];
        } else {
            // Hiç hesap kalmadı, tamamen oturumu kapat
            $_SESSION['aktif_hesap_id'] = 0;
            session_destroy();
        }
    }
} else {
    // ID gönderilmediyse Hepsinden çık (Tamamen oturumu kapat)
    session_destroy();
}

header("Location: index.php"); // Yönlendir
?>
