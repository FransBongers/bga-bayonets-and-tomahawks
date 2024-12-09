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
-- CREATE TABLE IF NOT EXISTS `custom_log` (
--   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
--   `type` varchar(32) NOT NULL,
--   `round` varchar(32) NOT NULL,
--   `year` int(10) NOT NULL,
--   `data` JSON,
--   PRIMARY KEY (`id`)
-- ) ENGINE = InnoDB DEFAULT CHARSET = utf8;
CREATE TABLE IF NOT EXISTS `cards` (
  `card_id` varchar(100) NOT NULL,
  `card_location` varchar(32) NOT NULL,
  `card_state` int(10) DEFAULT 0,
  `extra_data` JSON NULL,
  PRIMARY KEY (`card_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
CREATE TABLE IF NOT EXISTS `connections` (
  `connection_id` varchar(100) NOT NULL,
  `connection_location` varchar(32) NOT NULL,
  `connection_state` int(10) DEFAULT 0,
  `british_limit` int(10) DEFAULT 0,
  `french_limit` int(10) DEFAULT 0,
  `british_road_used` tinyint(1) NOT NULL DEFAULT 0,
  `french_road_used` tinyint(1) NOT NULL DEFAULT 0,
  `road` int(10) DEFAULT 0,
  -- `extra_data` JSON NULL,
  PRIMARY KEY (`connection_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
CREATE TABLE IF NOT EXISTS `units` (
  `unit_id` varchar(100) NOT NULL,
  `unit_location` varchar(64) NOT NULL,
  `unit_state` int(10) DEFAULT 0,
  `counter_id` VARCHAR(255) NOT NULL,
  `previous_location` varchar(64) DEFAULT NULL,
  `reduced` tinyint(1) DEFAULT 0,
  `spent` tinyint(1) DEFAULT 0,
  -- `extra_data` JSON NULL,
  PRIMARY KEY (`unit_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
CREATE TABLE IF NOT EXISTS `spaces` (
  `space_id` varchar(100) NOT NULL,
  `space_location` varchar(32) NOT NULL,
  `space_state` int(10) DEFAULT 0,
  `battle` int(10) DEFAULT 0,
  `control` VARCHAR(10) NOT NULL,
  `control_start_of_turn` VARCHAR(10) NOT NULL,
  `units_start_of_turn` VARCHAR(10) NOT NULL DEFAULT "none",
  `defender` VARCHAR(10),
  `fort_construction` int(10) DEFAULT 0,
  `raided` VARCHAR(10),
  -- `extra_data` JSON NULL,
  PRIMARY KEY (`space_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
CREATE TABLE IF NOT EXISTS `markers` (
  `marker_id` varchar(100) NOT NULL,
  `marker_location` varchar(64) NOT NULL,
  `side` int(10) DEFAULT 0,
  `marker_state` int(10) DEFAULT 0,
  `extra_data` JSON NULL,
  PRIMARY KEY (`marker_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
CREATE TABLE IF NOT EXISTS `wie_chits` (
  `wie_chit_id` varchar(100) NOT NULL,
  `wie_chit_location` varchar(64) NOT NULL,
  `wie_chit_state` int(10) DEFAULT 0,
  `value` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`wie_chit_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
ALTER TABLE `gamelog`
ADD `cancel` TINYINT(1) NOT NULL DEFAULT 0;