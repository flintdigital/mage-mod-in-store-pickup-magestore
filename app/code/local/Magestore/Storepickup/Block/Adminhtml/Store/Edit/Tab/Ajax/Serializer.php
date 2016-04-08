<?php

class Magestore_Storepickup_Block_Adminhtml_Store_Edit_Tab_Ajax_Serializer 
		extends Mage_Core_Block_Template
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('storepickup/serializer.phtml');
        return $this;
    }
	
	
    public function getObjectsJSON()
    {
        $result = array();
        if (count($this->getObjects())) {
            foreach ($this->getObjects() as $object) {
                $id = $object->getId();
                $result[$id] = 0;
            }
        }
		
		if (count($result)) {
            return Zend_Json::encode($result);
        }
		
        return '{}';
    }
}