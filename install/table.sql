
CREATE TABLE IF NOT EXISTS `ms_admin` (
  `admin_id` int(10) NOT NULL AUTO_INCREMENT,
  `admin_username` varchar(101) COLLATE utf8_bin NOT NULL,
  `admin_password` varchar(101) COLLATE utf8_bin NOT NULL,
  `admin_name` varchar(101) COLLATE utf8_bin NOT NULL,
  `admin_access` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT 'a',
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `admin_username` (`admin_username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `ms_menu` (
  `menu_id` int(10) NOT NULL AUTO_INCREMENT,
  `menu_nama` text COLLATE utf8_bin NOT NULL,
  `menu_harga` int(12) NOT NULL,
  `menu_status` int(1) DEFAULT '1' COMMENT '1 = active, 0 inactive',
  `menu_tag` text COLLATE utf8_bin,
  PRIMARY KEY (`menu_id`),
  KEY `menu_status` (`menu_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `ms_pengumuman` (
  `peng_id` int(10) NOT NULL AUTO_INCREMENT,
  `peng_admin_id` int(10) NOT NULL,
  `peng_status` int(1) NOT NULL,
  `peng_text` text COLLATE utf8_bin,
  `peng_lastedit` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`peng_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `ms_pesanan` (
  `pesan_id` int(10) NOT NULL AUTO_INCREMENT,
  `pesan_tanggal` date NOT NULL,
  `pesan_waktu` time NOT NULL,
  `pesan_user` varchar(201) COLLATE utf8_bin NOT NULL,
  `pesan_user_email` varchar(201) COLLATE utf8_bin NOT NULL,
  `pesan_user_photo` text COLLATE utf8_bin,
  `pesan_gedung` int(1) NOT NULL DEFAULT '1',
  `pesan_uang` int(10) NOT NULL DEFAULT '0',
  `pesan_uang_terpakai` int(10) DEFAULT '0',
  `pesan_text` text COLLATE utf8_bin NOT NULL,
  `pesan_status` int(1) NOT NULL DEFAULT '0',
  `pesan_uang_donasi` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pesan_id`),
  UNIQUE KEY `pesan_tanggal_pesan_user_email` (`pesan_tanggal`,`pesan_user_email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='pesanan masuk sini';

INSERT INTO `wb_msg`.`ms_admin` (`admin_username`, `admin_password`, `admin_name`, `admin_access`) VALUES ('admin', 'ee4e93dde964bbd61c6c1768a4a1f39d', 'admin', 'abcd');