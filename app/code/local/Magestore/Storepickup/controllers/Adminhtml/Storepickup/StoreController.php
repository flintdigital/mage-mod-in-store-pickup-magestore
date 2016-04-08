<?php

class Magestore_Storepickup_Adminhtml_Storepickup_StoreController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
             ->_setActiveMenu('storepickup/stores')
             ->_addBreadcrumb(Mage::helper('adminhtml')->__('Stores Manager'), Mage::helper('adminhtml')->__('Stores Manager'));

        return $this;
    }

    public function indexAction()
    {
        if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
            return;
        }
        $this->_initAction()
             ->renderLayout();
    }

    public function relatedordersAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('storepickup.edit.tab.relatedorders')
             ->setOrders($this->getRequest()->getPost('relatedorders', null));
        $this->renderLayout();
    }

    public function relatedordersgridAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('storepickup.edit.tab.relatedorders')
             ->setOrders($this->getRequest()->getPost('relatedorders', null));
        $this->renderLayout();
    }

    public function messageAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('storepickup.edit.tab.message')
             ->setMessages($this->getRequest()->getPost('message', null));
        $this->renderLayout();
    }

    public function messagegridAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('storepickup.edit.tab.message')
             ->setMessages($this->getRequest()->getPost('message', null));
        $this->renderLayout();
    }

    public function editAction()
    {
        if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
            return;
        }
        $id = $this->getRequest()->getParam('id');
        $store = $this->getRequest()->getParam('store');
        $model = Mage::getModel('storepickup/store')->setStoreId($store)->load($id);

        if ($model->getId() || $id == 0) {

            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('store_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('storepickup/stores');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Store Manager'), Mage::helper('adminhtml')->__('Store Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Store News'), Mage::helper('adminhtml')->__('Store News'));
            /* Edit by Son */
            if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
                $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
            }
            /* Edit by Son */
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->getLayout()->getBlock('head')->addJs('magestore/storepickup/colorpicker/prototype_colorpicker.js');
            $this->getLayout()->getBlock('head')->addJs('magestore/storepickup/colorpicker/config.js');
            $this->getLayout()->getBlock('head')->addCss('css/magestore/storepickup/prototype_colorpicker.css');

            $this->_addContent($this->getLayout()->createBlock('storepickup/adminhtml_store_edit'))
                 ->_addLeft($this->getLayout()->createBlock('storepickup/adminhtml_store_edit_tabs'));

            $this->renderLayout();
        } else {

            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storepickup')->__('Store does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {

        $this->_forward('edit');
    }

    public function saveAction()
    {

        $store = $this->getRequest()->getParam('store');
        if ($data = $this->getRequest()->getPost()) {
            $id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('storepickup/store');

            //setStateValue
            if (isset($data['state_id'])) {
                $state = Mage::getModel('directory/region')->load($data['state_id']);
                $data['state'] = $state->getName();
            }
            if (isset($data['store_status'])) {
                $data['status'] = $data['store_status'];
            }
            foreach (array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') as $day) {
                $data[$day . '_open'] = implode(':', $data[$day . '_open']);
                $data[$day . '_close'] = implode(':', $data[$day . '_close']);
                /*Edit by Son*/
                $data[$day . '_open_break'] = implode(':', $data[$day . '_open_break']);
                $data[$day . '_close_break'] = implode(':', $data[$day . '_close_break']);
                /*End by Son*/
            }
            if (isset($data['store_name'])) {
                $storeName = strtolower(trim($data['store_name'], ' '));
                $storeName = Mage::helper('storepickup/url')->characterSpecial($storeName);
            }
//            var_dump(Mage::app()->getStore()->getStoreId());
            //            die();
            $check = 1;
            $storeID = $model->load($id);
            if (!$storeID->getId()) {
                while ($check == 1) {
                    $stores = Mage::getModel('storepickup/store')->getCollection()
                                                                 ->addFieldToFilter('url_id_path', $storeName)
                                                                 ->getFirstItem();

                    if ($stores->getId()) {
                        $storeName = $storeName . '-1';
                    } else {
                        $check = 0;
                    }
                    $data['url_id_path'] = $storeName;
                }
            } else {
                $data['url_id_path'] = $storeID->getUrlIdPath();
            }
            if (isset($data['tag_ids'])) {
                $data['tag_ids'] = implode(',', $data['tag_ids']);
            }

            /*Edit by Son*/
            if (isset($data['image_icon'])) {
                if (isset($data['image_icon']['delete'])) {
                    //$deleteIcon = 1;
                    $data['image_icon'] = '';
                } else {
                    $imageData = explode('/', $data['image_icon']['value']);

                    $data['image_icon'] = $imageData['4'];

                    $vntext = Mage::helper('storepickup')->vn_str_filter($data['image_icon']);
                    $data['image_icon'] = preg_replace('([^a-zA-Z0-9-\.])', '_', $vntext);
                }
            }
            if (isset($_FILES['image_icon']) && $_FILES['image_icon']['name']) {

                $data['image_icon'] = $_FILES['image_icon']['name'];

                $vntext = Mage::helper('storepickup')->vn_str_filter($data['image_icon']);
                $data['image_icon'] = preg_replace('([^a-zA-Z0-9-\.])', '_', $vntext);
                $data['image_icon'] = 'icon_' . $data['image_icon'];
//                ZEND_DEBUG::dump($data['image_icon']);
                //                 die();
            }
            /* End by Son*/
            $model->setData($data)
                  ->setStoreId($store)
                  ->setId($id);
            //Mage::helper('storepickup')->SaveImage($data['images'],$id, $_FILES);
            try {

                $model->save();

                //$allStores = Mage::app()->getStores();
                //                $storeId=Mage::app()->getStore()->getStoreId();
                //                $url_suffix = Mage::getStoreConfig('catalog/seo/product_url_suffix', Mage::app()->getStore()->getStoreId());
                //               // foreach ($allStores as $_eachStoreId => $val) {
                //                    $rewrite = Mage::getModel('core/url_rewrite')->getCollection()
                //                                    ->addFieldToFilter('id_path', $data['url_id_path'])
                //                                    ->addFieldToFilter('store_id',$storeId)
                //                                    ->getFirstItem();
                //
                //                    if (!$rewrite->getId()) {
                //                        $rewrite->setStoreId($storeId)
                //                                ->setData('is_system', 0)
                //                                ->setIdPath($data['url_id_path'])
                //                                ->setRequestPath('storepickup/' . $data['url_id_path'] . $url_suffix)
                //                                ->setTargetPath('storepickup/index/index/viewstore/' . $model->getId());
                //                    }
                //                   $rewrite->save();
                // }
                /*Edit by Son*/
                $stores = Mage::app()->getStores();
                foreach ($stores as $store) {
                    $model->setStoreId($store->getStoreId())
                          ->updateUrlKey();
                }

                /*End by Son*/

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('storepickup')->__('Store was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                /* Edit by Tien */

                Mage::helper('storepickup/image')->saveImageStore($data['storepickup_images'], $data['storepickup_values'], $model->getId());
                /* End by Tien */

                /*Edit by Son*/
                if ($id == null) {

                    if (isset($_FILES['image_icon']) && $_FILES['image_icon']['name']) {
                        Mage::helper('storepickup')->saveIcon($_FILES['image_icon'], $model->getCollection()->getLastItem()->getId());
                    }
                } else {
                    if (isset($_FILES['image_icon']) && $_FILES['image_icon']['name']) {
                        Mage::helper('storepickup')->saveIcon($_FILES['image_icon'], $id);

                    }
                }
                /*End by Son*/
                //Edit by Tien
//                Mage::helper('storepickup')->exportAllStoreMultiViewToJson();

                //End by Tien
                //                if (isset($data['radio']))
                //                    Mage::helper('storepickup')->setImageBig($data['radio'], $id);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), 'store' => $store));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storepickup')->__('Unable to find store to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('storepickup/store');

//
                $url_suffix = Mage::getStoreConfig('catalog/seo/product_url_suffix', Mage::app()->getStore()->getStoreId());
                $storeUrlIdPath = $model->load($this->getRequest()->getParam('id'))->getUrlIdPath();
//                $rewrites = Mage::getModel('core/url_rewrite')->getCollection()
                //                        ->addFieldToFilter('id_path', $storeUrlIdPath);
                //
                //                foreach ($rewrites as $rewrite)
                //                    $rewrite->delete();
                /*Edit by Son */
                $allStores = Mage::app()->getStores();
                foreach ($allStores as $_eachStoreId => $val) {
                    // $idPath = 'storepickup/' . $store->getStoreId() . '/' . $this->getRequest()->getParam('id');

                    $rewrite = Mage::getModel('core/url_rewrite')->getCollection()
                                                                 ->addFieldToFilter('id_path', $storeUrlIdPath)
                                                                 ->addFieldToFilter('store_id', $_eachStoreId)
                                                                 ->getFirstItem();

                    /*End by Son*/
                    $rewrite->delete();
                    //var_dump($rewrite);die();
                }

                $model->setId($this->getRequest()->getParam('id'))
                      ->delete();

                $holidays = Mage::getModel('storepickup/holiday')->getCollection()
                                                                 ->addFieldToFilter('store_id', $this->getRequest()->getParam('id'));
                foreach ($holidays as $holiday) {
                    $holiday->delete();
                }

                //Edit by Tien
//                Mage::helper('storepickup')->exportAllStoreMultiViewToJson();
                //End by Tien

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Store was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function deletemessageAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('storepickup/store');
                $model->setId($this->getRequest()->getParam('id'))
                      ->delete();

                $holidays = Mage::getModel('storepickup/holiday')->getCollection()
                                                                 ->addFieldToFilter('store_id', $this->getRequest()->getParam('id'));
                foreach ($holidays as $holiday) {
                    $holiday->delete();
                }
                //Edit by Tien
                //                Mage::helper('storepickup')->exportAllStoreToJson($this->getRequest()->getParam('store'));
                //End by Tien
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Store was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $storeIds = $this->getRequest()->getParam('storepickup');
        if (!is_array($storeIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($storeIds as $storeId) {
                    $store = Mage::getModel('storepickup/store')->load($storeId);

                    $storeUrlIdPath = $store->getUrlIdPath();
                    $rewrites = Mage::getModel('core/url_rewrite')->getCollection()
                                                                  ->addFieldToFilter('id_path', $storeUrlIdPath);

                    foreach ($rewrites as $rewrite) {
                        $rewrite->delete();
                    }

                    $store->delete();

                    $holidays = Mage::getModel('storepickup/holiday')->getCollection()
                                                                     ->addFieldToFilter('store_id', $storeId);
                    foreach ($holidays as $holiday) {
                        $holiday->delete();
                    }
                }
                //Edit by Tien
//                Mage::helper('storepickup')->exportAllStoreMultiViewToJson();
                //End by Tien
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($storeIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function messageDeleteAction()
    {

    }

    public function massStatusAction()
    {
        $storepickupIds = $this->getRequest()->getParam('storepickup');
        $storeViewId = $this->getRequest()->getParam('store');
        if (!is_array($storepickupIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($storepickupIds as $storepickupId) {
                    $storepickup = Mage::getSingleton('storepickup/store')
                        ->load($storepickupId)
                        ->setStoreId($storeViewId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                //Edit by Tien

//                Mage::helper('storepickup')->exportAllStoreMultiViewToJson();
                //End by Tien
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($storepickupIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName = 'store.csv';
        //$content    = $this->getLayout()->createBlock('storepickup/adminhtml_store_grid')
        //    ->getCsv();

        $content = Mage::getModel('storepickup/exporter')
            ->exportStore();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName = 'store.xml';
        // $content    = $this->getLayout()->createBlock('storepickup/adminhtml_store_grid')
        //     ->getXml();

        $content = Mage::getModel('storepickup/exporter')
            ->getXmlStore();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream')
    {
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

    /**
     * Store grid for AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('storepickup/adminhtml_store_grid')->toHtml()
        );
    }
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('storepickup');
    }
}
