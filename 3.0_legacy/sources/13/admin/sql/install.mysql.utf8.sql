DROP TABLE IF EXISTS `#__hallowelt`;

CREATE TABLE `#__hallowelt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hallo` varchar(25) NOT NULL,
  `catid` int(11) NOT NULL DEFAULT '0',
  `params` VARCHAR(1024) NOT NULL DEFAULT '',
   PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#__hallowelt` (`hallo`) VALUES
    ('Hello World!'),
    ('Good bye World!');
