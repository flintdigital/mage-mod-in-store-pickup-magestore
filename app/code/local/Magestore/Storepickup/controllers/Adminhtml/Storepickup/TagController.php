<?php

class Magestore_Storepickup_Adminhtml_Storepickup_TagController extends Mage_Adminhtml_Controller_Action {
	protected function _initAction() {
		$this->loadLayout()
		     ->_setActiveMenu('storepickup/tag')
		     ->_addBreadcrumb(Mage::helper('adminhtml')->__('Tags Manager'), Mage::helper('adminhtml')->__('Tags Manager'));
		return $this;
	}

	public function indexAction() {
		$this->_initAction()
		     ->renderLayout();
	}

	public function editAction() {
		$id = $this->getRequest()->getParam('id');
		$model = Mage::getModel('storepickup/tag')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('tag_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('storepickup/tag');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Tags Manager'), Mage::helper('adminhtml')->__('Tags Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Tag News'), Mage::helper('adminhtml')->__('Tag News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('storepickup/adminhtml_tag_edit'))
			     ->_addLeft($this->getLayout()->createBlock('storepickup/adminhtml_tag_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storepickup')->__('Tag does not exist'));
			$this->_redirect('*/*/');
		}
	}

	public function newAction() {
		$this->_forward('edit');
	}

	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
           
			if (isset($data['title']) && $data['title'] != '') {
              
				try {
					/* Starting upload */
                     
					$uploader = new Varien_File_Uploader('img_icon');
                   
					// Any extention would work
					$uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
					$uploader->setAllowRenameFiles(false);

					// Set the file upload mode
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
                     
					// We set media as the upload dir
                    $tagicon='';
					$path = Mage::getBaseDir('media') . DS . 'storepickup' . DS . 'images' . DS . 'icon';
                    
                    $vntext=Mage::helper('storepickup')->vn_str_filter($data['title']);
                    $tagicon = preg_replace('([^a-zA-Z0-9-\.])', '_', $vntext);
                    if($_FILES["img_icon"]["type"]=="image/jpeg"){
                        $tagicon=$tagicon.'_icon.jpg';
                    }else if($_FILES["img_icon"]["type"]=="image/gif"){
                        $tagicon=$tagicon.'_icon.gif';
                    }else if($_FILES["img_icon"]["type"]=="image/png"){
                        $tagicon=$tagicon.'_icon.png';
                    }
                   // var_dump($b);die();
					$uploader->save($path, $tagicon);

				} catch (Exception $e) {}
				//this way the name is saved in DB
             
				$data['icon'] = $tagicon;
               
               // var_dump($data['icon']);die();
			}
			if (isset($data['img_icon']['delete'])) {
				$data['icon'] = '';
			}
			$model = Mage::getModel('storepickup/tag');
			$model->setData($data)
			      ->setId($this->getRequest()->getParam('id'));

			try {

				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('storepickup')->__('Tag was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storepickup')->__('Unable to find tag to save'));
		$this->_redirect('*/*/');
	}

	public function deleteAction() {
		if ($this->getRequest()->getParam('id') > 0) {
			try {
				$model = Mage::getModel('storepickup/tag');
				$model->setId($this->getRequest()->getParam('id'))
				      ->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Tag was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	public function massDeleteAction() {
		$storepickupIds = $this->getRequest()->getParam('tag_id');
		if (!is_array($storepickupIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select tag(s)'));
		} else {
			try {
				foreach ($storepickupIds as $storepickupId) {
					$storepickup = Mage::getModel('storepickup/tag')->load($storepickupId);
					$storepickup->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($storepickupIds)));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

	public function massStatusAction() {
		$storepickupIds = $this->getRequest()->getParam('tag_id');
		if (!is_array($storepickupIds)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select tag(s)'));
		} else {
			try {
				foreach ($storepickupIds as $storepickupId) {
					$storepickup = Mage::getSingleton('storepickup/tag')
						->load($storepickupId)
						->setStatus($this->getRequest()->getParam('status'))
						->setIsMassupdate(true)
						->save();
				}
				$this->_getSession()->addSuccess(
					$this->__('Total of %d record(s) were successfully updated', count($storepickupIds))
				);
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

	public function exportCsvAction() {
		$fileName = 'storepickup_tags.csv';
		$content = $this->getLayout()->createBlock('storepickup/adminhtml_tag_grid')->getCsv();
		$this->_prepareDownloadResponse($fileName, $content);
	}

	public function exportXmlAction() {
		$fileName = 'storepickup_tags.xml';
		$content = $this->getLayout()->createBlock('storepickup/adminhtml_tag_grid')->getXml();
		$this->_prepareDownloadResponse($fileName, $content);
	}
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('storepickup');
    }
}