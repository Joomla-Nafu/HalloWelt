DROP TABLE IF EXISTS `#__hallowelt`;

CREATE TABLE `#__hallowelt` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `hallo` VARCHAR(25) NOT NULL,
  `catid` INT(11) NOT NULL DEFAULT '0',
  `params` VARCHAR(1024) NOT NULL DEFAULT '',
   PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#__hallowelt` (`hallo`) VALUES
    ('Hello World!'),
    ('Good bye World!');
