<?php

class Magestore_Storepickup_Block_Adminhtml_Specialday_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

	public function __construct() {
		parent::__construct();

		$this->_objectId = 'id';
		$this->_blockGroup = 'storepickup';
		$this->_controller = 'adminhtml_specialday';

		$this->_updateButton('save', 'label', Mage::helper('storepickup')->__('Save Special Day'));
		$this->_updateButton('delete', 'label', Mage::helper('storepickup')->__('Delete Special Day'));

		$this->_addButton('saveandcontinue', array(
			'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
			'onclick' => 'saveAndContinueEdit()',
			'class' => 'save',
		), -100);

		$this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }

            Event.observe('specialday_date_to','change',function(){
                            if(!$('date').value){
                                    alert('" . $this->__('You need to insert From Date') . "');
                                    $('specialday_date_to').value='';
                                }
                            if($('date').value && ($('date').value>$('specialday_date_to').value)){
                                alert('" . $this->__('Invalid Date') . "');
                                    $('specialday_date_to').value='';
                            }
                            });
        ";
	}

	public function getHeaderText() {
		if (Mage::registry('specialday_data') && Mage::registry('specialday_data')->getId()) {
			//ZEND_DEBUG::DUMP($this->htmlEscape(Mage::registry('specialday_data')->getData('specialday_name')));die();
			return Mage::helper('storepickup')->__("Edit Special Day '%s'", $this->htmlEscape(Mage::registry('specialday_data')->getData('special_name')));
		} elseif ($this->getRequest()->getParam('id')) {

			return Mage::helper('storepickup')->__("Edit Special Day '%s'", Mage::getModel('storepickup/specialday')->load($this->getRequest()->getParam('id'))->getData('special_name'));
		} else {
			return Mage::helper('storepickup')->__('Add Special Day');
		}
	}

}
