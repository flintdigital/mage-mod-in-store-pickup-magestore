<?php

class Magestore_Storepickup_Block_Adminhtml_Tag extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
		$this->_controller = 'adminhtml_tag';
		$this->_blockGroup = 'storepickup';
		$this->_headerText = Mage::helper('storepickup')->__('Tag Manager');
		$this->_addButtonLabel = Mage::helper('storepickup')->__('Add Tag');
		parent::__construct();
	}
}