<?php

class Magestore_Storepickup_Block_Tags extends Mage_Core_Block_Template {

    public function getTags(){
        $collection = Mage::getModel('storepickup/tag')->getCollection()
            ->addFieldToFilter('status', 1);
        return $collection;
    }
}
