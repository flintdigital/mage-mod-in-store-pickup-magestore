<?php

$installer = $this;

$installer->startSetup();

$installer->run(" 
ALTER TABLE {$this->getTable('storepickup_store')}
 ADD COLUMN `status_order` int(11);
");

$installer->endSetup(); 