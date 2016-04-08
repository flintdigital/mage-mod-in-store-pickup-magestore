<?php

class Magestore_Storepickup_Adminhtml_Storepickup_MessageController extends Mage_Adminhtml_Controller_Action
{	       
    
    public function  backAction (){
        $id = $this->getRequest()->getParam('id');  
        $model = Mage::getModel('storepickup/message');
        $idd = $model->load($id)->getData('store_id');
        $this->_redirect('storepickup/adminhtml_store/edit', array('id' => $idd));
    }
    public function editmessageAction (){
        if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){return;}
        $id     = $this->getRequest()->getParam('id');            
        $model  = Mage::getModel('storepickup/message')->load($id);             
        if ($model->getId() || $id == 0) {

                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                    // var_dump($id);die();
                if (!empty($data)) {
                        $model->setData($data);
                }

                Mage::register('store_data', $model);

                $this->loadLayout();			

                $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

                $this->_addContent($this->getLayout()->createBlock('storepickup/adminhtml_message_edit'))
                        ->_addLeft($this->getLayout()->createBlock('storepickup/adminhtml_message_edit_tabs'));

                $this->renderLayout();
        } else {

                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storepickup')->__('Store does not exist'));
                $this->_redirect('storepickup/adminhtml_store');
        }
    }

    public function deleteAction() {
        $model = Mage::getModel('storepickup/message');
            $idd = $model->load($this->getRequest()->getParam('id'))->getData('store_id');//var_dump($idd);die();
            if( $this->getRequest()->getParam('id') > 0 ) {
                    try {

                            $model->setId($this->getRequest()->getParam('id'))
                                    ->delete();								
                            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Message was successfully deleted'));
                            $this->_redirect('storepickup/adminhtml_store/edit', array('id' => $idd));
                    } catch (Exception $e) {
                            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                            $this->_redirect('storepickup/adminhtml_store/edit', array('id' => $this->getRequest()->getParam('id')));
                    }
            }
            $this->_redirect('storepickup/adminhtml_store/edit', array('id' => $idd));
    }
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('storepickup');
    }
}