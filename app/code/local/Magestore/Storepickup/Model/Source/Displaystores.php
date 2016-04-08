<?php

class Magestore_Storepickup_Model_Source_Displaystores
{
    public function toOptionArray()
	{
		return array(
            array('value'=>2, 'label'=>Mage::helper('storepickup')->__('Top Link')),
            array('value'=>1, 'label'=>Mage::helper('storepickup')->__('Footer Link')),
            array('value'=>0, 'label'=>Mage::helper('storepickup')->__('Not shown')),
		);	
	}
}