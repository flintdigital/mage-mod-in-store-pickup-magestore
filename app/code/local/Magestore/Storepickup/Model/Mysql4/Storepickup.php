<?php

class Magestore_Storepickup_Model_Mysql4_Storepickup extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the storepickup_id refers to the key field in your database table.
        $this->_init('storepickup/storepickup', 'storepickup_id');
    }
}