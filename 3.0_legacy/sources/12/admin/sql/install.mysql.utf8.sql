DROP TABLE IF EXISTS `#__hallowelt`;

CREATE TABLE `#__hallowelt` (
  `id`    INT(11)     NOT NULL AUTO_INCREMENT,
  `hallo` VARCHAR(25) NOT NULL,
  `catid` INT(11)     NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 0
  DEFAULT CHARSET = utf8;

INSERT INTO `#__hallowelt` (`hallo`) VALUES
('Hello World!'),
('Good bye World!');
