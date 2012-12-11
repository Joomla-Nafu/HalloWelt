DROP TABLE IF EXISTS `#__hallowelt`;

CREATE TABLE `#__hallowelt` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `hallo` VARCHAR(25) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#__hallowelt` (`hallo`) VALUES
('Hallo Welt !'),
('Tschüss Welt !');
