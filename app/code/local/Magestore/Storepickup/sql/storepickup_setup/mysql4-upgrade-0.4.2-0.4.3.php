<?php 
$installer = $this;
$installer->startSetup();
// Required tables
$statusTable = $installer->getTable('sales/order_status');
$statusStateTable = $installer->getTable('sales/order_status_state');
 
// Insert statuses
$installer->getConnection()->insertArray(
    $statusTable,
    array(
        'status',
        'label'
    ),
    array(
        array('status' => 'store_pickup', 'label' => 'Store Pickup')
        
    )
);
 
// Insert states and mapping of statuses to states
$installer->getConnection()->insertArray(
    $statusStateTable,
    array(
        'status',
        'state',
        'is_default'
    ),
    array(
        array(
            'status' => 'store_pickup',
            'state' => 'store_pickup',
            'is_default' => 1
        )
    )
);
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('storepickup_tag')};
CREATE TABLE {$this->getTable('storepickup_tag')} (
  `tag_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `icon` varchar(255) NOT NULL default '',
  `content` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE {$this->getTable('storepickup_store')} ADD `tag_ids` varchar(200) NOT NULL default '';
    ");
$installer->endSetup(); 
?>