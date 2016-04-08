<?php
class Magestore_Storepickup_Block_Adminhtml_Region extends Mage_Core_Block_Template
{
	public function getStore()
	{
		$collection = null;
		$id = $this->getRequest()->getParam('id');
		if($id)
		{
			$collection = Mage::getModel('storepickup/store')->load($id);
		}
		return $collection;
	}
}
?>