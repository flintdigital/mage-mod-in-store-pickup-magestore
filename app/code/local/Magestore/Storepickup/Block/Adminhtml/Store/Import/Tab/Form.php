<?php

class Magestore_Storepickup_Block_Adminhtml_Store_Import_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('import_form', array('legend'=>Mage::helper('storepickup')->__('Import File')));
     
      $fieldset->addField('csv_store', 'file', array(
          'label'     => Mage::helper('storepickup')->__('CSV File'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'csv_store',
          'note'      => Mage::helper('storepickup')->__("Click <a href='%s'>here</a> to download the sample csv file.", Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'storepickup/store.csv' )));
	  
      return parent::_prepareForm();
  }
}