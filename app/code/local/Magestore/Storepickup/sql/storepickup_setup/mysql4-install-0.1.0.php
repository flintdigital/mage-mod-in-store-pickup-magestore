<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('storepickup_holiday')};
CREATE TABLE {$this->getTable('storepickup_holiday')}  (
  `holiday_id` int(11) NOT NULL auto_increment,
  `store_id` int(11) NOT NULL default '0',
  `date` date NOT NULL,
  `comment` varchar(255) default NULL,
  PRIMARY KEY  (`holiday_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

DROP TABLE IF EXISTS {$this->getTable('storepickup_store')};
CREATE TABLE {$this->getTable('storepickup_store')} (
  `store_id` int(11) unsigned NOT NULL auto_increment,
  `store_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` smallint(6) NOT NULL default '0',
  `address` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `state_id` int(11) NOT NULL,
  `country` varchar(255) NOT NULL,
  `monday_open` varchar(5) NOT NULL,
  `monday_close` varchar(5) NOT NULL,
  `tuesday_open` varchar(5) NOT NULL,
  `tuesday_close` varchar(5) NOT NULL,
  `wednesday_open` varchar(5) NOT NULL,
  `wednesday_close` varchar(5) NOT NULL,
  `thursday_open` varchar(5) NOT NULL,
  `thursday_close` varchar(5) NOT NULL,
  `friday_open` varchar(5) NOT NULL,
  `friday_close` varchar(5) NOT NULL,
  `saturday_open` varchar(5) NOT NULL,
  `saturday_close` varchar(5) NOT NULL,
  `sunday_open` varchar(5) NOT NULL,
  `sunday_close` varchar(5) NOT NULL,
  `minimum_gap` int(11) NOT NULL default '45',
  PRIMARY KEY  (`store_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

    ");
	
//get entity_type_id
$entity_type = Mage::getSingleton("eav/entity_type")->loadByCode("order");
$entity_type_id = $entity_type->getId();

$attribute = Mage::getModel("eav/entity_attribute");

//create attribute `order_store_id`
$data = array();
$data['id'] = null;
$data['entity_type_id'] = $entity_type_id;
$data['attribute_code'] = "order_store_id";
$data['backend_type'] = "int";
$data['frontend_input'] = "int";
     
$attribute->setData($data);
$attribute->save();

//create attribute `order_shipping_date`
$data['attribute_code'] = "order_shipping_date";
$data['backend_type'] = "varchar";
$data['frontend_input'] = "varchar";
     
$attribute->setData($data);
$attribute->setId(null);
$attribute->save();

//create attribute `order_shipping_time`
$data['attribute_code'] = "order_shipping_time";
$data['backend_type'] = "varchar";
$data['frontend_input'] = "varchar";
     
$attribute->setData($data);
$attribute->setId(null);
$attribute->save();

$installer->endSetup(); 