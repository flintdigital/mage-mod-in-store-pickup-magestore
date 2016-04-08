<?php

class Magestore_Storepickup_Model_Mysql4_Message extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {            
        $this->_init('storepickup/message', 'message_id');
    }
}