<?php

class Magestore_Storepickup_Block_Adminhtml_Tag_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
	public function __construct() {
		parent::__construct();
		$this->setId('tag_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('storepickup')->__('Tag Information'));
	}

	protected function _beforeToHtml() {
		$this->addTab('form_section', array(
			'label' => Mage::helper('storepickup')->__('Tag Information'),
			'title' => Mage::helper('storepickup')->__('Tag Information'),
			'content' => $this->getLayout()->createBlock('storepickup/adminhtml_tag_edit_tab_form')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}