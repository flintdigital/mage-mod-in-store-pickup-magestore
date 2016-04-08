<?php
class Magestore_Storepickup_Block_Adminhtml_Store extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_store';
    $this->_blockGroup = 'storepickup';
    $this->_headerText = Mage::helper('storepickup')->__('Store Manager');
    $this->_addButtonLabel = Mage::helper('storepickup')->__('Add Store');
    parent::__construct();
	$this->_addButton('import_store', array(
		'label'     => Mage::helper('storepickup')->__('Import Store'),
		'onclick'   => 'location.href=\''. $this->getUrl('*/storepickup_import/importstore',array()) .'\'',
		'class'     => 'add',
	),1000);		
  }
}