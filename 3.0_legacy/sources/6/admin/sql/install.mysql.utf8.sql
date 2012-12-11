DROP TABLE IF EXISTS `#__hallowelt`;
 
CREATE TABLE `#__hallowelt` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`greeting` VARCHAR(25) NOT NULL,
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
 
INSERT INTO `#__hallowelt` (`greeting`) VALUES
	('Hallo Welt !'),
	('Tschüss Welt !');
