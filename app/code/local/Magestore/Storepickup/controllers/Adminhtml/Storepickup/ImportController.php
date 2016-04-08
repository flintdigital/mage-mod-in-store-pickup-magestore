<?php

class Magestore_Storepickup_Adminhtml_Storepickup_ImportController extends Mage_Adminhtml_Controller_Action {

	protected function _initAction() {
		$this->loadLayout()
		     ->_setActiveMenu('storepickup/stores')
		     ->_addBreadcrumb(Mage::helper('adminhtml')->__('Import Stores'), Mage::helper('adminhtml')->__('Import Stores'));

		return $this;
	}

	public function importstoreAction() {
		if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
			return;
		}
		$this->loadLayout();
		$this->_setActiveMenu('storepickup/stores');

		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import Stores'), Mage::helper('adminhtml')->__('Import Stores'));
		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import Stores'), Mage::helper('adminhtml')->__('Import Stores'));

		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

		$editBlock = $this->getLayout()->createBlock('storepickup/adminhtml_store_edit');
		$editBlock->removeButton('delete');
		$editBlock->removeButton('saveandcontinue');
		$editBlock->removeButton('reset');
		$editBlock->updateButton('back', 'onclick', 'backEdit()');
		$editBlock->setData('form_action_url', $this->getUrl('*/*/save', array()));

		$this->_addContent($editBlock)
		     ->_addLeft($this->getLayout()->createBlock('storepickup/adminhtml_store_import_tabs'));

		$this->renderLayout();
	}

	public function saveAction() {
		if (!isset($_FILES['csv_store'])) {
			Mage::getSingleton('core/session')->addError('Not selected file!');
			$this->_redirect('*/*/importstore');
			return;
		}
		if ($_FILES['csv_store']['type'] != 'application/vnd.ms-excel' && $_FILES['csv_store']['type'] != 'text/csv') {
			Mage::getSingleton('core/session')->addError('The file error!');
			$this->_redirect('*/*/importstore');
			return;
		}

		$oFile = new Varien_File_Csv();
		$data = $oFile->getData($_FILES['csv_store']['tmp_name']);
		$store = Mage::getModel('storepickup/store');
		$storeData = array();

		try {
			$total = 0;
			$test = true;
			foreach ($data as $col => $row) {
				$test = FALSE;
				if ($col == 0) {
					$index_row = $row;
				} else {

					for ($i = 0; $i < count($row); $i++) {
						$storeData[$index_row[$i]] = $row[$i];
					}
					if ($storeData['monday_status'] == 0 || !$storeData['monday_status']) {
						$storeData['monday_status'] = 1;
					}

					if ($storeData['tuesday_status'] == 0 || !$storeData['tuesday_status']) {
						$storeData['tuesday_status'] = 1;
					}

					if ($storeData['wednesday_status'] == 0 || !$storeData['wednesday_status']) {
						$storeData['wednesday_status'] = 1;
					}

					if ($storeData['thursday_status'] == 0 || !$storeData['thursday_status']) {
						$storeData['thursday_status'] = 1;
					}

					if ($storeData['friday_status'] == 0 || !$storeData['friday_status']) {
						$storeData['friday_status'] = 1;
					}

					if ($storeData['saturday_status'] == 0 || !$storeData['saturday_status']) {
						$storeData['saturday_status'] = 1;
					}

					if ($storeData['sunday_status'] == 0 || !$storeData['sunday_status']) {
						$storeData['sunday_status'] = 1;
					}

					if ($storeData['monday_time_interval'] == 0 || !$storeData['monday_time_interval']) {
						$storeData['monday_time_interval'] = 15;
					}

					if ($storeData['tuesday_time_interval'] == 0 || !$storeData['tuesday_time_interval']) {
						$storeData['tuesday_time_interval'] = 15;
					}

					if ($storeData['wednesday_time_interval'] == 0 || !$storeData['wednesday_time_interval']) {
						$storeData['wednesday_time_interval'] = 15;
					}

					if ($storeData['thursday_time_interval'] == 0 || !$storeData['thursday_time_interval']) {
						$storeData['thursday_time_interval'] = 15;
					}

					if ($storeData['friday_time_interval'] == 0 || !$storeData['friday_time_interval']) {
						$storeData['friday_time_interval'] = 15;
					}

					if ($storeData['saturday_time_interval'] == 0 || !$storeData['saturday_time_interval']) {
						$storeData['saturday_time_interval'] = 15;
					}

					if ($storeData['sunday_time_interval'] == 0 || !$storeData['sunday_time_interval']) {
						$storeData['sunday_time_interval'] = 15;
					}

					if ($storeData['store_name'] && $storeData['address'] && $storeData['country']) {
						$store->setData($storeData);
						$store->setId(null);
						if ($store->import()) {
							$total++;
						}

					}
				}
			}
			//Edit by Tien
//			Mage::helper('storepickup')->exportAllStoreMultiViewToJson();
			//End by Tien
			$this->_redirect('*/storepickup_store/index');
			if ($total != 0) {
				Mage::getSingleton('core/session')->addSuccess('Imported successful total ' . $total . ' stores');
			} else {
				Mage::getSingleton('core/session')->addSuccess('No store imported');
			}

		} catch (Exception $e) {
			Mage::getSingleton('core/session')->addError($e->getMessage());
			$this->_redirect('*/*/importstore');
		}
	}
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('storepickup');
    }
}
