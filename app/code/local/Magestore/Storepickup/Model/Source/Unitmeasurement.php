<?php
class Magestore_Storepickup_Model_Source_Unitmeasurement
{
    public function toOptionArray()
	{
            return array(  
                array('value' => 'metric', 'label' => Mage::helper('storepickup')->__('Kilometers')),
                array('value' => 'imperial', 'label' => Mage::helper('storepickup')->__('Miles')),
        );
    }
}