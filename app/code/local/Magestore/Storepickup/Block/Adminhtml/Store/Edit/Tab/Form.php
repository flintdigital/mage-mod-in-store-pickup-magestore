<?php

class Magestore_Storepickup_Block_Adminhtml_Store_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

	protected function _prepareForm() {

		$form = new Varien_Data_Form();

		$dataObj = new Varien_Object(array(
			'store_id' => '',
			'store_name_in_store' => '',
			'status_in_store' => '',
			'description_in_store' => '',
			'address_in_store' => '',
			'city_in_store' => '',
		));

		if (Mage::getSingleton('adminhtml/session')->getStoreData()) {
			$data = Mage::getSingleton('adminhtml/session')->getStoreData();
			Mage::getSingleton('adminhtml/session')->setStoreData(null);
		} elseif (Mage::registry('store_data')) {
			$data = Mage::registry('store_data')->getData();
		}
		if (isset($data)) {
			$dataObj->addData($data);
		}

		$data = $dataObj->getData();

		$this->setForm($form);
		$fieldset = $form->addFieldset('store_form', array('legend' => Mage::helper('storepickup')->__('Store Information')));

		$inStore = $this->getRequest()->getParam('store');
		$defaultLabel = Mage::helper('storepickup')->__('Use Default');
		$defaultTitle = Mage::helper('storepickup')->__('-- Please Select --');
		$scopeLabel = Mage::helper('storepickup')->__('STORE VIEW');

		$fieldset->addField('store_name', 'text', array(
			'label' => Mage::helper('storepickup')->__('Store Name'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'store_name',
			'disabled' => ($inStore && !$data['store_name_in_store']),
			'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="store_name_default" name="store_name_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['store_name_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="store_name_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
          </td><td class="scope-label">
			[' . $scopeLabel . ']
          ' : '</td><td class="scope-label">
			[' . $scopeLabel . ']',
		));
		$symbolCurrency = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
		$fieldset->addField('shipping_price', 'text', array(
			'label' => Mage::helper('storepickup')->__('Shipping Fee' . " ($symbolCurrency)"),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'shipping_price',
		));

		$fieldset->addField('status', 'select', array(
			'label' => Mage::helper('storepickup')->__('Status'),
			'name' => 'store_status',
			'values' => array(
				array(
					'value' => 1,
					'label' => Mage::helper('storepickup')->__('Enabled'),
				),
				array(
					'value' => 2,
					'label' => Mage::helper('storepickup')->__('Disabled'),
				),
			),
			'disabled' => ($inStore && !$data['status_in_store']),
			'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="status_default" name="status_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['status_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="status_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
          </td><td class="scope-label">
			[' . $scopeLabel . ']
          ' : '</td><td class="scope-label">
			[' . $scopeLabel . ']',
		));

		$fieldset->addField('address', 'text', array(
			'label' => Mage::helper('storepickup')->__('Address'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'address',
			'disabled' => ($inStore && !$data['address_in_store']),
			'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="address_default" name="address_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['address_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="address_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
          </td><td class="scope-label">
			[' . $scopeLabel . ']
          ' : '</td><td class="scope-label">
			[' . $scopeLabel . ']',
		));

		$fieldset->addField('city', 'text', array(
			'label' => Mage::helper('storepickup')->__('City'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'city',
			'disabled' => ($inStore && !$data['city_in_store']),
			'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="city_default" name="city_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['city_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="city_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
          </td><td class="scope-label">
			[' . $scopeLabel . ']
          ' : '</td><td class="scope-label">
			[' . $scopeLabel . ']',
		));

		$fieldset->addField('country', 'select', array(
			'label' => Mage::helper('storepickup')->__('Country'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'country',
			'values' => Mage::helper('storepickup/location')->getOptionCountry(),
		));

		$fieldset->addField('stateEl', 'note', array(
			'label' => Mage::helper('storepickup')->__('State/Province'),
			'name' => 'stateEl',
			'text' => $this->getLayout()->createBlock('storepickup/adminhtml_region')->setTemplate('storepickup/region.phtml')->toHtml(),
		));

		$fieldset->addField('zipcode', 'text', array(
			'label' => Mage::helper('storepickup')->__('Zip Code'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'zipcode',
		));
		$fieldset->addField('tag_ids', 'multiselect', array(
			'label' => Mage::helper('storepickup')->__('Tags'),
			'class' => '',
			'required' => FALSE,
			'name' => 'tag_ids',
			'values' => Mage::getModel('storepickup/tag')->getOptionCountry(),
		));

//        $fieldset->addField('shipping_fee', 'text', array(
		//            'label' => Mage::helper('storepickup')->__('Shipping Fee'),
		////            'class' => 'required-entry',
		//            'required' => FALSE,
		//            'name' => 'shipping_fee',
		//            'note' => Mage::helper('storepickup')->__('free shipping if this field empty'),
		//        ));

//        $fieldset->addField('store_latitude', 'text', array(
		//            'label' => Mage::helper('storepickup')->__('Store Latitude'),
		//            'name' => 'store_latitude',
		//        ));
		//
		//        $fieldset->addField('store_longitude', 'text', array(
		//            'label' => Mage::helper('storepickup')->__('Store Longitude'),
		//            'name' => 'store_longitude',
		//        ));

//        if (!$this->getRequest()->getParam('id')) {
		//            $data['pin_color'] = 'f75448';
		//            $fieldset->addField('pin_color', 'text', array(
		//                'label' => Mage::helper('storepickup')->__('Pin Color'),
		//                'name' => 'pin_color',
		//                'note' => Mage::helper('storepickup')->__('Set color for store’s pin shown on map'),
		//                'after_element_html' => '<script>loadColor_storepickup("click", "0")</script>'
		//            ));
		//        }
		$wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array('add_variables' => false, 'add_widgets' => false, 'add_images' => false, 'files_browser_window_url' => $this->getBaseUrl() . 'admin/cms_wysiwyg_images/index/'));
		$fieldset->addField('description', 'editor', array(
			'name' => 'description',
			'label' => Mage::helper('storepickup')->__('Description'),
			'title' => Mage::helper('storepickup')->__('Description'),
			'style' => 'height:150px;',
			'wysiwyg' => true,
			'required' => false,
			'config' => $wysiwygConfig,
			'disabled' => ($inStore && !$data['description_in_store']),
			'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="description_default" name="description_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['description_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="description_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
          </td><td class="scope-label">
			[' . $scopeLabel . ']
          ' : '</td><td class="scope-label">
			[' . $scopeLabel . ']',
		));
		/* Edit by Son*/
		// Store contact form
		$fieldset = $form->addFieldset('store_contact_form', array('legend' => Mage::helper('storepickup')->__('Contact Information')));
		$fieldset->addField('store_manager', 'text', array(
			'label' => Mage::helper('storepickup')->__('Store Manager'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'store_manager',
		));

		$fieldset->addField('store_phone', 'text', array(
			'label' => Mage::helper('storepickup')->__('Phone Number'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'store_phone',
		));

		$fieldset->addField('store_email', 'text', array(
			'label' => Mage::helper('storepickup')->__('Email Address'),
			'class' => 'required-entry validate-email',
			'required' => true,
			'name' => 'store_email',
		));

		$fieldset->addField('store_fax', 'text', array(
			'label' => Mage::helper('storepickup')->__('Fax Number'),
			'name' => 'store_fax',
		));

		$fieldset->addField('status_order', 'select', array(
			'label' => Mage::helper('storepickup')->__('Receive email when order status is changed'),
			'name' => 'status_order',
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

//        $fieldset->addField('image_id', 'text', array(
		//            'label' => Mage::helper('storepickup')->__('Store Image(s)'),
		//            'name' => 'image_id',
		//            'value' => Mage::helper('storepickup')->getDataImage($this->getRequest()->getParam('id')),
		//        ))->setRenderer($this->getLayout()->createBlock('storepickup/adminhtml_grid_renderer_button'));
		/* Edit by Tien */
//          $fieldset->addField('image_id', 'note', array(
		//            'label' => Mage::helper('storepickup')->__('Store Image(s)'),
		//            'name' => 'image_id',
		//            'text' => $this->getLayout()->createBlock('storepickup/adminhtml_gallery_content')->setTemplate('storepickup/helper/gallery.phtml')->toHtml(),
		//            'after_element_html' => '<style>.columns .form-list td.value{width:300px !important}</style>'
		//        ));

		$fieldset->addField('image', 'note', array(
			'label' => Mage::helper('storepickup')->__('Store Image(s)'),
			'name' => 'image',
			'text' => $this->getLayout()->createBlock('storepickup/adminhtml_gallery_content')->setTemplate('storepickup/helper/gallery.phtml')->toHtml(),
			// 'after_element_html' => '<style>.columns .form-list td.value{width:300px !important}</style>',
		));
		/* End by Tien */
		//Google map
		$fieldset = $form->addFieldset('google_map', array('legend' => Mage::helper('storepickup')->__('Google Map')));
		$fieldset->addField('zoom_level', 'text', array(
			'label' => Mage::helper('storepickup')->__('Zoom Level'),
			'name' => 'zoom_level',
//            'value' => 12
		));
		$fieldset->addField('store_latitude', 'text', array(
			'label' => Mage::helper('storepickup')->__('Store Latitude'),
			'name' => 'store_latitude',
			'class' => 'required-entry',
			'required' => true,
			// 'value' => $data['store_latitude']
		));
		$fieldset->addField('store_longitude', 'text', array(
			'label' => Mage::helper('storepickup')->__('Store Longitude'),
			'name' => 'store_longitude',
			'class' => 'required-entry',
			'required' => true,
			//'value' => $data['store_longitude']
		));
		if (!$this->getRequest()->getParam('id')) {
			$data['pin_color'] = 'f75448';
			$fieldset->addField('pin_color', 'text', array(
				'label' => Mage::helper('storepickup')->__('Pin Color'),
				'name' => 'pin_color',
				'note' => Mage::helper('storepickup')->__('Set color for store’s pin shown on map'),
				'after_element_html' => '<script>loadColor_storepickup("click", "0")</script>',
			));
		} else {
			$fieldset->addField('pin_color', 'text', array(
				'label' => Mage::helper('storepickup')->__('Pin Color'),
				'name' => 'pin_color',
				'value' => $data['pin_color'],
				'note' => Mage::helper('storepickup')->__('Set color for store’s pin shown on map'),

			));
		}

		if (isset($data['image_icon']) && $data['image_icon']) {
			$data['image_icon'] = 'storepickup/images/icon/' . $data['store_id'] . '/' . $data['image_icon'];
		}
		$fieldset->addField('image_icon', 'image', array(
			'label' => Mage::helper('storepickup')->__('Store Icon'),
			'note' => 'Shown on Google Map<br/>Recommended size: 50x60 px. Supported format: jpeg, png, gif',
			'name' => 'image_icon',
		));
		$fieldset->addField('gmap', 'text', array(
			'label' => Mage::helper('storepickup')->__('Store Map'),
			'name' => 'gmap',
		))->setRenderer($this->getLayout()->createBlock('storepickup/adminhtml_gmap'));
		/*End by Son*/
		if (Mage::getSingleton('adminhtml/session')->getStoreData()) {
			$form->setValues(Mage::getSingleton('adminhtml/session')->getStoreData());
			Mage::getSingleton('adminhtml/session')->setStoreData(null);
		} elseif (Mage::registry('store_data')) {
			$form->setValues($data);
		}

		parent::_prepareForm();
	}

}