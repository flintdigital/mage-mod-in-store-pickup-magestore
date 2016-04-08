<?php
class Magestore_Storepickup_Block_Adminhtml_Storepickup extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_storepickup';
    $this->_blockGroup = 'storepickup';
    $this->_headerText = Mage::helper('storepickup')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('storepickup')->__('Add Item');
    parent::__construct();
  }
}