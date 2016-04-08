<?php

class Magestore_Storepickup_Model_Source_Displayselectbox
{
    public function toOptionArray(){   
        return array(
            0   => array(
                        'value'=> 1,
                        'label' => Mage::helper('storepickup')->__('Select Box')
                    ),
            1   => array(
                        'value'=> 2,
                        'label' => Mage::helper('storepickup')->__('popup')
                    ),
            2   => array(
                        'value'=> 3,
                        'label' => Mage::helper('storepickup')->__('both select box and popup')
                    ),
        );
	}
}