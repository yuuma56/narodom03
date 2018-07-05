-- Adminer 4.6.2 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `member`;
CREATE TABLE `member` (
  `id_m` int(2) NOT NULL AUTO_INCREMENT COMMENT 'ลำดับ',
  `lname` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT 'ชื่อ',
  `fname` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT 'นามสกุล',
  `email` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT 'อีเมลล์',
  `tel` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT 'เบอร์โทร',
  `address` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'ที่อยู่',
  `uname` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT 'ชื่อผู้ใช้',
  `password` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT 'รหัสผ่าน',
  `typeid` int(3) NOT NULL COMMENT 'รหัสประเภท',
  PRIMARY KEY (`id_m`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='ตารางสมาชิก';


-- 2018-07-05 04:16:28
