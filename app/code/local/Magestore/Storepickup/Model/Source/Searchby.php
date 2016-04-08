<?php

class Magestore_Storepickup_Model_Source_Searchby{
    public function toOptionArray(){
	return array(
            0   => array(
                        'value'=> 'country_name',
                        'label' => Mage::helper('storepickup')->__('Country')
                    ),
            1   => array(
                        'value'=> 'state',
                        'label' => Mage::helper('storepickup')->__('State')
                    ),
            2   => array(
                        'value'=> 'city',
                        'label' => Mage::helper('storepickup')->__('City')
                    ),
            3   => array(
                        'value'=> 'store_name',
                        'label' => Mage::helper('storepickup')->__('Store Name')
                    ),
            4   => array(
                        'value'=> 'zipcode',
                        'label' => Mage::helper('storepickup')->__('Zipcode')
                    ),
        );
    }
}