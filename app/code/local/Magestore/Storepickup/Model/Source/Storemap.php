<?php

class Magestore_Storepickup_Model_Source_Storemap
{
    public function toOptionArray()
	{
		return array(
            array('value'=>2, 'label'=>Mage::helper('storepickup')->__('Selector')),
            array('value'=>1, 'label'=>Mage::helper('storepickup')->__('Map')),
        );	
	}
}