<?php

class Magestore_Storepickup_Block_Adminhtml_Store_Import_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('importstore_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('storepickup')->__('Import File'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('storepickup')->__('Import File'),
          'title'     => Mage::helper('storepickup')->__('Import File'),
          'content'   => $this->getLayout()->createBlock('storepickup/adminhtml_store_import_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}