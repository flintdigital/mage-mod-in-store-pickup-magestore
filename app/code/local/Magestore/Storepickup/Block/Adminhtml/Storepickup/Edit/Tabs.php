<?php

class Magestore_Storepickup_Block_Adminhtml_Storepickup_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('storepickup_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('storepickup')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('storepickup')->__('Item Information'),
          'title'     => Mage::helper('storepickup')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('storepickup/adminhtml_storepickup_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}