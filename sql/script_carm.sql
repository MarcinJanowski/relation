-- Tables creation for Relation plugin version 1.2.0
--
-- Philippe THOIREY 2010/09/01
-- ------------------------------------------------------
--

CREATE TABLE IF NOT EXISTS `glpi_plugin_relation_relations` (
  `id` int(11) NOT NULL auto_increment,
  `items_id` int(11) NOT NULL default '0',
  `itemtype` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `parent_id` int(11) NOT NULL default '0',
  `parent_type` varchar(100) collate utf8_unicode_ci default NULL,
  `relation_type` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `itemtype` (`itemtype`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_relation_typerelations` (
  `id` int(11) NOT NULL auto_increment,
  `entities_id` int(11) NOT NULL default '0',
  `is_recursive` tinyint(1) NOT NULL default '0',
  `name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `invname` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `comment` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `entities_id` (`entities_id`),
  KEY `is_recursive` (`is_recursive`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_relation_relationclases` (
  `id` int(11) NOT NULL auto_increment,
  `entities_id` int(11) NOT NULL default '0',
  `is_recursive` tinyint(1) NOT NULL default '0',
  `classname` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `classlist` text collate utf8_unicode_ci NOT NULL,
  `comment` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`),
  KEY `classname` (`classname`),
  KEY `entities_id` (`entities_id`),
  KEY `is_recursive` (`is_recursive`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_relation_clases` (
  `id` int(11) NOT NULL auto_increment,
  `entities_id` int(11) NOT NULL default '0',
  `is_recursive` tinyint(1) NOT NULL default '0',
  `name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `viewname` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `comment` text collate utf8_unicode_ci,  
  `img` VARCHAR(45) DEFAULT 'nothing.png',
  `is_visible` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `entities_id` (`entities_id`),
  KEY `is_recursive` (`is_recursive`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE TABLE `glpi_plugin_relation_typerelations`;
INSERT INTO `glpi_plugin_relation_typerelations` (`id`,`entities_id`,`is_recursive`,`name`,`invname`,`comment`) 
	VALUES 	(1,0,0,'It is part of','It is part of (reverse)','It is part of'),
			(2,0,0,'Linked with','Linked with (reverse)','Linked with'),
			(3,0,0,'Use','Used by','Use'),
			(4,0,0,'He is the father of','Is son of','Father son');

