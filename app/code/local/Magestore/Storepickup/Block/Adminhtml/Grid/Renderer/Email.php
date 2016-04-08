<?php
class Magestore_Storepickup_Block_Adminhtml_Grid_Renderer_Email extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$ob = Mage::getModel('storepickup/message')->load($row->getId());
		$emailAdrress = $ob->getEmail();							
		return '<a href="mailto:'.$emailAdrress.'">'.$emailAdrress.'</a>';
	}
}