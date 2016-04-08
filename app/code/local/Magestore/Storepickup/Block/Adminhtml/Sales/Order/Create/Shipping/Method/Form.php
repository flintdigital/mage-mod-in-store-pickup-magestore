<?php
class Magestore_Storepickup_Block_Adminhtml_Sales_Order_Create_Shipping_Method_Form extends Mage_Adminhtml_Block_Sales_Order_Create_Shipping_Method_Form
{
	protected function _toHtml(){
		$this->setTemplate('storepickup/form.phtml');
		return parent::_toHtml();
	}
	
	//(6-2-2013)
	public function getCoordinates($store)
	{		
		$address['street'] = $store->getSuburb();
		$address['street'] = '';
		$address['city'] = $store->getCity();
		$address['region'] = $store->getRegion();
		$address['zipcode'] = $store->getZipcode();
		$address['country'] = $store->getCountryName();
		
		$coordinates = Mage::getModel('storepickup/gmap')
							->getCoordinates($address);
		if(! $coordinates)
		{	
			$coordinates['lat'] = '0.000';
			$coordinates['lng'] = '0.000';			
		}

		return $coordinates;
	}
}