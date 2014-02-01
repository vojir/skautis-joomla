CREATE TABLE IF NOT EXISTS `#__skautis_config` (
  `name` varchar(50) NOT NULL,
  `value` varchar(50) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tabulka obsahující nastavení pro komponentu com_skautis';


INSERT INTO `#__skautis_config` (`name`, `value`) VALUES
('SKAUTIS_APP_ID', 'xxxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx'),
('SKAUTIS_URL', 'https://is.skaut.cz');


CREATE TABLE IF NOT EXISTS `#__skautis_users` (
  `id` int(11) NOT NULL,
  `id_skautis_user` int(11) NOT NULL,
  `id_person` int(11) NOT NULL,
  `photo_extension` varchar(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tabulka obsahující informace o uživatelích ze skautisu';
