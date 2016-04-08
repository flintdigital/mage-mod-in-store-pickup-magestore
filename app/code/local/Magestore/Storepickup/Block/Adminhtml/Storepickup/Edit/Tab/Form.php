<?php

class Magestore_Storepickup_Block_Adminhtml_Storepickup_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('storepickup_form', array('legend'=>Mage::helper('storepickup')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('storepickup')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('storepickup')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('storepickup')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('storepickup')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('storepickup')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('storepickup')->__('Content'),
          'title'     => Mage::helper('storepickup')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getStorepickupData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getStorepickupData());
          Mage::getSingleton('adminhtml/session')->setStorepickupData(null);
      } elseif ( Mage::registry('storepickup_data') ) {
          $form->setValues(Mage::registry('storepickup_data')->getData());
      }
      return parent::_prepareForm();
  }
}