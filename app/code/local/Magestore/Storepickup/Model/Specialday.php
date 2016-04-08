<?php

class Magestore_Storepickup_Model_Specialday extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('storepickup/specialday');
    }

    public function isSpecialday($date, $store_id) {
        $check = false;
        $date = substr($date, 6, 4) . '-' . substr($date, 0, 3) . substr($date, 3, 2);

        $collection = $this->getCollection()
                ->addFieldToFilter('store_id', array('finset' => $store_id));

        foreach ($collection as $specialday) {
            if ($date >= $specialday->getDate() && $date <= $specialday->getSpecialdayDateTo()) {
                $check = true;
            }
        }
       
        return $check;
       
    }

}
