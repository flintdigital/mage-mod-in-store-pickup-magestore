<?php

class Magestore_Storepickup_Adminhtml_Storepickup_GuideController extends Mage_Adminhtml_Controller_action
{

    public function indexAction() {            
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Store Pickup Guide'));
        $this->renderLayout();
    }
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('storepickup');
    }
}