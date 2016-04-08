<?php
class Magestore_Storepickup_Block_Storepickup extends Mage_Core_Block_Template
{
	public function __construct()
	{	
		parent::__construct();
		
		$this->setData('shipping_model',Mage::getModel('storepickup/shipping_storepickup'));
	}
	
	public function _prepareLayout()
    {
		$return = parent::_prepareLayout();
		
		$listStore = $this->getStoreByLocation();
			
		$this->setListStoreLocation($listStore);
		
		$this->setTemplate('storepickup/storepickup.phtml');
		
		return $return;
	}
	
	public function getListTime()
	{
		return Mage::helper('storepickup')->getListTime();		
	}
	
	public function has_stores()
	{
		return true;
	}
	
	public function getStoreByLocation()
	{
		
		if(! $this->hasData('storecollection'))
		{
			if($this->getShippingModel()->getConfigData('active_gapi'))	
			{
				$stores =  Mage::getSingleton('storepickup/store')->filterStoresUseGAPI();
			} else {
				$stores =  Mage::getSingleton('storepickup/store')->convertToList();
			}
			//var_dump($stores);die();
			$this->setData('storecollection',$stores);
		}
		return $this->getData('storecollection');
	}

	public function getAllStores()
	{
		return $collection = Mage::getModel('storepickup/store')->getCollection()
							->addFieldToFilter('status',1);
	}

	public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }	
}