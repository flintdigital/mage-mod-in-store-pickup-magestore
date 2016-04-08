<?php

class Magestore_Storepickup_Adminhtml_Storepickup_HolidayController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('storepickup/holidays')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Holiday Manager'), Mage::helper('adminhtml')->__('Holiday Manager'));

        return $this;
    }

    public function indexAction() {
        if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
            return;
        }
        $this->_initAction()
                ->renderLayout();
    }

    public function editAction() {
        if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
            return;
        }
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('storepickup/holiday')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('holiday_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('storepickup/holidays');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Holiday Manager'), Mage::helper('adminhtml')->__('Holiday Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Holiday News'), Mage::helper('adminhtml')->__('Holiday News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('storepickup/adminhtml_holiday_edit'))
                    ->_addLeft($this->getLayout()->createBlock('storepickup/adminhtml_holiday_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storepickup')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('storepickup/holiday');
            if($data['store_id']){    
                $data['store_id'] = implode(',',$data['store_id']);
               }
            if ($this->getRequest()->getParam('id')) {
                $holidays = $model->getCollection()
                        ->addFieldToFilter('holiday_id', array('nin' => $this->getRequest()->getParam('id')))
                        //->addFieldToFilter('store_id', $data['store_id'])
                        ->addFieldToFilter('date', $data['date'])
                        ->addFieldToFilter('holiday_date_to', $data['holiday_date_to']);
            } else {
                $holidays = $model->getCollection()
                        //->addFieldToFilter('store_id', $data['store_id'])
                        ->addFieldToFilter('date', $data['date'])
                        ->addFieldToFilter('holiday_date_to', $data['holiday_date_to']);
            }
            foreach ($holidays as $_h) {
                    if ($_h->getStoreId() == $data['store_id']) {
                            $flag = true;break;
                    }
            }
            $unwanted_array = array('ả'=>'a','ố'=>'o','ồ'=>'o','ổ'=>'o','ế'=>'e','ề'=>'e','ể'=>'e','ễ'=>'e',    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A','Ả'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C','Ẹ'=>'E', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E','Ễ'=>'E','Ệ'=>'E', 'Ë'=>'E','Ẻ'=>'E','Ẽ'=>'E','Ĩ'=>'I', 'Ì'=>'I','Ị'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I','Ỉ'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U','Ụ'=>'U','Ũ'=>'U', 'Ü'=>'U','Ủ'=>'U','Ỷ'=>'Y','Ỵ'=>'Y', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'a'=>'a','ả'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ệ'=>'e','ê'=>'e','ẻ'=>'e', 'ë'=>'e','ĩ'=>'i', 'ì'=>'i','ỉ'=>'i', 'í'=>'i','ị'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o','ỏ'=>'o', 'ô'=>'o', 'õ'=>'o','ọ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u','ụ'=>'u','ũ'=>'u', 'ú'=>'u','ủ'=>'u', 'û'=>'u','ỷ'=>'y', 'ý'=>'y','ỵ'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
           
            $data['holiday_name'] = strtr( $data['holiday_name'], $unwanted_array );
            
            
           // zend_debug::dump($data['holiday_name']);die();
            if (empty($flag)) {              
                    $model->addData($data)
                            ->setId($this->getRequest()->getParam('id'));
                try {
                    $model->save();
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('storepickup')->__('Item was successfully saved'));
                    Mage::getSingleton('adminhtml/session')->setFormData(false);
//                    Mage::helper('storepickup')->exportAllStoreToJson();
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
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storepickup')->__('Holiday exists'));
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storepickup')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('storepickup/holiday');

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();
//                Mage::helper('storepickup')->exportAllStoreToJson();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $storepickupIds = $this->getRequest()->getParam('holiday');
        if (!is_array($storepickupIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($storepickupIds as $storepickupId) {
                    $storepickup = Mage::getModel('storepickup/holiday')->load($storepickupId);
                    $storepickup->delete();
                }
//                Mage::helper('storepickup')->exportAllStoreToJson();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($storepickupIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction() {
        $fileName = 'holiday.csv';
        $content = $this->getLayout()->createBlock('storepickup/adminhtml_holiday_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'holiday.xml';
        $content = $this->getLayout()->createBlock('storepickup/adminhtml_holiday_grid')
                ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('storepickup');
    }
}
