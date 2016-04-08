<?php
class Magestore_Storepickup_Block_Adminhtml_Specialday extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_specialday';
    $this->_blockGroup = 'storepickup';
    $this->_headerText = Mage::helper('storepickup')->__('Special Day Manager');
    $this->_addButtonLabel = Mage::helper('storepickup')->__('Add Special Day');
    parent::__construct();
  }
}