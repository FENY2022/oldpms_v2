-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 11, 2026 at 06:02 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `v2_oldpms`
--

-- --------------------------------------------------------

--
-- Table structure for table `user_client`
--

CREATE TABLE `user_client` (
  `client_id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `mid_name` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `verification_token` varchar(255) DEFAULT NULL,
  `password` text NOT NULL,
  `mobilenum` text NOT NULL,
  `comp_id_upload` longtext NOT NULL,
  `govt_id_upload` longtext NOT NULL,
  `auth_letter` longtext NOT NULL,
  `password_unhashed` text NOT NULL,
  `Status` int(11) NOT NULL,
  `province` text NOT NULL,
  `citymun` text NOT NULL,
  `brgy` text NOT NULL,
  `zips` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_client`
--

INSERT INTO `user_client` (`client_id`, `firstname`, `mid_name`, `lastname`, `email`, `verification_token`, `password`, `mobilenum`, `comp_id_upload`, `govt_id_upload`, `auth_letter`, `password_unhashed`, `Status`, `province`, `citymun`, `brgy`, `zips`) VALUES
(25, 'ANTHONIE FENY', 'VENZON', 'CATALAN', 'venzonanthonie@gmail.com', NULL, '$2y$10$TZYNXuaJNfi9sBhlc01joeEHQwpde9.qMFXsrg59qeBSEDlOECoc6', '09101761895', 'PDF-654834ffb2e3d9.41986084.jpg', '', 'PDF-654834ffb32ae8.39574224.jpg', 'feny9959', 1, '', '', '', ''),
(29, 'JESTONIE', 'M', 'ALARCON', 'jestonie@gmail.com', NULL, '$2y$10$bh8XCBNApr4s4J3LewV7yuoyoAGe89gFpdlYyY3ylQwlZzkeK2BHy', '09101761895', 'PDF-65ed8ec7b6ec87.85769514.jpg', '', 'PDF-65ed8ec7bc4b97.81925472.jpg', 'jestonie2024', 1, '', '', '', ''),
(43, 'Maria', 'M', 'Orteza', 'mariaorteza@gmail.com', NULL, '$2y$10$CsIr5Tvp5yqMB69rfpvhKuhhhaDl0Tnp4UNaHT3zBIJhcRyplfTqu', '09101761895', 'PDF-65ee62e69b0bf9.89369258.jpg', '', 'PDF-65ee62e69b4089.99002420.jpg', 'mariaorteza2024', 1, '', '', '', ''),
(44, 'Maria', 'M', 'Alarcon', 'mariaalarcon@gmail.com', NULL, '$2y$10$BJAnPTg6uwf/UrMpgpJcG.RiCJDyJFkvZPEefh3Y4Ke9z7Ptj2iJa', '09101761895', 'PDF-65efb6eb3c3187.94055354.jpg', '', 'PDF-65efb6eb420064.83151958.jpg', 'mariaalarcon2024', 1, '', '', '', ''),
(45, 'DESA', 'U', 'OCHEA', 'desa@gmail.com', NULL, '$2y$10$AOOjsy4fg22699jbu2rVrOIG9hGpx0OyJ2ihu/rCEisH5xHp/YoeW', '09101761895', 'PDF-660ba6647aa402.24066864.jpg', '', 'PDF-660ba6648102b1.74951804.jpg', 'desa2024', 1, '', '', '', ''),
(46, 'Antonio', 'Maldito', 'Banderama', 'angmaldito@mail.com', NULL, '$2y$10$JSPwZaIjHqEF0k/z6GGqsua4YLc1lX4MlGV9TXbjHk5AnUKsZGzF.', '09999999999', 'PDF-660e09df1aaf37.32563897.jpg', '', 'PDF-660e09df206084.94706333.jpg', 'maldita', 1, '', '', '', ''),
(47, 'DESA', 'U', 'OCHEA', 'desa1@gmail.com', NULL, '$2y$10$TtC0Wua6hUv3iOc7k/pmKOcuBJQIS4BAj4UkhlWBAqIPzGQeQqPku', '09101761895', 'PDF-6613956c9a8926.21015912.jpg', '', 'PDF-6613956ca00549.46911968.jpg', '123223', 1, '', '', '', ''),
(48, 'Narly', 'Morta', 'Perang', 'nhoeladeneca@gmail.com', NULL, '$2y$10$NMuAnpCS5a.lQ.RMU2AN6umrhYz8NyJi.RKC05w5rl7HgQR0tCro2', '09565684761', 'PDF-6618af75a03631.41491053.jpg', '', 'PDF-6618af75a6f477.31643711.jpg', 'perangfamily2024', 1, '', '', '', ''),
(49, 'Ester', 'Talaroc', 'Pabillore', 'hantelnicholas@gmail.com', NULL, '$2y$10$l.3ueJaw415TNY9VXTn0MOwQYGFbJ53jAZ5PetIF09Wubfnn.FBle', '09206034642', 'PDF-6618afb2408d98.29146877.png', '', 'PDF-6618afb240eba7.36634775.png', 'ETP', 1, '', '', '', ''),
(50, 'bayang', 'fdfd', 'dfdfdf', 'bayangsuluh@protonmail.com', NULL, '$2y$10$A2vTmS1tjIE2f25qBu4IFegV4oBcJg7Ysq4HM47QqOSAhb1s8cln2', '23223232323', 'PDF-6621ea273f4471.86451798.jpg', '', 'PDF-6621ea27446989.30398522.jpg', 'Bayang@123', 1, '', '', '', ''),
(51, 'ANTHONIE FENY', 'VENZON', 'CATALAN', 'venzonanthonie4@gmail.com', NULL, '$2y$10$zFZGXnmePR54Z7ixXVoNCOms3DYXHaBySsSofZFqUXOTuzPsVbbPG', '09874561231', 'PDF-669778e5b9e939.97346555.jpg', '', 'PDF-669778e5ba8955.46225425.jpg', 'feny2024', 0, '1602', '160201', '160201021', '    8601'),
(52, 'Deozas', 'D', 'Devinci', 'deozas@gmail.com', NULL, '$2y$10$6BiStphiQfrREIB/Ffd/8uiYJ/Ma8z9Q1L3Hf9NAnaWiiGxRGfOtm', '09874561230', 'PDF-6698c7abd52714.82034247.jpg', '', 'PDF-6698c7abd79897.62273409.jpg', 'deozas2024', 0, '1602', '160211', '160211007', 'CENRO Tubay'),
(53, 'EUNICE', 'MAMSOG', 'DESRE', 'eunice@gmail.com', NULL, '$2y$10$HFBBU9xJccLLyNQGe4LNTeibS0g1ntX98cHzTAUbdg/uksigWJDIG', '09478984921', 'PDF-66dfbfdcc888b7.90652736.png', '', 'PDF-66dfbfdccb8fe1.57549376.png', 'eunice2024', 1, '1668', '166811', '166811005', 'CENRO Lianga'),
(54, 'Arnold', 'Mabandos', 'Acebedo', 'acebedoarnold5@gmail.com', NULL, '$2y$10$HZMNs8fBhBGPpYA0HM4q6OmMIteg52XvDmW6OHQQh8IUCgx/ONUXS', '09624122821', 'PDF-66e009cbde7267.54434314.jpg', '', 'PDF-66e009cbe08bc5.69200090.jpg', '1968acebedo!', 1, '1668', '166816', '166816018', 'CENRO Lianga'),
(55, 'Aprel May', 'T', 'Piala', 'aprelmaypiala8@gmail.com', NULL, '$2y$10$UHb5QkaoAChkIp6cG8sd4uXVpxACjYS.D/IvEg2beGTQCAeFmQ.o6', '09624122821', 'PDF-66e23dee175a98.24873569.jpg', '', 'PDF-66e23dee1940b0.07969571.jpg', 'aprelmay9728Piala', 1, '1668', '166801', '166801002', 'CENRO Lianga'),
(56, 'Sample_Client', 'Of', 'CENROcantilan', 'cenroutil2022@gmail.com', NULL, '$2y$10$bk7JYTRDmw34b6aBGevvIeOOEI5ZasnjX9DMy1wsHf/BL1ZWD9Fha', '9105597356', 'PDF-66e254f147a177.36775308.jpg', '', 'PDF-66e254f14860a2.70660486.jpg', 'oldpms2024', 1, '1668', '166805', '166805011', 'CENRO Cantilan'),
(57, 'Roxanne', 'Daymiel', 'Varela', 'opuscorii16@gmail.com', NULL, '$2y$10$5eIn1cM4sLs2Xn60AbUiwehJ0vcy8hx4GypIJc31DHrrJde1i7RA6', '09507614169', 'PDF-66ebbc58a63225.72277267.jpg', '', 'PDF-66ebbc58a867f8.10555089.jpg', 'rhanielle', 1, '1668', '166815', '166815007', 'CENRO Lianga'),
(58, 'Marisol', 'Sample', 'Sample', 'marisol@gmail.com', NULL, '$2y$10$GwqDr.tDOUvxa6wLMX3oGeazjhcDpVKapHHqCD.8h4Q4EpAkPBhau', '09101761895', 'PDF-66f90df28b6106.17214029.jpg', '', 'PDF-66f90df28d79c6.72807241.jpg', 'feny1995', 1, '1603', '160311', '160311005', 'CENRO Talacogon'),
(59, ' Marlon', 'Basa', 'Plaza', 'marlonplaza892@gmail.com', NULL, '$2y$10$qPECclQYKAYTtikfhKZhZOjnnHlf5tIhzpNMPVlc2UWqKawPY6j7q', '09304772063', 'PDF-66fa3aef6fbbd3.88628282.jpg', '', 'PDF-66fa3aef71abe5.51943073.jpg', 'goldeneye', 1, '1603', '160311', '160311011', 'CENRO Talacogon'),
(60, 'NAPOLEON', 'ESTAL', 'CALLOTE', 'ronalkentabis@yahoo.com', NULL, '$2y$10$kBOkGaSqvye/HeCBHRik8OGJHuS7lqlx8ZayGtIWnxVkbqlDbpPSS', '09466073667', 'PDF-670cb6c7504e73.28503621.jpg', '', 'PDF-670cb6c7530023.12049812.jpg', 'ronalkentabis', 1, '1668', '166810', '166810001', 'CENRO Cantilan'),
(61, 'NORBERTO', 'BACHINICHA', 'BABIA, JR', 'bumblebee799342@gmail.com', NULL, '$2y$10$7pJWuRFT1jVkDl071DNtmuZKzgWDoXrr5P3XjcMkdEgRAQSX3Yt7y', '09630658357', 'PDF-670cd363016806.29051221.jpg', '', 'PDF-670cd363022cd4.61054570.pdf', 'bumblebee799342', 1, '1668', '166803', '166819008', 'CENRO Cantilan'),
(62, 'LINA', 'GERANDOY', 'INSINADA', 'linainsinada01@gmail.com', NULL, '$2y$10$WC1c9i/c5HmLXcr9MNgtWOc1hLUUVl8aVcYwKZO2Of/HHsPF5dp9u', '09658168802', 'PDF-672d8c6a74b8f2.29910875.jpg', '', 'PDF-672d8c6a76a0a8.14381973.jpg', 'lumberdealer', 1, '1602', '160203', '160203012', 'CENRO Tubay'),
(63, 'Herminio', 'Guma', 'Cabillo', 'cabilloherminio@gmail.com', NULL, '$2y$10$/hr6lb228ivKvcZmEFGGaOdg48rwBPye.Hi3VZy03zdljHvtHZGrO', '09484750988', 'PDF-6732e187771f86.29791016.jpg', '', 'PDF-6732e18778e886.80288951.jpg', 'dodong71', 1, '1603', '160305', '160305005', 'CENRO Loreto'),
(64, 'JUAN', 'A.', 'NAVARRO', 'juan22@gmail.com', NULL, '$2y$10$EEnAbhFNv3A33SCnXLvYduqSIUp6bSSy9wgNMLpzrE9I6FlJ4NhEW', '09353959443', 'PDF-674475f9aed2f8.45731925.png', '', 'PDF-674475f9b1f001.26002657.png', 'juan22_2024', 1, '1685', '168502', '168502002', 'PENRO Dinagat Islands'),
(65, 'Marvin', 'Jalalon', 'Gumimod', 'marvingumimod111@gmail.com', NULL, '$2y$10$BpAcY4YNT8ThdVUWPjW9ruWlvYIVzSfsSoYN6m6xcl33dXGeP5HHu', '09272557679', 'PDF-6746819dd919a4.44363532.jpg', '', 'PDF-6746819ddb3a04.60323905.jpg', 'akoaydinagat', 1, '1685', '168506', '168506011', 'PENRO Dinagat Islands'),
(66, 'Roque', 'Quisi', 'Culla', 'cullaroque@gmail.com', NULL, '$2y$10$RzurI4AMzeMJgM/d.N8fSuzPWppM1Vem6Zr1B2GGfkqzcLkCeE9lW', '09482396678', 'PDF-675fc926d94c47.06517477.jpg', '', 'PDF-675fc926dba318.76660025.jpg', 'cullaroque1961', 1, '1668', '166819', '', 'CENRO Bislig'),
(67, 'Marc Kaven', 'Dulguime', 'Castañares', 'maricki542@gmail.com', NULL, '$2y$10$n7D4aW8o0VetDfhh/qYXo.y1xQz4lpcvDLKDHIxGh2K3hIfhdriDO', '09536234005', 'PDF-677f45cf24e118.69680851.jpg', '', 'PDF-677f45cf549c88.76802377.jpg', 'Cherrym_@2024', 0, '1667', '166707', '166707013', 'PENRO Surigao del Norte'),
(68, 'Gladys Jisa', 'Ermita', 'Ruperez', 'rgladysjisa@gmail.com', NULL, '$2y$10$xNEsYra0b2YTtQ2F9IJYEeng8tex5VjIzo3Au9ZqmTa.05/UkdaF2', '09109876679', 'PDF-677f486fbc5172.57018869.jpg', '', 'PDF-677f486fbcd853.61114482.jpg', 'JISA120898', 1, '1667', '166725', '166725014', 'CENRO Tubod'),
(69, 'Marc Kaven', 'Dulguime', 'Castañares', 'maricki5432@gmail.com', NULL, '$2y$10$nWmqJNXdHfVz3m9jqioGruczYjQUZOPIBSox/svlBCrxisuFgJdG6', '09536234005', 'PDF-677f4870eab214.60139547.jpg', '', 'PDF-677f4870eb3369.05578637.jpg', 'Cherrym_@2024', 1, '1667', '166725', '166725014', 'CENRO Tubod'),
(70, 'Dante', 'Josol', 'Balo', 'dantebalo68@gmail.com', NULL, '$2y$10$70RbRnGWYwlozEcwjcpQI.2tXduHYNoWdT0UbmlMaxn.Jx0NbO5n2', '09079206167', 'PDF-6791a25a737443.05974687.jpg', '', '', 'dante@sumosumo2025', 1, '1668', '166818', '166818019', 'CENRO Lianga'),
(71, 'Julius', 'Perez', 'Benamban', 'mcvanzgo@yahoo.com', NULL, '$2y$10$qbHNP4JLGIC1VyKK/I5Wh.PAXvR6rnr7vgj2eoJvSTUVG3/HH16MS', '09996951727', 'PDF-67a44db7bb6c35.43382024.jpg', '', '', 'gumamela112274', 1, '1603', '160306', '160306031', 'CENRO Bayugan'),
(72, 'Flordeliz ', 'Entrena', 'Ranuja ', 'surigaofulda@gmail.com', NULL, '$2y$10$s3/fe6XXXeb95t.V13Cet.4Wc/wIua3jpwZu/NJpJLRF6zuIoACBC', '09774702782', 'PDF-67aee4cb112580.41239403.jpg', '', '', '12345678', 1, '1667', '166724', '', 'CENRO Tubod'),
(73, 'Ester', 'Talaroc', 'Pabillore', 'noeme0594@gmail.com', NULL, '$2y$10$uYI/2ep6pbDN4TF9Pfdt8uJj4pRi5YsqJONHK/gqIC.0YXF0qcJmC', '09098090194', 'PDF-67b322a6b741b9.91629960.jpg', '', '', '00702346', 1, '1602', '160212', '160212001', 'CENRO Tubay'),
(74, 'Aiko', 'Doloricon', 'Beloy', 'Aikobeloy09@gmail.com', NULL, '$2y$10$5O4eZlfSv7ptieFnTJZLqOgIoz0vVibuJrdx8cXNjalJWNaNghv2e', '09505447901', 'PDF-67bd5ef9032b92.02966105.jpg', '', '', 'aiko04091993', 1, '1668', '166818', '166818011', 'CENRO Lianga'),
(75, 'joan ', 's', 'bigcas', 'dhiemhie031219@gmail.com', NULL, '$2y$10$wMZ9r9Neba3c0ZmscLDI2.jaIJ34J.7yr.rTdE3.vXURajWEFSZ.2', '09502731433', 'PDF-67bea2863c4ff0.96994860.jpg', '', '', 'mhienddhie031219', 1, '1602', '160208', '160208001', 'CENRO Tubay'),
(76, 'NORMAN', 'A', 'BASAN', 'norman@gmail.com', NULL, '$2y$10$RTz00/rOPn0NiwnlEgydO.TdhvIv/9GlFBVRsniRqDEoJWW2u8ewe', '09236841689', 'PDF-67c840730dc200.95254497.png', '', '', '12345', 1, '1603', '160301', '160301019', 'CENRO Bayugan'),
(77, 'awdawawd', 'awdawd', 'awdawd', 'lleva.er@shc.edu.ph', NULL, '$2y$10$Khg7Yo5/kgcTP7WUfM/Smei.thdVU.Bk7lxAAz5dXKuvKxPczFTEW', '09540545454', 'PDF-67dfcba4361ae7.37144058.jpg', '', 'AUTH-67dfcba436a725.12594670.jpg', 'e51OzxTV', 1, '1603', '160308', '', 'CENRO Bunawan'),
(78, 'Vincent ', 'M', 'Co', 'vincentco123@gmail.com', NULL, '$2y$10$tG2sg0Y09JqUaTBk21ZVmumfuvqI36LFRcBY4vsKCBC.z5eEM7UR6', '09866855', 'PDF-67e0429fcd1ef5.30335534.png', '', 'AUTH-67e0429fcdb945.16231404.jpeg', '123456123', 0, '1685', '168505', '168505011', 'PENRO Dinagat Islands'),
(79, 'HARETO', 'DINGDING', 'DIGOLTO', 'haretodigolto@gmail.com', NULL, '$2y$10$z7bVhUriXNVgrAepaRWnbugWIAKinZDICmdRVesdioC2.4hMDMsjy', '09939394779', 'PDF-67e4fd583e29f1.19433695.jpg', '', '', 'lumberdealer', 1, '1602', '160205', '160205002', 'CENRO Tubay'),
(80, 'Joel ', 'Suan', 'Carcasona', 'jocars101978@gmail.com', NULL, '$2y$10$oreUW3/Uunch61OQ5yArSezTbG17aT00i5B9AhQZGi7FVIAqxRxjK', '09105704075', 'PDF-67eccc1576cb97.15721339.jpeg', '', '', 'joel101978.', 1, '1602', '160202', '', 'CENRO Nasipit'),
(81, 'Sherwin', 'Ongcoy', 'Bacalso', 'ewinbacs@gmail.com', NULL, '$2y$10$X4Ww0XJYJWNN3PoILyN/2eYIdwlhcJ68uJFwHrQMvTMBO.QpYKmVa', '09153381163', 'PDF-67f4d742eea937.46742717.jpg', '', '', 'ewinbacs', 1, '1602', '160202', '160202066', 'CENRO Nasipit'),
(82, 'Mark Joseph', 'Ruelan', 'Lagario', 'mgcuizon@ngcp.net.ph', NULL, '$2y$10$9MNLQsCK6fcLydbdg.tHOuCOzL9oKrd398nX/Ovk2ykH0.nZ.9m4y', '09359252208', 'PDF-67f7576fe9ca26.48169447.jpg', '', '', 'MatetC@NGCP', 1, '1668', '166811', '166811010', 'CENRO Lianga'),
(83, 'jiselle', 'cortejos', 'tiña', 'jiselletina320@gmail.com', NULL, '$2y$10$OU6BHquOaRHOr1i/gyQglu7b3B4y5qf9RNduAsMXQifzuPHD7wXaW', '09764610879', 'PDF-67f89649dae121.37291065.jpg', '', '', 'Mypassword123?', 1, '1603', '160308', '160308027', 'CENRO Bunawan'),
(84, 'JERRY', 'HORCA', 'APOSTOL', 'jhapostol.ja@gmail.com', NULL, '$2y$10$qN1Y7CXowzBSdDoNq9gX/O3cCMxkiWtG00qhel1kz1x866j3Mi48K', '09178723322', 'PDF-67fc575352f2d4.65530090.jpg', '', '', 'n@kaliMotKo#2025', 1, '1602', '160202', '160202043', 'CENRO Nasipit'),
(85, 'Jiselle', 'cortejos', 'Tiña', 'jiselletina00@gmail.com', NULL, '$2y$10$4tuaLmtOoHrw74uxxSV4ieSg4xIp0eDVnp2eDLWbzhN6AFyAGaHBq', '09764610879', 'PDF-67fdb7ba14d4e3.73915590.jpg', '', '', 'Mypassword123?', 1, '1603', '160308', '160308027', 'CENRO Bunawan'),
(86, 'Ropston Paul', 'Navarro', 'Pahit', 'ropstonpahit1993@gmail.com', NULL, '$2y$10$0.2TdH4ISIH3HYyUBAhI3ulpf7NQoa9rZJYqA5R1GN0a1KQo.Xgfa', '09553493509', 'PDF-67fdc7785d6182.86895888.jpeg', '', '', 'Pahit1993', 1, '1667', '166710', '166710015', 'SIPLAS'),
(88, 'Jezreel', 'Botona', 'Dumanjog', 'jezreeldumanjog@gmail.com', NULL, '$2y$10$u5qmx0LazgwPmxXTLC.f8.uv.qHvxS8CTr6IkBnjJyeeh7EJEJxpy', '09126366632', 'PDF-67fe26bd256b01.72633883.jpg', '', '', 'nehemiahrise', 1, '1667', '166707', '166707019', 'SIPLAS'),
(89, 'RONALDO', 'LASPIÑAS', 'CORVERA', 'rcorvera96@gmail.com', NULL, '$2y$10$XpAIL3wNVc89qbXcy8rb0.GemophXZB.hAxyEtMP/dgkYjYl.nv2q', '09455936042', 'PDF-680afe0d711125.76027677.pdf', '', '', 'rycwoodproductsmanufacturing', 1, '1603', '160309', '160309005', 'CENRO Talacogon'),
(90, 'RODERICK', 'BUAR', 'GUMBAN', 'rglumber1986@gmail.com', NULL, '$2y$10$zf6Ib8088tE9DVWHW2a8Tev/AQMYGHR0Sh8sOAQ3tn2ZmvIz1xZ.C', '09683645316', 'PDF-680ef6176e4389.55918695.pdf', '', '', 'otinabmug1986', 1, '1668', '166819', '166803009', 'CENRO Bislig'),
(91, 'Rolibe', 'Diegas', 'Morales', 'moralesrolibe@gmail.com', NULL, '$2y$10$qU6dGTzK/cM6JokDScoK0OrYpQFX3dHuLLEhNXcWafhpjxGQemOiC', '09485253698', 'PDF-680eff1a774d23.21793384.pdf', '', '', 'Morales1968', 1, '1668', '166819', '166803009', 'CENRO Bislig'),
(92, 'Rolibe', 'Diegas', 'Morales', 'rolibemorales@gmail.com', NULL, '$2y$10$RxColZh9vHaaMEqb/EzFNePvr9jnzdItDwYHslRCbREDbdceD6hmy', '09485253698', 'PDF-680f4213eeae31.61462188.pdf', '', '', 'morales1968', 1, '1668', '166819', '166803009', 'CENRO Bislig'),
(93, 'ricardo', 'hernan', 'nebres', 'dongnebres.gm@il.com', NULL, '$2y$10$/iK83P5vzreJQ04FOKuLrOx/Nd0L3yi5h7tG8AA.5jBBKwuxyOzWu', '09309885267', '', '', '', '19940408', 0, '1685', '168506', '', 'PENRO Dinagat Islands'),
(94, 'Michael', 'Rosales', 'Areñavo', 'rosaleskemi24@gmail.com', NULL, '$2y$10$X2WSsG1T5bL.F0SqKAKgheaXaXwiUaMaw0MRVQnsZwf4AzMxwGm6G', '09858221604', 'PDF-681b04caab7e88.65048103.jpg', '', '', '1986', 1, '1667', '166710', '166710015', 'SIPLAS'),
(95, 'Norberto, Jr.', 'Bachinicha', 'Babia', 'babiajrnorberto@gmail.com', NULL, '$2y$10$3YWropGsyXQ92wBiR85/c.oedSCOVEhBWIX8CRdobCge95NzEAvV.', '09630658357', 'PDF-681c39d4abd451.98269449.jpg', '', '', 'norberto@bajaotago2025', 1, '1668', '166818', '166818005', 'CENRO Lianga'),
(96, 'Nemson', 'Dalugdugan', 'Binongo', 'rosaleskemi@gmail.com', NULL, '$2y$10$qrBKRwQGdFzdYmBVjMMHJOjq/KgvleevQF5EEWjRBqqZspNwRmwcu', '09858221604', 'PDF-6822bef122de25.82732305.jpg', '', '', '198624', 1, '1667', '166710', '166710015', 'SIPLAS'),
(97, 'Maricel', 'Balili', 'Polinar', 'polinarm957@gmail.com', NULL, '$2y$10$uU8JXVrNYoFbJc6GNWgz1OC5ZcjfShTVsBz9AcJlQMPPHT1MDZZs2', '09311097471', 'PDF-6826f67fcd5ef5.64613765.jpg', '', '', 'Michael10018487', 1, '1668', '166811', '166811008', 'CENRO Lianga'),
(98, 'Ronie', 'Rebelala', 'Navejas', 'girlynavejas@gmail.com', NULL, '$2y$10$FvKxkGRJtioSi/oRWreYkePemYQ0piZ9kavlQ4uUWBu7ADOBURV5G', '09705366259', 'PDF-68350c171c9c96.22960566.jpg', '', '', 'ronienavejas', 1, '1667', '166724', '160672036', 'CENRO Tubod'),
(99, 'AILVIN', 'URBIZTONDO', 'ORTEGA', 'ailvinortega29@gmail.com', NULL, '$2y$10$hqG2fgbXSXhSD94eVJjj4ecWDRkHlU.Z6CkzVhU1Lo1w.0zuydqXK', '09484926505', 'PDF-6835220b839fa9.81548534.pdf', '', '', '123456789', 1, '1668', '166813', '166813006', 'CENRO Cantilan'),
(100, 'Rolen', 'Echavez', 'Binatero', 'rolenbinatero@gmail.com', NULL, '$2y$10$Sa6decdSOLPEXl87kqLmOOf7izXT2tLXmYG8Wv9UE2EkAYLHpJXA.', '09074801262', 'PDF-6835575b28b998.73443753.jpg', '', '', '1984', 1, '1667', '166710', '166710011', 'SIPLAS'),
(101, 'Ma. Conifie', 'Pajuelas', 'Caspe', 'maconiefecaspe8@gmail.com', NULL, '$2y$10$Bln3dnLCyoH5hXCcxg1niu6udPS7WeyKKsa2LJMQrgVLUa3P7Kz8y', '09126895768', 'PDF-683928127a42b2.20610640.pdf', '', '', 'coniefe33', 1, '1685', '168506', '168506007', 'PENRO Dinagat Islands'),
(102, 'Edgardo', 'Piao', 'Lampad', 'shainatoscano080@gmail.com', NULL, '$2y$10$vVRJR9MgkzDABeh32isU/u7wFNRsjeB5moXVtn8wxo88LjFgswZTC', '09308739297', 'PDF-683d14679cecd7.35709566.jpg', '', '', 'shainatoscano', 1, '1667', '166723', '166723007', 'SIPLAS'),
(103, 'Marino', 'Bioy', 'Gubaton', 'marinogubaton63@gmail.com', NULL, '$2y$10$yicCdkBhfZAj.Sbc3OwaMezdJt/wa2cB/GEdmcCJWrssNdsZ9YZ1u', '09815196471', 'PDF-685a0eda86db28.19431209.jpg', '', '', 'malinlumber', 1, '1667', '166710', '166710015', 'SIPLAS'),
(104, 'Venancio', 'Libre', 'Baynosa', 'clintonbaynosa8@gmail.com', NULL, '$2y$10$PhJZ/ishCIyPM9xYByrQteSd4j2JXnXgFRxaIl33qq4HVi8s5f81G', '09633191088', 'PDF-6870953095ffc2.12572639.jpg', '', '', 'baynosa@09', 1, '1602', '160206', '160206001', 'CENRO Tubay'),
(105, 'Arturo', 'Benolerao', 'Lloren', 'arturolloren352@gmail.com', NULL, '$2y$10$AyoYAImuWYD5pLVSblO2Bu3ZOk3Q.fcW8t4MrQzs0VTmb1kKLQKF6', '09388715218', 'PDF-68785e0ce0cd14.49758809.pdf', '', '', 'march031962', 1, '1668', '166804', '166804012', 'CENRO Lianga'),
(106, 'Roel', 'Bohol', 'Bisaya', 'roelbisaya2025@gmail.com', NULL, '$2y$10$/FcrxWBHxAxou7lYPqalJ.Qcx8jFZavJkVx.8EvBeU/1SemVRZnoy', '09633278834', 'PDF-688990e6337ef0.84627124.jpg', '', '', 'roelbisaya_@2025', 1, '1667', '166723', '166723007', 'SIPLAS'),
(107, 'Jenepher', 'Saagundo', 'Monoy', 'azrainmonoy@gmail.com', NULL, '$2y$10$9jneUIQ3MDYD93kfnLHUYOgVq13y85CW0SCiyP7pCxr5uGb3kl.IS', '09302501768', 'PDF-6891ad2def6ea2.52599176.jpg', '', '', 'lumberdealer', 1, '1602', '160205', '160205011', 'CENRO Tubay'),
(108, 'Juanita', 'Labrador', 'Bacus', 'bacusjuanita935@gmail.com', NULL, '$2y$10$Gxr/iwZauxl0ZJto.qgq0uR1SPh5J23uGZas1LiVEEj/ICmVO9n2e', '09387054453', 'PDF-689c2b7088bb31.37640659.pdf', '', '', 'january311970', 1, '1668', '166811', '166811011', 'CENRO Lianga'),
(109, 'Jornido', 'Canillas', 'Singson', 'jornidosingson@gmail.com', NULL, '$2y$10$O1Xx1dHSDCk633c88RbyVuv0kx1hoIo7LvdRRyR15bjbgWOskStIG', '09103391810', 'PDF-68a80d8632f713.50937135.jpg', '', 'AUTH-68a80d8633e5f4.30502899.pdf', 'singson77!', 1, '1603', '160301', '160301029', 'CENRO Bayugan'),
(110, 'Alfredo, Jr', 'Agravante', 'Obligado', 'obligadoalfredo21@gmail.com', NULL, '$2y$10$ftU5Uz.T3VpgX/x/vnxv6eT20r8ujmTKuvsdB3z7aORsfhxV8cSDS', '09653356112', 'PDF-68afcbaf3e1ce2.17367323.jpg', '', '', 'obligadoalfredo_21', 1, '1603', '160301', '160301011', 'CENRO Bayugan'),
(111, 'Yolando', 'Razonado', 'Estillore', 'estilloreyolando6@gmail.com', NULL, '$2y$10$Ie/EN5Z9agwD6KbSDwzgGeEWFhycPVbN4kYGgrDynwPNotUPy.cF2', '09633431352', 'PDF-68b15372473b43.06421553.jpg', '', '', 'Promulco_2025', 1, '1603', '160306', '160306004', 'CENRO Bayugan'),
(112, 'MENCHIE', 'CLAVERIA', 'CABARUAN', 'nguyenlyly@tutamail.com', NULL, '$2y$10$3gjEHJqE2s/H32yYXwzFYu0JFjLfPrH7FEWgkMhdVDObjCUVckYu.', '09859261933', 'PDF-68b510a53b9545.92207744.jpg', '', '', 'AaQazWsx09217@@', 1, '1602', '160201', '160201001', 'CENRO Nasipit'),
(114, 'Arnel', 'Labastida', 'Alute', 'arnelalute42@gmail.com', NULL, '$2y$10$R.DcmXq10/eSQcnmcNY.euATEto/eMyoK0.cvUFlrYlwLqNkwSyoi', '09856261191', 'PDF-68b952c08e2379.40115032.jpg', '', '', 'alutearnel_42', 1, '1603', '160306', '160306014', 'CENRO Bayugan'),
(115, 'William Ryan ', 'Tambis', 'Noquil', 'nwilmarryan@gmail.com', NULL, '$2y$10$C0xhpnP7InFqFfzzu3hRZ.rxTKOTHeStJUqlBWAzNgXbapjaOqBgq', '09386466827', 'PDF-68ba5e8f37c128.65078013.pdf', '', '', 'Nram1996', 1, '1668', '166819', '166803009', 'CENRO Bislig'),
(116, 'SEGUNDINO JR', 'CHAVEZ', 'DAGAANG', 'segundinodagaang1975@gmail.com', NULL, '$2y$10$Iuf14IM/SjpZdKKNLOeiHeGnvZc1iK2nK8h4qqKs762OYIqe2q5FS', '09852304531', 'PDF-68d2190fa24d12.20328643.pdf', '', '', 'Password123', 1, '1603', '160308', '160308015', 'CENRO Bunawan'),
(117, 'Johnny', 'Roxan', 'Cruz', 'wizardsec23@gmail.com', NULL, '$2y$10$SGMZleQnHVP7yVlw.vhd2OOF3E4gMMmMr/tm6MOWBbA47DQobAAEe', '09281866071', 'PDF-68d3a75a285508.91326847.jpg', '', '', '@@Kokoloko01', 1, '1602', '160204', '160204010', 'CENRO Nasipit'),
(118, 'EMMANUEL', 'SENO', 'BAGAIPO', 'prancejenom@gmail.com', NULL, '$2y$10$d6soserze.RleploBqTzYuOezrsUz2RnOjOQsm9fG6Owh/f1IX4ou', '09815191313', 'PDF-68db42ce2889f9.20758554.jpg', '', '', 'bagaipo2025', 1, '1602', '160202', '160202012', 'CENRO Nasipit'),
(119, 'Teofredo', 'Cabasan', 'Porpayas, JR.', 'marlindasaligo48@gmail.com', NULL, '$2y$10$IKHXLJarEGuk1JsMx37FBuIQzEISpEIAHgwKIcN1P8xoiCUveSi.u', '09666496956', 'PDF-68db748a9d0686.89094964.jpeg', '', '', 'marlindasaligo48', 1, '1667', '166707', '166707013', 'SIPLAS'),
(120, 'EMMANUEL', 'SENO', 'BAGAIPO', 'akirahmordeno@gmail.com', NULL, '$2y$10$O6NgIrEGOPktli18P5KITO78IIWiFi.T0n2fwSRGIvevFhQhHE7lK', '09815191313', 'PDF-68db8230a51173.36356550.jpg', '', '', 'bagaipo2024', 1, '1602', '160202', '160202012', 'CENRO Nasipit'),
(121, 'Rosalinda', 'Solano', 'Catana', 'Racatana03@gmail.com', NULL, '$2y$10$GYcL8bcDZu.ZxSXjvBqTl.4aMvZh/9mjlFRN.9DwGe0xKIH/5eB9W', '09193015946', 'PDF-68ec840ee8b4d0.55743117.pdf', '', '', 'mamaaries', 1, '1685', '168506', '168506003', 'PENRO Dinagat Islands'),
(122, 'Marializa', 'Tampos', 'Custodio', 'custodiomarializa370@gmail.com', NULL, '$2y$10$ATxU2vT7IncCeliKD9F.pegNXLSATTXu3hKFmyBHArW8sMpm8aUNW', '09705366259', 'PDF-68ef429e03f154.17775015.jpg', '', '', 'M123456789!', 1, '1668', '166810', '166810004', 'CENRO Cantilan'),
(123, 'norie', 'tancio', 'onggo', 'norieonggo13@gmail.com', NULL, '$2y$10$1hJRjdjkZ.3ipVichAU11.dN/XAMa1tBJy8EpUxWFbXi9xakDSVES', '09515131513', 'PDF-68f5cce3974098.62748407.pdf', '', '', 'Password123?', 1, '1603', '160308', '160308015', 'CENRO Bunawan'),
(124, 'MARCOS ANTONIO KJ', 'MORALDE', 'VISTAL', 'marcoslumber68@gmail.com', NULL, '$2y$10$i4mUkMX9q8S/6ldnaBSnmeVqO.EM5kXhenZrq.MTzEWlPZYYrhK1W', '09276733428', 'PDF-68f5cd85d757a0.84146180.pdf', '', '', 'Password123?', 1, '1603', '160308', '160308015', 'CENRO Bunawan'),
(125, 'Georlan', 'Peru', 'Arnaiz', 'orlanarnaiz@gmail.com', NULL, '$2y$10$ehpcjFBUNbBKCt/aJSgv5.3TPvLfrfM6z0MjFj/qBvfJRH/sJSTgi', '09171576599', 'PDF-68f768b72357b3.01268074.jpg', '', '', 'Orl17arnaiz*', 1, '1602', '160202', '160202054', 'CENRO Nasipit'),
(126, 'Rudelito', 'Tamayo', 'Elandag', 'rudebems@gmail.com', NULL, '$2y$10$oNGoadtI8i40SW/NaxAmG.6878Jh6XQfpjNdbCzpKFpcfiUXlWIy.', '09260619244', 'PDF-68f8a92f4cd475.52753324.jpg', '', '', 'rudebems', 1, '1667', '166710', '166710004', 'SIPLAS'),
(127, 'NECY ', 'ESPAÑA', 'DAVID', 'santribez25@gmail.com', NULL, '$2y$10$dW9XDt6/zAmv7osA6SA/tehdY5.fZocUjTzrYfZJR1gjR9RfD73fi', '09630272610', 'PDF-6901a96f5a0762.29205805.jpg', '', '', 'necydavid2025', 1, '1603', '160305', '160305005', 'CENRO Loreto'),
(128, 'Ceferino', 'Mozo', 'Jumandos', 'ceferinojumandos711@gmail.com', NULL, '$2y$10$aRHqs8DSzNKM9FPjCc9gdeb.O5YWp5dJ1dihxx64WnKIJywKRbxJm', '09309407046', 'PDF-690308d98cd961.28374462.pdf', '', '', 'August261958', 1, '1668', '166811', '166811008', 'CENRO Lianga'),
(129, 'Thelma', 'Murcilla', 'Rosales', 'rosalesthelma17@gmail.com', NULL, '$2y$10$BGaF4KkTHLM798H.fwoRUuKOubHe5rRvshngW0NsHCt3/Kd6gOvci', '09461950892', 'PDF-6903145ce3dc87.27476142.pdf', '', '', 'Feb11972', 1, '1668', '166801', '166801015', 'CENRO Lianga'),
(130, 'MA. IVY', 'LICOS', 'CASTROMAYOR', 'maivycastromayor@gmail.com', NULL, '$2y$10$P4Xb3ts3slIod4ngOYFyy.aSvUu.Di1bqHVor7Yogeg0gt42WyigK', '09485950672', 'PDF-690af1a3ae40c6.20549141.pdf', '', '', 'maivycastromayor1004', 1, '1603', '160311', '160311017', 'CENRO Talacogon'),
(131, 'ARCELITA', 'BADAO', 'DINAMPO', 'arsiedinampo@gmail.com', NULL, '$2y$10$D2WkdTqcRXYT6sRxXUdig.D05G6A9c9rM6kEjo0RoNlwD5F3ydrue', '09700658162', 'PDF-690c2f8f4e9491.66223402.jpg', '', '', 'arsiedinampo2025', 1, '1603', '160305', '160305009', 'CENRO Loreto'),
(132, 'GREG', 'VILLANUEVA', 'BAYOTAS', 'gregbayutasaugust@gmail.com', NULL, '$2y$10$xYcoeiOnWOCr.CNQQn3dEeXdZgeSe9zJgmdyWT.6iQBK5NEcBwCKW', '09216342201', 'PDF-690c3defe1fed6.18637924.pdf', '', '', 'gregBayotas2022', 1, '1603', '160304', '160304005', 'CENRO Talacogon'),
(133, 'CLENTHANY', 'CAGULADA', 'ARGUELLES', 'clenthanya@gmail.com', NULL, '$2y$10$wHtqPjdr6sjcbjLgMp0Z5ejXQAKKwo0qM2CUsE1gm2NL2QkKKigTi', '09639433704', 'PDF-6912a139b0d1c6.52160133.jpg', '', '', 'CLENTHANYA', 1, '1603', '160305', '160305006', 'CENRO Loreto'),
(134, 'Alfredo', 'Agravante', 'Obligado', 'armezasanel563@gmail.com', NULL, '$2y$10$osSShfkJffb6MYy2X6wFJeQ00ZKdm1Uo97rOvoIOdNjEMpGd0Crfi', '09653356112', 'PDF-6912cc54971c23.48979813.pdf', '', '', 'Password123?', 1, '1603', '160312', '160312002', 'CENRO Bunawan'),
(135, 'Jovel', 'Y.', 'DAVIS', 'clarosjovel758@gmail.com', NULL, '$2y$10$OEMmes6ZI3k0lAkaim0YxO5rmdtKL5URClqgw02cYfjl8wisNfj.S', '09560841326', 'PDF-69158dc0cff554.85227774.pdf', '', '', 'Levoj1992', 0, '1668', '166819', '166803009', 'CENRO Bislig'),
(136, 'Jovel', 'Yosures', 'Claros', 'jovelclaros68@gmail.com', NULL, '$2y$10$cvZJqiCIc.IcMKSHaLIiJuHaYMidCzfahioFN4B29ssE2W0OYtgnS', '09197182717', 'PDF-691692d8a4ec83.59069616.pdf', '', '', 'Levoj1992', 1, '1668', '166819', '', 'CENRO Bislig'),
(137, 'Mylene', 'Colima', 'Bigcas', 'lancelaurence03@gmail.com', NULL, '$2y$10$VPgse3F1qn7Vg6WhWQETZuoObVOy4xLNhT8s4J/BOyAkfLPa/wKmC', '09307405029', 'PDF-6918561ead8ad2.84454163.jpg', '', '', 'mjlancelaurencecolimabigcas', 1, '1602', '160211', '160211003', 'CENRO Tubay'),
(138, 'Leo', 'Gilaga', 'Osugay', 'osugayleo71@gmail.com', NULL, '$2y$10$nYlW1/FPNrHrQuRa7BF8Wu0T9xVFEx8ABpEmdkmBkwJx/SYaaFZl.', '09167463477', 'PDF-691ae001bee695.33958705.pdf', '', '', 'Oleyag1130', 1, '1668', '166819', '166803012', 'CENRO Bislig'),
(139, 'narly', 'morta', 'perang', 'perangnarly@gmail.com', NULL, '$2y$10$zfr9s4V2Spcb8DZfUGwqru0g939/spS8YwCzfUW7SEzh0bhUpff5i', '09565684761', 'PDF-6937cbea6ca190.31535075.jpg', '', '', 'noelnarlynhoelanoela56', 1, '1602', '160203', '160203019', 'CENRO Tubay'),
(140, 'MARK JHON', 'ROSALES', 'YTAC', 'fimcofimco@gmail.com', NULL, '$2y$10$YD8ricQeI2CE97hJnKUz3OiENY7gjiIpTFAxP0rLIUNJJNhXUvca.', '09919079929', 'PDF-693b7a092a6a82.67597078.pdf', '', 'AUTH-693b7a092b6416.37079978.pdf', 'Bnw_1232', 1, '1603', '160302', '160302001', 'CENRO Bunawan'),
(141, 'narly', 'morta', 'perang', 'narlymorta25@gmail.com', NULL, '$2y$10$jSvFJXt2gVsqs56c/rbQWuQ1EaWrLvPrt4s/i4bEWdLWxC5yu7Vvm', '09565684761', 'PDF-693bb6c6d86740.62540923.jpg', '', '', '565656narlynoel', 1, '1602', '160203', '160203019', 'CENRO Tubay'),
(142, 'Maria Christina Abigail', 'T', 'VESO', 'vesomachristina@gmail.com', NULL, '$2y$10$.lVPJtzbqyGRXOKshZ50l.bZq5o1oPGt5qCB6eG.cut0m8.EoCePi', '09506812562', 'PDF-695e1af1a15598.20221169.pdf', '', '', 'Password123?', 1, '1603', '160308', '160308015', 'CENRO Bunawan'),
(143, 'Raul', 'Truba', 'Cubil', 'ARClumberdealer@gmail.com', NULL, '$2y$10$dtmvi3KZkXM6lLbNjyLKB.8Lv4YXnfyzyiaRuIBqJ7r5..jWhUsM.', '09088744207', 'PDF-696d8500c1d451.14287776.pdf', '', '', 'Cubil#1973', 1, '1668', '166814', '166814007', 'CENRO Lianga'),
(144, 'Norberto Jr.', 'Bachinicha', 'Babia', 'junbabs799342@gmail.com', NULL, '$2y$10$giuG.yryOnn43jKEQHqw4uQ1y5tXBV95AroC1CYgOF9dzKX8QtXqC', '09630658357', 'PDF-69722042116777.48327861.jpg', '', '', '799342junbabs', 1, '1668', '166803', '166819018', 'CENRO Cantilan'),
(145, 'Geodel June', 'Batulan', 'Salado', 'junesalado@gmail.com', NULL, '$2y$10$ghAHXaGC65tXissCofMl4.EG3bYQV3jZf/o5ZK5M8ORDcfRE9NfSa', '09075109748', 'PDF-697817f4badb64.26634248.jpg', '', '', '123456789G', 1, '1668', '166807', '166807001', 'CENRO Cantilan'),
(149, 'ANTHONIE FENY', 'V.', 'CATALAN', 'natej39709@flemist.com', '793e41ff215e437b9917c1197aaeb18346b2fa31c926c38bcbd3ec7f0827347e', '$2y$10$CD0DTGXs/tBcmeTUV5RXr.HB6NkaMVbAhTcgd5rcSkfhC3v.CnQg6', '09478984921', 'uploads/1770785902_manage_requirements.php', 'uploads/1770785902_db.php', '', 'Feny9959..', 0, 'REGIONAL OFFICE', 'Tubay', 'Purok 1, Dona Rosario', '8606');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user_client`
--
ALTER TABLE `user_client`
  ADD PRIMARY KEY (`client_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user_client`
--
ALTER TABLE `user_client`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
