<?php
class Magestore_Storepickup_Model_Source_Timeformat
{
    public function toOptionArray()
	{
            return array(  
                array('value' => '24', 'label' => Mage::helper('storepickup')->__('24h')),
                array('value' => '12', 'label' => Mage::helper('storepickup')->__('12h')),
        );
    }
}