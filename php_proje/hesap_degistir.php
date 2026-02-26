<?php
include("db.php"); // Session başlatmak için

if(isset($_GET['id'])) { // ID gelmiş mi
    $hedef_id = (int)$_GET['id'];
    
    // Güvenlik: Kullanıcı gerçekten bu oturuma sahip mi? Sadece kendi session'ındaki hesaplar arası geçiş yapabilir.
    if(isset($_SESSION['hesaplar'][$hedef_id])) {
        $_SESSION['aktif_hesap_id'] = $hedef_id; // Aktif ID'yi değiştir
    }
}

// Nereden geldiyse oraya (veya ana sayfaya) dön
if(isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    header("Location: index.php");
}
?>
