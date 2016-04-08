<?php

class Magestore_Storepickup_Model_Sales_Quote_Address_Total_Storepickup extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    public function collect(Mage_Sales_Model_Quote_Address $address) {
        $datashipping = Mage::getSingleton('checkout/session')->getData('storepickup_session');
        $shippingMethod = $address->getShippingMethod();
        $shippingMethod = explode('_', $shippingMethod);
        $shippingCode = $shippingMethod[0];
        if ($shippingCode != "storepickup")
            return $this;
        if ((isset($datashipping['date']) && $datashipping['date']) || (isset($datashipping['time']) && $datashipping['time']))
            $address->setShippingDescription($address->getShippingDescription() . ' ' . Mage::helper('storepickup')->__('Pickup Time:') . ' ' . $datashipping['date'] . ' ' . $datashipping['time']);
        return $this;
    }

}
