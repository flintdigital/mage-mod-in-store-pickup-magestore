<?php

class Magestore_Storepickup_Model_Value extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('storepickup/value');
    }
    
    public function loadAttributeValue($storePickupId, $storeId, $attributeCode) {
        $attributeValue = $this->getCollection()
    		->addFieldToFilter('storepickup_id', $storePickupId)
    		->addFieldToFilter('store_id', $storeId)
    		->addFieldToFilter('attribute_code',$attributeCode)
    		->getFirstItem();
		$this->setData('storepickup_id', $storePickupId)
			->setData('store_id',$storeId)
			->setData('attribute_code',$attributeCode);
    	if ($attributeValue)
    		$this->addData($attributeValue->getData())
    			->setId($attributeValue->getId());
		return $this;
    }
}