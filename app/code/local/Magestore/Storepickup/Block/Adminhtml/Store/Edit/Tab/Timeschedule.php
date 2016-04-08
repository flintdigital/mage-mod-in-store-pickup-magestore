<?php

class Magestore_Storepickup_Block_Adminhtml_Store_Edit_Tab_Timeschedule extends Mage_Adminhtml_Block_Widget_Form {

	protected function _prepareForm() {

		$form = new Varien_Data_Form();
		$this->setForm($form);

		if (Mage::getSingleton('adminhtml/session')->getStoreData()) {
			$data = Mage::getSingleton('adminhtml/session')->getStoreData();
			Mage::getSingleton('adminhtml/session')->setStoreData(null);
		} elseif (Mage::registry('store_data')) {
			$data = Mage::registry('store_data')->getData();
		}

		$timeInterval = array();
		foreach (array(15, 30, 45) as $key => $var) {
			$timeInterval[$key]['value'] = $var;
			$timeInterval[$key]['label'] = Mage::helper('storepickup')->__($var . ' minutes');
		}

		$html_button = '<button style="float:right" onclick="saveApplyForOtherDays()" class="scalable save" type="button" title="Apply for other days" id="id_apply"><span>' . Mage::helper('storepickup')->__('Apply to other days') . '</span></button><style>.entry-edit .entry-edit-head h4{width:100%;}</style>';
		foreach (array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') as $key => $day) {
			if ($key == 0) {
				$fieldset = $form->addFieldset('timeschedule_form_' . $day, array('legend' => Mage::helper('storepickup')->__(ucfirst($day) . $html_button)));
			} else {
				$fieldset = $form->addFieldset('timeschedule_form_' . $day, array('legend' => Mage::helper('storepickup')->__(ucfirst($day))));
			}

			$fieldset->addField($day . '_status', 'select', array(
				'label' => Mage::helper('storepickup')->__('Open'),
				'required' => false,
				'name' => $day . '_status',
				'class' => 'status_day',
				'values' => array(
					array(
						'value' => 1,
						'label' => Mage::helper('storepickup')->__('Yes'),
					),
					array(
						'value' => 2,
						'label' => Mage::helper('storepickup')->__('No'),
					),
				),
			));

			$fieldset->addField($day . '_time_interval', 'select', array(
				'label' => Mage::helper('storepickup')->__('Time Interval'),
				'required' => false,
				'class' => 'time_interval',
				'name' => $day . '_time_interval',
				'values' => $timeInterval,
				'note' => Mage::helper('storepickup')->__('Set interval between 2 Shipping Time options'),
			));

			$field = array('name' => $day . '_open',
				'data' => isset($data[$day . '_open']) ? $data[$day . '_open'] : '',
				'type' => 'open',
			);

			$fieldset->addField($day . '_open', 'note', array(
				'label' => Mage::helper('storepickup')->__('Opening Time'),
				'name' => $day . '_open',
				'text' => $this->getLayout()->createBlock('storepickup/adminhtml_time')->setData('field', $field)->setTemplate('storepickup/time.phtml')->toHtml(),
			));

			/*Edit by Son*/
			$field = array('name' => $day . '_open_break',
				'data' => isset($data[$day . '_open_break']) ? $data[$day . '_open_break'] : '',
				'type' => 'open_break',
			);
			$fieldset->addField($day . '_open_break', 'note', array(
				'label' => Mage::helper('storepickup')->__('Lunch break starts at'),
				'name' => $day . '_open_break',
				'text' => $this->getLayout()->createBlock('storepickup/adminhtml_time')->setData('field', $field)->setTemplate('storepickup/time.phtml')->toHtml(),
			));

			$field = array('name' => $day . '_close_break',
				'data' => isset($data[$day . '_close_break']) ? $data[$day . '_close_break'] : '',
				'type' => 'close_break',
			);
			$fieldset->addField($day . '_close_break', 'note', array(
				'label' => Mage::helper('storepickup')->__('Lunch break ends at'),
				'name' => $day . '_close_break',
				'text' => $this->getLayout()->createBlock('storepickup/adminhtml_time')->setData('field', $field)->setTemplate('storepickup/time.phtml')->toHtml(),
			));
			/*End by Son*/
			$field = array('name' => $day . '_close',
				'data' => isset($data[$day . '_close']) ? $data[$day . '_close'] : '',
				'type' => 'close',
			);
			$fieldset->addField($day . '_close', 'note', array(
				'label' => Mage::helper('storepickup')->__('Closing Time'),
				'name' => $day . '_close',
				'text' => $this->getLayout()->createBlock('storepickup/adminhtml_time')->setData('field', $field)->setTemplate('storepickup/time.phtml')->toHtml(),
			));

			/*
		$fieldset->addField($day.'_available_slot', 'text', array(
		'label'     => Mage::helper('storepickup')->__('Available Per Slot'),
		'required'  => false,
		'name'      => $day.'_available_slot',
		)); */
		}

		if (Mage::getSingleton('adminhtml/session')->getStoreData()) {
			$form->setValues(Mage::getSingleton('adminhtml/session')->getStoreData());
			Mage::getSingleton('adminhtml/session')->setStoreData(null);
		} elseif (Mage::registry('store_data')) {
			$form->setValues(Mage::registry('store_data')->getData());
		}
		return parent::_prepareForm();
	}

}