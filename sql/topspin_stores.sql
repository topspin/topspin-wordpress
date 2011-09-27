CREATE TABLE IF NOT EXISTS `<?php echo $wpdb->prefix;?>topspin_stores` (
  `store_id` int(11) NOT NULL auto_increment,
  `post_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `created_date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `items_per_page` int(11) NOT NULL,
  `show_all_items` tinyint(1) NOT NULL,
  `grid_columns` int(11) NOT NULL,
  `default_sorting` varchar(255) NOT NULL,
  `default_sorting_by` varchar(255) NOT NULL,
  `items_order` longtext NOT NULL,
  `featured_item` int(11) NOT NULL,
  `page_template` varchar(255) NOT NULL,
  `internal_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`store_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;