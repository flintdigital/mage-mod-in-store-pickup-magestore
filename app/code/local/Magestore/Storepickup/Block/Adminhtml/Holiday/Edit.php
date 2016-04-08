<?php

class Magestore_Storepickup_Block_Adminhtml_Holiday_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'storepickup';
        $this->_controller = 'adminhtml_holiday';

        $this->_updateButton('save', 'label', Mage::helper('storepickup')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('storepickup')->__('Delete Item'));

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }	
            
            Event.observe('holiday_date_to','change',function(){
                            if(!$('date').value){
                                    alert('" . $this->__('You need to insert data into From Date') . "');
                                    $('holiday_date_to').value='';
                                }
                            if($('date').value && ($('date').value>$('holiday_date_to').value)){
                                alert('" . $this->__('Invalid Date') . "');
                                    $('holiday_date_to').value='';
                            }
                            });
        ";
    }

    public function getHeaderText() {
        if (Mage::registry('holiday_data') && Mage::registry('holiday_data')->getId()) {
            return Mage::helper('storepickup')->__("Edit Holiday '%s'", $this->htmlEscape(Mage::registry('holiday_data')->getData('holiday_name')));
        } elseif ($this->getRequest()->getParam('id')) {
            return Mage::helper('storepickup')->__("Edit Holiday '%s'", Mage::getModel('storepickup/holiday')->load($this->getRequest()->getParam('id'))->getData('holiday_name'));
        } else {
            return Mage::helper('storepickup')->__('Add Holiday');
        }
    }

}
