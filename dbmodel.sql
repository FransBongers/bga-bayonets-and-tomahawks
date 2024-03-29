
-- ------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- bayonetsandtomahawks implementation : © <Your name here> <Your email address here>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-- -----

CREATE TABLE IF NOT EXISTS `global_variables` (
  `name` varchar(50) NOT NULL,
  `value` json,
  PRIMARY KEY (`name`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;


CREATE TABLE IF NOT EXISTS `user_preferences` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` int(10) NOT NULL,
  `pref_id` int(10) NOT NULL,
  `pref_value` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `move_id` int(10) NOT NULL,
  `table` varchar(32) NOT NULL,
  `primary` varchar(32) NOT NULL,
  `type` varchar(32) NOT NULL,
  `affected` JSON,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `cards` (
  `card_id` varchar(100) NOT NULL,
  `card_location` varchar(32) NOT NULL,
  `card_state` int(10) DEFAULT 0,
  `extra_data` JSON NULL,
  PRIMARY KEY (`card_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `units` (
  `unit_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `unit_location` varchar(32) NOT NULL,
  `unit_state` int(10) DEFAULT 0,
  `counter_id` VARCHAR(255) NOT NULL,
  `extra_data` JSON NULL,
  PRIMARY KEY (`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `spaces` (
  `space_id` varchar(100)  NOT NULL,
  `control` VARCHAR(255) NOT NULL,
  -- `extra_data` JSON NULL,
  PRIMARY KEY (`space_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tokens` (
  `token_id` varchar(100) NOT NULL,
  `token_location` varchar(32) NOT NULL,
  `token_state` int(10) DEFAULT 0,
  `extra_data` JSON NULL,
  PRIMARY KEY (`token_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

ALTER TABLE `gamelog`
ADD `cancel` TINYINT(1) NOT NULL DEFAULT 0;