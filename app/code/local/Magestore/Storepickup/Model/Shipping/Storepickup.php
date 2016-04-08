<?php

class Magestore_Storepickup_Model_Shipping_Storepickup extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {

	protected $_code = 'storepickup';

	public function getCode() {
		return $this->_code;
	}

	public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
		if (!Mage::helper('magenotification')->checkLicenseKey('Storepickup')) {
			return false;
		}
		if (!$this->getConfigFlag('active')) {
			return false;
		}

		$items = $request->getAllItems();

		if (!count($items)) {
			return;
		}

		$result = Mage::getModel('shipping/rate_result');
		/* @var $result Mage_Shipping_Model_Rate_Result */

		$result->append($this->_getStandardShippingRate());

		return $result;
	}
	protected function _getStandardShippingRate() {
		/* @var $rate Mage_Shipping_Model_Rate_Result_Method */
		$method = Mage::getModel('shipping/rate_result_method');

		$method->setCarrier($this->_code);

		/**
		 * getConfigData(config_key) returns the configuration value for the
		 * carriers/[carrier_code]/[config_key]
		 */
		$method->setCarrierTitle($this->getConfigData('title'));

		$method->setMethod('storepickup');
		$method->setMethodTitle($this->getConfigData('shipping_method_title'));
		$storepickup_shipping_price = Mage::getSingleton('checkout/session')->getData('storepickup_shipping_price');
		if (isset($storepickup_shipping_price)) {
			$price = Mage::getSingleton('checkout/session')->getData('storepickup_shipping_price');
		} else {
			$price = 0;
		}

		$shippingPrice = $this->getFinalPriceWithHandlingFee($price);
		$method->setPrice($shippingPrice);
		$method->setCost($shippingPrice);

		return $method;
	}
	public function getAllowedMethods() {
		return array('storepickup' => 'storepickup');
	}

}
