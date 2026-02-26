-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 13 Oca 2026, 13:07:29
-- Sunucu sürümü: 10.1.13-MariaDB
-- PHP Sürümü: 5.5.37

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `uyelik_sistemi`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `uyeler`
--

CREATE TABLE `uyeler` (
  `id` int(6) UNSIGNED NOT NULL,
  `ad` varchar(50) COLLATE utf8_turkish_ci NOT NULL,
  `sifre` varchar(255) COLLATE utf8_turkish_ci NOT NULL,
  `tc` varchar(11) COLLATE utf8_turkish_ci NOT NULL,
  `mail` varchar(50) COLLATE utf8_turkish_ci NOT NULL,
  `adres` text COLLATE utf8_turkish_ci NOT NULL,
  `kayit_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `uyeler`
--

INSERT INTO `uyeler` (`id`, `ad`, `sifre`, `tc`, `mail`, `adres`, `kayit_tarihi`) VALUES
(1, 'Türker İronmaker', '8f10d078b2799206cfe914b32cc6a5e9', '64616496', 'turker_ironmaker37@gmail.com', 'KASTAMONU''NUN YAKIŞIKLISI', '2026-01-12 10:14:46'),
(2, 'Tolgahan Bülbül', '0efe53366e7e886c9203698b425ee8bf', '1464697949', 'tolgahanbulbul66@gmail.com', 'deneme2', '2026-01-12 10:32:38'),
(3, 'Hesap 3', '1f2cd90af2150de6edf00f3d65f5921b', '14457896244', 'hesap3@gmail.com', 'Hesap 3', '2026-01-12 16:44:32'),
(4, 'Hesap 4', '8e7078558ca583de743d456fb7935913', '44789655412', 'hesap4@gmail.com', 'Hesap 4', '2026-01-12 16:45:54');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `uyeler`
--
ALTER TABLE `uyeler`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `uyeler`
--
ALTER TABLE `uyeler`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
