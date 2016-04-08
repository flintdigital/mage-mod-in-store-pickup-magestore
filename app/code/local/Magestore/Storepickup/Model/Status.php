<?php

class Magestore_Storepickup_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('storepickup')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('storepickup')->__('Disabled')
        );
    }
}