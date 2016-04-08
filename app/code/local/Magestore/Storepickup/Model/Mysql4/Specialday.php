<?php

class Magestore_Storepickup_Model_Mysql4_Specialday extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {           
        $this->_init('storepickup/specialday', 'specialday_id');
    }
}