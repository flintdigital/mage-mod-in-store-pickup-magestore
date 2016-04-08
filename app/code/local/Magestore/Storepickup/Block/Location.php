<?php
class Magestore_Storepickup_Block_Location extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
	
	public function getListCountry()
	{
		return Mage::helper('storepickup')->getListCountry();
	}	
	
	public function getListRegion()
	{
		return Mage::helper('storepickup/location')->getListRegion();
	}
	
	public function getListCity()
	{
		if($this->getCurrRegionId())
			return Mage::helper('storepickup/location')->getListCity($this->getCurrRegionId());
		else
			return null;
	}

	public function getListSuburb()
	{
		if($this->getCurrCityId())
			return Mage::helper('storepickup/location')->getListSuburb($this->getCurrCityId());
		else
			return null;
	}	
	
	public function getCurrCountryId()
	{
		if(!$this->hasData('country_id'))
		{
			if($this->_getShippingAddress())
			{
				$shippingAddress = $this->_getShippingAddress();
			
				if($shippingAddress->getCountryId());
					$this->setData('country_id',$shippingAddress->getCountryId());
			} 
		}	
		return $this->getData('country_id');
	}			
	
	public function getCurrState()
	{
		if(!$this->hasData('state'))
		{
			if($this->_getShippingAddress())
			{
				$shippingAddress = $this->_getShippingAddress();
				if($shippingAddress->getState())
				{
					$this->setData('state',$shippingAddress->getState());
				} 
				else {
					$collection = Mage::getResourceModel('storepickup/store_collection')
							->addFieldToFilter('state',$shippingAddress->getState());
					if(count($collection))
					{
						foreach($collection as $item){}
						$this->setData('state',$item->getState());
					}
				}
			} 
		}	
		return $this->getData('state');
	}
	
	public function getCurrCityId()
	{
		if(!$this->hasData('city_id'))
		{
			if($this->_getShippingAddress())
			{
				$shippingAddress = $this->_getShippingAddress();
				$collection = Mage::getResourceModel('storepickup/store')
						->addFieldToFilter('city',$shippingAddress->getCity());
				if(count($collection))
				{
					foreach($collection as $item){}
					$this->setData('city_id',$item->getCityId());
				}
			} 
		}	
		return $this->getData('city_id');
	}

	public function getCurrSuburbId()
	{
		return null;
	}	
	
	protected function _getShippingAddress()
	{
		if(! $this->hasData('shippingaddress'))
		{
			$shippingAddress = Mage::getSingleton('checkout/cart')
								->getQuote()
								->getShippingAddress();
			$this->setData('shippingaddress',$shippingAddress);
		}	
		
		return $this->getData('shippingaddress');
	}
}