<?php

class Magestore_Storepickup_Block_Adminhtml_Storepickup_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'storepickup';
        $this->_controller = 'adminhtml_storepickup';
        
        $this->_updateButton('save', 'label', Mage::helper('storepickup')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('storepickup')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('storepickup_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'storepickup_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'storepickup_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('storepickup_data') && Mage::registry('storepickup_data')->getId() ) {
            return Mage::helper('storepickup')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('storepickup_data')->getTitle()));
        } else {
            return Mage::helper('storepickup')->__('Add Item');
        }
    }
}