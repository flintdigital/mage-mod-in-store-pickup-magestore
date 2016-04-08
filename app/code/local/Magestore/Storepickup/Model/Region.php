<?php

class Magestore_Storepickup_Model_Region extends Mage_Core_Model_Abstract
{	
    protected function _construct()
    {
        $this->_init('storepickup/region');
    }
	
	public function save()
	{
		$code = $this->_generateCode($this->getData('default_name'));
		$this->setData('code',$code);
		return parent::save();
	}
	
	protected function _generateCode($name)
	{
		return strtoupper(substr($name,0,2));
	}	
}