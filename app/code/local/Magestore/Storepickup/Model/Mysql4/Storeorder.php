<?php

class Magestore_Storepickup_Model_Mysql4_Storeorder extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('storepickup/storeorder', 'storeorder_id');
    }
}