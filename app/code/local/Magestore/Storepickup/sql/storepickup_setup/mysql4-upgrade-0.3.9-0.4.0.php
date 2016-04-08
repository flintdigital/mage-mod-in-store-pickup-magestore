<?php

$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE {$this->getTable('storepickup_holiday')} 
		ADD `holiday_date_to` date NOT NULL;
        
    ALTER TABLE  {$this->getTable('storepickup_store')}
		ADD COLUMN `monday_status` smallint(6) NOT NULL default '1'
			AFTER `store_longitude`,
		ADD COLUMN `tuesday_status` smallint(6) NOT NULL default '1'
			AFTER `monday_close`,
		ADD COLUMN `wednesday_status` smallint(6) NOT NULL default '1'
			AFTER `tuesday_close`,
		ADD COLUMN `thursday_status` smallint(6) NOT NULL default '1'
			AFTER `wednesday_close`,
		ADD COLUMN `friday_status` smallint(6) NOT NULL default '1'
			AFTER `thursday_close`,
		ADD COLUMN `saturday_status` smallint(6) NOT NULL default '1'
			AFTER `friday_close`,
		ADD COLUMN `sunday_status` smallint(6) NOT NULL default '1'
			AFTER `saturday_close`;
    
    ALTER TABLE  {$this->getTable('storepickup_store')}
		ADD COLUMN `monday_time_interval` smallint(6) NOT NULL default '15'
			AFTER `monday_status`,
		ADD COLUMN `tuesday_time_interval` smallint(6) NOT NULL default '15'
			AFTER `tuesday_status`,
		ADD COLUMN `wednesday_time_interval` smallint(6) NOT NULL default '15'
			AFTER `wednesday_status`,
		ADD COLUMN `thursday_time_interval` smallint(6) NOT NULL default '15'
			AFTER `thursday_status`,
		ADD COLUMN `friday_time_interval` smallint(6) NOT NULL default '15'
			AFTER `friday_status`,
		ADD COLUMN `saturday_time_interval` smallint(6) NOT NULL default '15'
			AFTER `saturday_status`,
		ADD COLUMN `sunday_time_interval` smallint(6) NOT NULL default '15'
			AFTER `sunday_status`;
    
    ALTER TABLE  {$this->getTable('storepickup_store')}
		ADD COLUMN `monday_available_slot` smallint(6) NOT NULL default '0'
			AFTER `monday_close`,
		ADD COLUMN `tuesday_available_slot` smallint(6) NOT NULL default '0'
			AFTER `tuesday_close`,
		ADD COLUMN `wednesday_available_slot` smallint(6) NOT NULL default '0'
			AFTER `wednesday_close`,
		ADD COLUMN `thursday_available_slot` smallint(6) NOT NULL default '0'
			AFTER `thursday_close`,
		ADD COLUMN `friday_available_slot` smallint(6) NOT NULL default '0'
			AFTER `friday_close`,
		ADD COLUMN `saturday_available_slot` smallint(6) NOT NULL default '0'
			AFTER `saturday_close`,
		ADD COLUMN `sunday_available_slot` smallint(6) NOT NULL default '0'
			AFTER `sunday_close`;
		
	ALTER TABLE {$this->getTable('storepickup_holiday')} 
		CHANGE `store_id` `store_id` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0';
        
    DROP TABLE IF EXISTS {$this->getTable('storepickup_specialday')};
    CREATE TABLE {$this->getTable('storepickup_specialday')}  (
        `specialday_id` int(11) NOT NULL auto_increment,
        `store_id` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
        `date` date NOT NULL,
        `specialday_date_to` date NOT NULL,      
        `specialday_time_open` varchar(5) NOT NULL,
        `specialday_time_close` varchar(5) NOT NULL,
        `specialday_time_interval` smallint(6) NOT NULL default '0',  
        `comment` varchar(255) default NULL,
      PRIMARY KEY  (`specialday_id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
	
	ALTER TABLE  {$this->getTable('storepickup_store')}
		ADD COLUMN `pin_color` varchar(8) NOT NULL default 'f75448'
			AFTER `sunday_available_slot`;
	
	ALTER TABLE {$this->getTable('sales_flat_order')} 
		CHANGE `shipping_description` `shipping_description` VARCHAR( 350 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Shipping Description';
		
	ALTER TABLE  {$this->getTable('storepickup_store')}
        ADD COLUMN `url_id_path`  VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0';   
    
    ");
$holidays = Mage::getModel('storepickup/holiday')->getCollection();
foreach ($holidays as $holiday) {
    if (!$holiday->getHolidayDateTo() || $holiday->getHolidayDateTo() == '0000-00-00') {
        $holiday->setHolidayDateTo($holiday->getDate());
        $holiday->save();
    }
}
$stores = Mage::getModel('storepickup/store')->getCollection();
        foreach($stores as $store){
            $urlIdPath =  strtolower(trim($store->getStoreName(), ' '));        
            $urlIdPath = Mage::helper('storepickup/url')->characterSpecial($urlIdPath); 
            $key = $stores->addFieldToFilter('url_id_path', $urlIdPath)->getFirstItem();  

            if($key->getId()!=$store->getId())
                $urlIdPath .= '-1';

            if($store->getUrlIdPath()==0){        
                $store->setUrlIdPath($urlIdPath);
                $store->save();
            }      
        }



$installer->endSetup();
