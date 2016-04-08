<?php

class Magestore_Storepickup_Model_Tag extends Mage_Core_Model_Abstract {
    public function _construct() {
        parent::_construct();
        $this->_init('storepickup/tag');
    }
    public function getOptionCountry(){
        $optionTag = array();
        $collection = $this->getCollection();
//            ->addFieldToFilter('status', 1);
        
        if(count($collection)){
            foreach($collection as $item){
                $optionTag[] = array('value' => $item->getId(), 'label' => $item->getTitle(), );
            }
        }
        return $optionTag;
    }
}
