<?php
class Magestore_Storepickup_Block_Displayallstores extends Mage_Core_Block_Template
{
	public function addTopLinkStores()
	{
		$storeID = Mage::app()->getStore()->getId();
		if(Mage::getStoreConfig('carriers/storepickup/display_allstores',$storeID)==2) {
			$toplinkBlock = $this->getParentBlock();
			if($toplinkBlock)
			$toplinkBlock->addLink($this->__('Our Stores'),'storepickup/index/index','Our Stores',true,array(),10);
		}
	}
	
	public function addFooterLinkStores()
	{
		$storeID = Mage::app()->getStore()->getId();
		if(Mage::getStoreConfig('carriers/storepickup/display_allstores',$storeID)==1) {
			$footerBlock = $this->getParentBlock();
			if($footerBlock)
			$footerBlock->addLink($this->__('Our Stores'),'storepickup/index/index','Our Stores',true,array());
		}
	}
}

?>