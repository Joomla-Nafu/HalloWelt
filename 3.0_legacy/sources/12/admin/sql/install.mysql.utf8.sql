DROP TABLE IF EXISTS `#__hallowelt`;

CREATE TABLE `#__hallowelt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hallo` varchar(25) NOT NULL,
  `catid` int(11) NOT NULL DEFAULT '0',
   PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

INSERT INTO `#__hallowelt` (`hallo`) VALUES
  ('Hello World!'),
  ('Good bye World!');
