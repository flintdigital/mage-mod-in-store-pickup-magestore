<?php

$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('storepickup_message')};
DROP TABLE IF EXISTS {$this->getTable('storepickup_image')};

CREATE TABLE {$this->getTable('storepickup_message')} (
`message_id` int(11) unsigned NOT NULL auto_increment,
`message` varchar(255) NULL,
`email` varchar(255) NULL,
`name` varchar(255) NULL,
`store_id` int(11) unsigned NOT NULL,
`date_sent` datetime NULL,
    INDEX(`store_id`),
    FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('storepickup_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
PRIMARY KEY (`message_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
    
CREATE TABLE {$this->getTable('storepickup_image')} (
`image_id` int(11) unsigned NOT NULL auto_increment,
`statuses` int(11),
`del` int(11),
`options` int(11),
`name` varchar(255),
`store_id` int(11) unsigned NOT NULL,
    INDEX(`store_id`),
    FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('storepickup_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
PRIMARY KEY (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;         
");

$installer->endSetup(); 