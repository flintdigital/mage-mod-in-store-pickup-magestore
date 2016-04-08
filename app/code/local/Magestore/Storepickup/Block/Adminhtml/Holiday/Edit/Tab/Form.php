<?php

class Magestore_Storepickup_Block_Adminhtml_Holiday_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

	protected function _prepareForm() {
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('holiday_form', array('legend' => Mage::helper('storepickup')->__('Holiday Information')));
		$image_calendar = Mage::getBaseUrl('skin') . 'adminhtml/default/default/images/grid-cal.gif';

		$fieldset->addField('holiday_name', 'text', array(
			'label' => Mage::helper('storepickup')->__('Holiday Name'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'holiday_name',
		));

		$fieldset->addField('store_id', 'multiselect', array(
			'label' => Mage::helper('storepickup')->__('Store'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'store_id',
			'values' => Mage::helper('storepickup')->getStoreOptions2(),
			'note' => Mage::helper('storepickup')->__('Select store(s) applying holiday.'),
		));

		$fieldset->addField('date', 'date', array(
			'label' => Mage::helper('storepickup')->__('Starting Date'),
			'required' => true,
			'format' => 'yyyy-MM-dd',
			'image' => $image_calendar,
			'name' => 'date',
		));

		$fieldset->addField('holiday_date_to', 'date', array(
			'label' => Mage::helper('storepickup')->__('End Date'),
			'required' => true,
			'format' => 'yyyy-MM-dd',
			'image' => $image_calendar,
			'name' => 'holiday_date_to',
		));

		$fieldset->addField('comment', 'textarea', array(
			'name' => 'comment',
			'label' => Mage::helper('storepickup')->__('Comment'),
			'title' => Mage::helper('storepickup')->__('Comment'),
			'note' => Mage::helper('storepickup')->__('Notification message shown to customers'),
			//'style' => 'width:500px; height:100px;',
			'wysiwyg' => false,
			'required' => false,
		));

		if (Mage::getSingleton('adminhtml/session')->getHolidayData()) {
			$form->setValues(Mage::getSingleton('adminhtml/session')->getHolidayData());
			Mage::getSingleton('adminhtml/session')->setHolidayData(null);
		} elseif (Mage::registry('holiday_data')) {
			$form->setValues(Mage::registry('holiday_data')->getData());
		}
		return parent::_prepareForm();
	}

}
