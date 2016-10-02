CREATE TABLE IF NOT EXISTS `#__minitek_wall_widgets` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `asset_id` int(10) unsigned NOT NULL DEFAULT '0',
 `type_id` varchar(100) NOT NULL,
 `source_id` varchar(100) NOT NULL,
 `source_type_id` varchar(100) NOT NULL,
 `name` varchar(255) NOT NULL,
 `description` text NOT NULL,
 `masonry_params` text NOT NULL,
 `slider_params` text NOT NULL,
 `scroller_params` text NOT NULL,
 `state` tinyint(1) NOT NULL DEFAULT '0',
 `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
 `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__minitek_wall_widgets_source` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `widget_id` int(10) unsigned NOT NULL,
 `joomla_source` text NOT NULL,
 `k2_source` text NOT NULL,
 `virtuemart_source` text NOT NULL,
 `jomsocial_source` text NOT NULL,
 `easyblog_source` text NOT NULL,
 `folder_source` text NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;