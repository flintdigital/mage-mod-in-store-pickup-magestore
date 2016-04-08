<?php
class Magestore_Storepickup_Block_Adminhtml_Holiday extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {    
    $this->_controller = 'adminhtml_holiday';
    $this->_blockGroup = 'storepickup';
    $this->_headerText = Mage::helper('storepickup')->__('Holiday Manager');
    $this->_addButtonLabel = Mage::helper('storepickup')->__('Add Holiday');
    parent::__construct();
  }
}