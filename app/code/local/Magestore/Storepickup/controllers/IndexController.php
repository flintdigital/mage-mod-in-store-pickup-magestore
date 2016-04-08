<?php

class Magestore_Storepickup_IndexController extends Mage_Core_Controller_Front_Action
{

    protected function _getCoreSession()
    {
        return Mage::getSingleton('core/session');
    }

    public function indexAction()
    {
        if (!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)) {
            return;
        }

        $this->loadLayout();
        $this->getLayout()
             ->getBlock('head')
             ->setTitle(Mage::helper('core')->__('Our Stores'));
        $this->renderLayout();
    }

    public function changestoreAction()
    {
        $storeId = $this->getRequest()->getParam('store_id');
        $store = Mage::getModel('storepickup/store')->load($storeId);
        if ($store->getId()) {
            Mage::getSingleton('checkout/session')->setData('storepickup_shipping_price', $store->getShippingPrice());
            $response = array('shippingPrice' => Mage::helper('core')->currency($store->getShippingPrice(), true, false));
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        } else {
            Mage::getSingleton('checkout/session')->unsetData('storepickup_shipping_price');
        }
        $data = array('store_id' => $storeId);
        Mage::getSingleton('checkout/session')->setData('storepickup_session', $data);
    }

    public function changedateAction()
    {
        try {
            $shipping_date = $this->getRequest()->getParam('shipping_date');
            $store_id = $this->getRequest()->getParam('store_id');
            $date = new Zend_Date();
            $date->setLocale(Mage::app()->getLocale()->getLocaleCode());
            $shipping_date_format = $date->setDate($shipping_date);
            $storepickup = Mage::getSingleton('checkout/session')->getData('storepickup_session');
            $storepickup['store_id'] = $store_id;
            // $storepickup['date'] = Mage::helper('core')->formatDate($shipping_date_format, 'medium', false);
            $storepickup['date'] = $shipping_date;
            Mage::getSingleton('checkout/session')->setData('storepickup_session', $storepickup);
            $html_select = Mage::helper('storepickup')->getTimeSelectHTML($shipping_date, $store_id);
            $this->getResponse()->setBody($html_select);
        } catch (Exception $e) {
            Mage::getSingleton('checkout/session')->setData('myerror', $e->getMessage());
        }
    }

    public function changetimeAction()
    {
        $time = new Zend_Date();
        $time->setLocale(Mage::app()->getLocale()->getLocaleCode());
        $shipping_time = $this->getRequest()->getParam('shipping_time');
        $shippingtime = $time->setTime($shipping_time);
        $storepickup = Mage::getSingleton('checkout/session')->getData('storepickup_session');
        $storepickup['time'] = Mage::helper('core')->formatTime($shippingtime, 'medium', false);
        Mage::getSingleton('checkout/session')->setData('storepickup_session', $storepickup);
    }

    public function savecontactAction()
    {
        $id = $this->getRequest()->getParam('id');
        //var_dump($this->_redirect('*/*/index/'));die();
        $mod = Mage::getModel('storepickup/message');
        $coreSession = $this->_getCoreSession();
        $captchaCode = $coreSession->getData('captcha_code' . $this->getRequest()->getParam('id'));
        $data = $this->getRequest()->getPost();
        $data['store_id'] = $id;
        if ($captchaCode != $data['captcha']) {
            Mage::getSingleton('core/session')->setPickupFormData($data);
            $coreSession->addError(Mage::helper('storepickup')->__('Please enter correct verification code!'));
            return $this->_redirect('*/*/index/', array('viewstore' => $id));
        } else {
            Mage::getSingleton('core/session')->setPickupFormData(null);
        }
        $mod->setData($data);
        $time = new Zend_Date();
        $time->setLocale(Mage::app()->getLocale()->getLocaleCode());
        $datatime = now();
        $mod->setDateSent($datatime);
        $mod->save();
        Mage::helper('storepickup/email')->sendEmailtoAdmin($mod->getId());
        Mage::helper('storepickup/email')->sendEmailtoStoreOwner($mod->getId(), $id);
        Mage::getSingleton('core/session')->addSuccess(Mage::helper('storepickup')->__('Message has been sent to store owner successfully!'));
        $this->_redirect('*/*/index/', array('viewstore' => $id));
    }
    public function imagecaptchaAction()
    {
        require_once Mage::getBaseDir('lib') . DS . 'captcha' . DS . 'class.simplecaptcha.php';
        $config['BackgroundImage'] = Mage::getBaseDir('lib') . DS . 'captcha' . DS . "white.png";
        $config['BackgroundColor'] = "FF0000";
        $config['Height'] = 30;
        $config['Width'] = 100;
        $config['Font_Size'] = 23;
        $config['Font'] = Mage::getBaseDir('lib') . DS . 'captcha' . DS . "ARLRDBD.TTF";
        $config['TextMinimumAngle'] = 15;
        $config['TextMaximumAngle'] = 30;
        $config['TextColor'] = '2B519A';
        $config['TextLength'] = 4;
        $config['Transparency'] = 80;
        $captcha = new SimpleCaptcha($config);
        $this->_getCoreSession()->setData('captcha_code' . $this->getRequest()->getParam('id'), $captcha->Code);
    }

    public function refreshcaptchaAction()
    {
        $result = Mage::getModel('core/url')->getUrl('*/*/imageCaptcha', array(
            'id' => $this->getRequest()->getParam('id'),
            'time' => time(),
        ));
        echo $result;
    }

    public function disableDateAction()
    {
        $date = array();
        $comment = array();
        $holiday_date = array();
        $specialday_date = array();
        $storeId = $this->getRequest()->getParam('store_id');
        if ($storeId == '') {
            $closed = array(1, 2, 3, 4, 5, 6, 0);
        } else {
            $close_date = Mage::getModel('storepickup/store')->getCollection()
                                                             ->addFieldToFilter('store_id', $storeId)->getFirstItem();
            $specialdays = Mage::getModel('storepickup/specialday')->getCollection()
                                                                   ->addFieldToFilter('store_id', array('finset' => $storeId));

            foreach ($specialdays as $specialday) {
                $specialdayFrom = str_replace('-', '', $specialday->getDate());
                $specialdayTo = str_replace('-', '', $specialday->getSpecialdayDateTo());
                for ($j = (int) $specialdayFrom; $j <= (int) $specialdayTo; $j++) {
                    $specialday_date[] = $j;
                    //$comment[] = $specialday->getComment();
                }
            }
            $closed = array();
            if ($close_date->getMondayStatus() == 2 || ($close_date->getMondayOpen() == $close_date->getMondayClose())) {
                $closed[] = 1;
            }
            if ($close_date->getTuesdayStatus() == 2 || ($close_date->getTuesdayOpen() == $close_date->getTuesdayClose())) {
                $closed[] = 2;
            }
            if ($close_date->getWednesdayStatus() == 2 || ($close_date->getWednesdayOpen() == $close_date->getWednesdayClose())) {
                $closed[] = 3;
            }
            if ($close_date->getThursdayStatus() == 2 || ($close_date->getThursdayOpen() == $close_date->getThursdayClose())) {
                $closed[] = 4;
            }
            if ($close_date->getFridayStatus() == 2 || ($close_date->getFridayOpen() == $close_date->getFridayClose())) {
                $closed[] = 5;
            }
            if ($close_date->getSaturdayStatus() == 2 || ($close_date->getSaturdayOpen() == $close_date->getSaturdayClose())) {
                $closed[] = 6;
            }
            if ($close_date->getSundayStatus() == 2 || ($close_date->getSundayOpen() == $close_date->getSundayClose())) {
                $closed[] = 0;
            }
        }
        $holidays = Mage::getModel('storepickup/holiday')->getCollection()
                                                         ->addFieldToFilter('store_id', array('finset' => $storeId));

        foreach ($holidays as $holiday) {
            $dateFrom = str_replace('-', '', $holiday->getDate());
            $dateTo = str_replace('-', '', $holiday->getHolidayDateTo());
            for ($i = (int) $dateFrom; $i <= (int) $dateTo; $i++) {
                $holiday_date[] = $i;
                $comment[] = $holiday->getComment();
            }
        }
        $date['specialdate'] = $specialday_date;
        $date['holidaydate'] = $holiday_date;
        $date['closed'] = $closed;
        $date['comment'] = $comment;

        $this->getResponse()->setBody(json_encode($date));
    }

}
