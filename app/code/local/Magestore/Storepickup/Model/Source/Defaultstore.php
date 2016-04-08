<?php

class Magestore_Storepickup_Model_Source_Defaultstore
{
    public function toOptionArray()
	{   
        $collection = Mage::getModel('storepickup/store')->getCollection();
		$arr = array();
        $arr [] = array('value' => 0, 'label' => '---Choose Default Store---');
        foreach ($collection as $item) {
            $arr[] = array('value' => $item->getId(), 'label' => $item->getStoreName());
        }
        //Zend_debug::dump($arr);die();
        return $arr;
	}
}