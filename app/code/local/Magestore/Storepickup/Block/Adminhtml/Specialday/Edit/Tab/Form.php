<?php

class Magestore_Storepickup_Block_Adminhtml_Specialday_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

	protected function _prepareForm() {
		$form = new Varien_Data_Form();
		$this->setForm($form);
		if (Mage::getSingleton('adminhtml/session')->getSpecialdayData()) {
			$data = Mage::getSingleton('adminhtml/session')->getSpecialdayData();
			Mage::getSingleton('adminhtml/session')->setSpecialdayData(null);
		} elseif (Mage::registry('specialday_data')) {
			$data = Mage::registry('specialday_data')->getData();
		}

		$fieldset = $form->addFieldset('specialday_form', array('legend' => Mage::helper('storepickup')->__('Special Day Information')));

		$html = Mage::helper('storepickup')->__('<span style="color:#EA4909">Note: Special days will be given the highest priority compared with Holidays and other days.</span>');
		$fieldset->addField('guide', 'note', array(
			'name' => 'guide',
			'text' => $html,
		));

		$image_calendar = Mage::getBaseUrl('skin') . 'adminhtml/default/default/images/grid-cal.gif';

		$fieldset->addField('special_name', 'text', array(
			'label' => Mage::helper('storepickup')->__('Special Day Name'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'special_name',
		));

		$fieldset->addField('store_id', 'multiselect', array(
			'label' => Mage::helper('storepickup')->__('Store'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'store_id',
			'values' => Mage::helper('storepickup')->getStoreOptions2(),
			'note' => Mage::helper('storepickup')->__('Select store(s) applying special day.'),
		));

		$fieldset->addField('date', 'date', array(
			'label' => Mage::helper('storepickup')->__('Starting Date'),
			'required' => true,
			'format' => 'yyyy-MM-dd',
			'image' => $image_calendar,
			'name' => 'date',
		));

		$fieldset->addField('specialday_date_to', 'date', array(
			'label' => Mage::helper('storepickup')->__('End Date'),
			'required' => true,
			'format' => 'yyyy-MM-dd',
			'image' => $image_calendar,
			'name' => 'specialday_date_to',
		));
		$timeInterval = array();
		foreach (array(15, 30, 45) as $key => $var) {
			$timeInterval[$key]['value'] = $var;
			$timeInterval[$key]['label'] = Mage::helper('storepickup')->__($var . ' minutes');
		}
		$fieldset->addField('specialday_time_interval', 'select', array(
			'label' => Mage::helper('storepickup')->__('Time Interval'),
			'required' => false,
			'name' => 'specialday_time_interval',
			'values' => $timeInterval,
			'note' => Mage::helper('storepickup')->__('Set interval between 2 Shipping Time options'),
		));

		$field_open = array('name' => 'specialday_time_open',
			'data' => isset($data['specialday_time_open']) ? $data['specialday_time_open'] : '',
		);
		$fieldset->addField('specialday_time_open', 'note', array(
			'label' => Mage::helper('storepickup')->__('Opening Time'),
			'name' => 'specialday_time_open',
			'text' => $this->getLayout()->createBlock('storepickup/adminhtml_time')->setData('field', $field_open)->setTemplate('storepickup/time.phtml')->toHtml(),
		));

		$field_close = array('name' => 'specialday_time_close',
			'data' => isset($data['specialday_time_close']) ? $data['specialday_time_close'] : '',
		);
		$fieldset->addField('specialday_time_close', 'note', array(
			'label' => Mage::helper('storepickup')->__('Closing Time'),
			'name' => 'specialday_time_close',
			'text' => $this->getLayout()->createBlock('storepickup/adminhtml_time')->setData('field', $field_close)->setTemplate('storepickup/time.phtml')->toHtml(),
		));

		$fieldset->addField('comment', 'textarea', array(
			'name' => 'comment',
			'label' => Mage::helper('storepickup')->__('Comment'),
			'title' => Mage::helper('storepickup')->__('Comment'),
			//'note' => Mage::helper('storepickup')->__('Message to customers'),
			'style' => 'width:500px; height:100px;',
			'wysiwyg' => false,
			'required' => false,
		));

		if (Mage::getSingleton('adminhtml/session')->getSpecialdayData()) {
			$form->setValues(Mage::getSingleton('adminhtml/session')->getSpecialdayData());
			Mage::getSingleton('adminhtml/session')->setSpecialdayData(null);
		} elseif (Mage::registry('specialday_data')) {
			$form->setValues(Mage::registry('specialday_data')->getData());
		}
		return parent::_prepareForm();
	}

}
