<?php

class Magestore_Storepickup_Model_Source_Systemunit{
    public function toOptionArray(){
	return array(
            0   => array(
                        'value'=> 'km',
                        'label' => Mage::helper('storepickup')->__('Kilometers')
                    ),
            1   => array(
                        'value'=> 'mi',
                        'label' => Mage::helper('storepickup')->__('Miles')
                    ),
            2   => array(
                        'value'=> 'm',
                        'label' => Mage::helper('storepickup')->__('Meters')
                    ),
        );
    }
}