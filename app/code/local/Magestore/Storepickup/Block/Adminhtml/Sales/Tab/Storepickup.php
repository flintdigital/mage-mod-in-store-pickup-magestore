<?php

class Magestore_Storepickup_Block_Adminhtml_Sales_Tab_Storepickup 
		extends Mage_Adminhtml_Block_Widget_Form
		implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('storepickup/storepickup.phtml');
	}
	
	public function getTabLabel()	{
		return Mage::helper('sales')->__('Store Pickup');
	}

	public function getTabTitle() {
		return Mage::helper('sales')->__('Store Pickup');
	}
	
	public function canShowTab()	{
		if($this->getStorepickup())	
			return true;
		else
			return false;
		}
	
	public function isHidden()	{
		if($this->getStorepickup())
			return false;
		else
			return true;
	}		
	
	public function getStorepickup()
	{
            
		if(!$this->hasData('storepickup'))
		{
			$storepickup = null;
			
			$order = $this->getOrder();
			
			if (!$order) 
			{
				$this->setData('storepickup',null);
				return $this->getData('storepickup');
			}
			
			$order_id = $order->getId();
			
			$storepickup = Mage::helper('storepickup')->getStorepickupByOrderId($order_id);
			$this->setData('storepickup',$storepickup);
		}
		return $this->getData('storepickup');
	}
	
	public function getOrder()
    {       
        if (Mage::registry('current_order')) {
            return Mage::registry('current_order');
        }
        if (Mage::registry('order')) {
            return Mage::registry('order');
        }
       
    }

	public function getShippingTime($order_id)
	{		
		$time = null;
		if ($order_id) { 
			$storeorder = Mage::getModel('storepickup/storeorder')->getCollection()
						->addFieldToFilter('order_id',$order_id)
						->getFirstItem();
		}					
		if ($storeorder)
			$time = $storeorder->getShippingTime();	
		return 	$time;		
	}	
	
	public function getShippingDate($order_id)
	{		
		$date = null;
		if ($order_id) {
			$storeorder = Mage::getModel('storepickup/storeorder')->getCollection()
						->addFieldToFilter('order_id',$order_id)
						->getFirstItem();
		}				
		if ($storeorder)
			$date = $storeorder->getShippingDate();
		return 	$date;		
	}	
}