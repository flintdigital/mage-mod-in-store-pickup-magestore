<?php

$installer = $this;

$installer->startSetup();

$installer->run("

	DROP TABLE IF EXISTS {$this->getTable('storepickup_order')};

	CREATE TABLE {$this->getTable('storepickup_order')}  (
	  `storeorder_id` int(11) NOT NULL auto_increment,
	  `order_id` int(11) NOT NULL default '0',
	  `store_id` int(11) NOT NULL default '0',
	  `shipping_date` date NOT NULL,
	  `shipping_time` time NULL,
	  PRIMARY KEY  (`storeorder_id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

");



$installer->endSetup(); 