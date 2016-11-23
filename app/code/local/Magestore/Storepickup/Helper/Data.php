<?php

class Magestore_Storepickup_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getSytemUnit()
    {
        return array(
            'km' => array('label' => 'Km', 'factor' => 1000),
            'mi' => array('label' => 'Mi', 'factor' => 1609.34),
            'm' => array('label' => 'M', 'factor' => 1),
        );
    }
    //Edit by Tien
    public function exportAllStoreMultiViewToJson()
    {
        $storeviews = Mage::app()->getStores();
        foreach ($storeviews as $key => $storeview) {
            $this->exportAllStoreToJson($storeview->getId());
        }
        //Export for store view default
        $this->exportAllStoreToJson();
    }
    public function echoAllStoreToJson($storeViewId = null) {
        $fieldToSelect = array(
            'store_id',
            'store_latitude',
            'store_longitude',
            'store_name',
            'store_phone',
            'state',
            'zipcode',
            'zoom_level',
            'url_id_path',
            'address',
            'city',
            'country',
            'sunday_status',
            'monday_status',
            'tuesday_status',
            'wednesday_status',
            'thursday_status',
            'friday_status',
            'saturday_status',
            'pin_color',
            'image_icon',
            'tag_ids',
        );
        $storeCollection = Mage::getModel('storepickup/store')
            ->getCollection()
            ->setStoreId($storeViewId)
            ->addFieldToSelect($fieldToSelect)
            ->addFieldToFilter('status', 1);

        $images = array();
        $imageCollection = Mage::getModel('storepickup/image')->getCollection()
                                                              ->addFieldToFilter('statuses', 1);
        foreach ($imageCollection as $image) {
            $link = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'storepickup/images/';
            $images[$image->getStoreId()] = $link . '/' . $image->getName();
        }
        $countrysArray = array();
        $countrys = Mage::getModel('directory/country')->getCollection();
        foreach ($countrys as $country) {
            $countrysArray[$country->getCountryId()] = $country->getName();
        }
        $stores = array();
        foreach ($storeCollection as $store) {
            $store->setStoreId($storeViewId);
            $storeData = $store->getData();
            if (isset($images[$store->getId()])) {
                $storeData['image_src'] = $images[$store->getId()];
            } else {
                $storeData['image_src'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'storepickup/market-store-icon.jpg';
            }

            if ($countrysArray[$store->getCountry()]) {
                $storeData['country_name'] = $countrysArray[$store->getCountry()];
            }
            $storeData['store_view_url'] = Mage::helper('storepickup/url')->getStoreViewUrl($store->getUrlIdPath(), $store->getId());
            $stores[] = $storeData;
        }
        return 'var listStoreJson = ' . Mage::helper('core')->jsonEncode($stores) . ';';
    }
    public function exportAllStoreToJson($storeViewId = null)
    {
        if (isset($storeViewId)) {
            $filePath = Mage::getBaseDir('media') . '/storepickup/liststore_storeview' . $storeViewId . '.js';
        } else {
            $filePath = Mage::getBaseDir('media') . '/storepickup/liststore.js';
        }

        $fieldToSelect = array(
            'store_id',
            'store_latitude',
            'store_longitude',
            'store_name',
            'store_phone',
            'state',
            'zipcode',
            'zoom_level',
            'url_id_path',
            'address',
            'city',
            'country',
            'sunday_status',
            'monday_status',
            'tuesday_status',
            'wednesday_status',
            'thursday_status',
            'friday_status',
            'saturday_status',
            'pin_color',
            'image_icon',
            'tag_ids',
        );
        $storeCollection = Mage::getModel('storepickup/store')
            ->getCollection()
            ->setStoreId($storeViewId)
            ->addFieldToSelect($fieldToSelect)
            ->addFieldToFilter('status', 1);

        $images = array();
        $imageCollection = Mage::getModel('storepickup/image')->getCollection()
                                                              ->addFieldToFilter('statuses', 1);
        foreach ($imageCollection as $image) {
            $link = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'storepickup/images/';
            $images[$image->getStoreId()] = $link . '/' . $image->getName();
        }
        $countrysArray = array();
        $countrys = Mage::getModel('directory/country')->getCollection();
        foreach ($countrys as $country) {
            $countrysArray[$country->getCountryId()] = $country->getName();
        }
        $stores = array();
        foreach ($storeCollection as $store) {
            $store->setStoreId($storeViewId);
            $storeData = $store->getData();
            if (isset($images[$store->getId()])) {
                $storeData['image_src'] = $images[$store->getId()];
            } else {
                $storeData['image_src'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'storepickup/market-store-icon.jpg';
            }

            if ($countrysArray[$store->getCountry()]) {
                $storeData['country_name'] = $countrysArray[$store->getCountry()];
            }
            $storeData['store_view_url'] = Mage::helper('storepickup/url')->getStoreViewUrl($store->getUrlIdPath(), $store->getId());
            $stores[] = $storeData;
        }

        file_put_contents($filePath, '; var listStoreJson = ' . Mage::helper('core')->jsonEncode($stores)) . ';';
    }
    //End by Tien
    public function getDateFormat()
    {
        $date_format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        // return $date_format;
        //Edit by Tien
        // Fix for most languages
        switch ($date_format) {
            case 'd-M-yyyy':
            case 'dd-MM-yy':
            case 'd.M.yyyy':
            case 'dd.MM.yyyy':
            case 'dd-MM-yyyy':
            case 'dd.M.yyyy':
            case 'd.M.yyyy.':
            case 'dd/MM/y':
                return '%d-%m-%Y';
            case 'd. MM. yyyy':
                return '%d-%m-%Y';
                break;
            case 'yyyy-MM-dd':case 'yyyy.MM.dd.':case 'yyyy. M. d.':case 'yyyy-M-d':
                return '%Y-%m-%d';
                break;
        }
        //End by Tien

        $str = explode("/", $date_format);
        foreach ($str as $key) {
            if ($key == 'yyyy' || $key == 'yy') {
                $sort[] = 'Y';
            } else {
                $sort[] = substr(strtolower($key), 0, 1);
            }

        }

        $result = '%' . $sort[0] . '-%' . $sort[1] . '-%' . $sort[2];

        return $result;
    }

    public function getStoresUrl()
    {
        return $this->_getUrl('storepickup/index/index', array());
    }
    /*Edit by Son*/
    public function saveIcon($flie, $id)
    {

        $this->createImageIcon($flie, $id);
    }
    public function createImageIcon($flie, $id)
    {
        try {

            /* Starting upload */
            $uploader = new Varien_File_Uploader($flie);
            // Any extention would work
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);

            // We set media as the upload dir
            //            $unwanted_array = array('ả'=>'a','ố'=>'o','ồ'=>'o','ổ'=>'o','ế'=>'e','ề'=>'e','ể'=>'e',    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A','Ả'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            //                            'Ê'=>'E', 'Ë'=>'E','Ẻ'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I','Ỉ'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
            //                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U','Ủ'=>'U','Ỷ'=>'Y', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'a'=>'a','ả'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
            //                            'è'=>'e', 'é'=>'e', 'ê'=>'e','ẻ'=>'e', 'ë'=>'e', 'ì'=>'i','ỉ'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o','ỏ'=>'o', 'ô'=>'o', 'õ'=>'o',
            //                            'ö'=>'o', '{'=>'_','}'=>'_','('=>'_',')'=>'_','ø'=>'o', 'ù'=>'u', 'ú'=>'u','ủ'=>'u', 'û'=>'u','ỷ'=>'y', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
            //
            $path = $this->getPathImageIcon($id);

//            $flie['name'] = strtr( $flie['name'], $unwanted_array );
            //            $flie['name']=str_replace(' ', '', $flie['name']);
            $vntext = $this->vn_str_filter($flie['name']);
            $flie['name'] = preg_replace('([^a-zA-Z0-9-\.])', '_', $vntext);
            $flie['name'] = 'icon_' . $flie['name'];
            $uploader->save($path, $flie['name']);
            $this->reSizeImage($id, $flie['name']);
        } catch (Exception $e) {

        }
    }
    public function getPathImageIcon($id)
    {
        $path = Mage::getBaseDir('media') . DS . 'storepickup' . DS . 'images' . DS . 'icon' . DS . $id;
        return $path;
    }
    public function reSizeImage($id, $nameimage)
    {
        $vntext = $this->vn_str_filter($nameimage);
        $nameimage = preg_replace('([^a-zA-Z0-9-\.])', '_', $vntext);
        $_imageUrl = $this->getPathImageIcon($id) . DS . $nameimage;

        $imageResized = $this->getPathImageIcon($id) . DS . 'resize' . DS . $nameimage;

        if (!file_exists($imageResized) && file_exists($_imageUrl)) {
            $imageObj = new Varien_Image($_imageUrl);
            $imageObj->keepTransparency(true);
            $imageObj->constrainOnly(true);
            $imageObj->keepAspectRatio(true);
            $imageObj->keepFrame(false);
            $imageObj->resize(40, 40);

            $imageObj->save($imageResized);
        }
    }
    public function vn_str_filter($str)
    {
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );

        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        return $str;
    }
    /*End by Son*/
    public function getTablePrefix()
    {
        $table = Mage::getResourceSingleton("eav/entity_attribute")->getTable("eav/attribute");

        $prefix = str_replace("eav_attribute", "", $table);

        return $prefix;
    }

    public function getListStoreByCustomerAddress()
    {
        $options = array();

        $cart = Mage::getSingleton('checkout/cart');
        $shippingAddress = $cart->getQuote()->getShippingAddress();

        $collection = Mage::getResourceModel('storepickup/store_collection')
            ->addFieldToFilter('country', $shippingAddress->getCountryId())
        ;

        if ($shippingAddress->getPostcode()) {
            $collection->addFieldToFilter('zipcode', $shippingAddress->getPostcode());
        }

        if (is_array($shippingAddress->getStreet())) {
            $street = $shippingAddress->getStreet();
            $suburb = trim(substr($street[0], strrpos($street[0], ',') + 1));
            $collection->addFieldToFilter('suburb', $suburb);
        } else if ($shippingAddress->getCity()) {
            $collection->addFieldToFilter('city', $shippingAddress->getCity());
        } else if ($shippingAddress->getRegion()) {
            $collection->addFieldToFilter('state', $shippingAddress->getRegion());
        }

        if (count($collection)) {
            foreach ($collection as $store) {
                $options[$store->getId()] = $store->getStoreName();
            }
        }

        return $options;
    }

    public function getListPreferedContact()
    {
        return array(1 => 'Email', 2 => 'Fax', 3 => 'SMS');
    }

    public static function getStoreOptions1()
    {
        $options = array();
        $collection = Mage::getModel('storepickup/store')->getCollection();
        foreach ($collection as $store) {
            $options[$store->getId()] = $store->getStoreName();
        }
        return $options;
    }

    public static function getStoreOptions2()
    {
        $options = array();
        $collection = Mage::getModel('storepickup/store')->getCollection()
                                                         ->setOrder('store_name', 'ASC');
        foreach ($collection as $store) {
            $option = array();
            $option['label'] = $store->getStoreName();
            $option['value'] = $store->getId();
            $options[] = $option;
        }

        return $options;
    }

    public function getListTime()
    {
        $listTime = array('9h00' => '9h00', '10h30' => '10h30');

        return $listTime;
    }

    public function getChangeStoreUrl()
    {
        return $this->_getUrl('storepickup/index/changestore', array('_secure' => true));
    }

    public function getChangeStoreAdminUrl()
    {
        return Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/storepickup_storepickup/changestore', array('_secure' => true));
    }

    public function getChangTimeUrl()
    {
        return $this->_getUrl('storepickup/index/changetime', array('_secure' => true));
    }

    public function getChangeTimeAdminUrl()
    {
        return Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/storepickup_storepickup/changetime', array('_secure' => true));
    }

    public function getChangDateUrl()
    {
        return $this->_getUrl('storepickup/index/changedate', array('_secure' => true));
    }

    public function getChangDateAdminUrl()
    {
        return Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/storepickup_storepickup/changedate', array('_secure' => true));
    }

    public function prepareListTime($shipdate, $datestamp, $store_id)
    {
        $listTime = Mage::getResourceModel('storepickup/store')->getValidTime(date('m-d-Y', $shipdate), $store_id);
        $newlistTime = array();

        if (count($listTime)) {
            foreach ($listTime as $time) {
                if ($time['status'] == 1) {
                    $newlistTime[] = $this->getSysTime($time['open_time']);
//                    $newlistTime[] = $this->getSysTime($time['open_time_break']);
//                    $newlistTime[] = $this->getSysTime($time['close_time_break']);
                    $newlistTime[] = $this->getSysTime($time['close_time']);
                } else {
                    return -2; //closed
                }
            }
        } else {
            return -3; //holiday
        }

        $min_time = $this->getMin($newlistTime);
        $max_time = $this->getMax($newlistTime);

        $sys_min_time = $this->getMinTime($shipdate, $store_id);

        $interval_time = isset($listTime[0]['interval_time']) ? $listTime[0]['interval_time'] : 30;
        $interval_time = intval($interval_time) * 60;

        $min_time = $min_time + ($shipdate - $datestamp);
        $max_time = $max_time + ($shipdate - $datestamp);

        if (($sys_min_time + $interval_time) > $max_time) {
            return -1;
        }

        if ($min_time && $max_time) {
            return $this->generateTimes($min_time, $max_time, $sys_min_time, $interval_time);
        }
    }

    public function prepareListTimeSpecialday($shipdate, $datestamp, $store_id)
    {
        $specialdays = Mage::getModel('storepickup/specialday')->getCollection()
                                                               ->addFieldToFilter('store_id', array('finset' => $store_id));
        $check_special = false;
        foreach ($specialdays as $specialday) {
            $specialdayFrom = str_replace('-', '', $specialday->getDate());
            $specialdayTo = str_replace('-', '', $specialday->getSpecialdayDateTo());
            $dateship = str_replace('-', '', date('Y-m-d', $shipdate));
            for ($j = (int) $specialdayFrom; $j <= (int) $specialdayTo; $j++) {
                if ((int) $dateship >= (int) $specialdayFrom && (int) $dateship <= (int) $specialdayTo) {
                    $spfrom = $specialday->getDate();
                    $spto = $specialday->getSpecialdayDateTo();
                    break;
                }
            }
        }

        $listTime = Mage::getResourceModel('storepickup/store')->getValidSpecialTime(date('m-d-Y', $shipdate), $store_id, $spfrom, $spto);
        $newlistTime = array();

        if (count($listTime)) {
            foreach ($listTime as $time) {
                if (!isset($time['status'])) {
                    $newlistTime[] = $this->getSysTime($time['open_time']);

                    $newlistTime[] = $this->getSysTime($time['close_time']);
                }
            }
        }

        $min_time = $this->getMin($newlistTime);
        $max_time = $this->getMax($newlistTime);

        $sys_min_time = $this->getMinTime($shipdate, $store_id);

        $interval_time = isset($listTime[0]['interval_time']) ? $listTime[0]['interval_time'] : 30;
        $interval_time = intval($interval_time) * 60;

        $min_time = $min_time + ($shipdate - $datestamp);
        $max_time = $max_time + ($shipdate - $datestamp);

        if (($sys_min_time + $interval_time) > $max_time) {
            return -1;
        }

        if ($min_time && $max_time) {
            return $this->generateTimes($min_time, $max_time, $sys_min_time, $interval_time);
        }
    }

    public function getTimeSelectHTML($date, $store_id)
    {
        // check shipping date
        $timestamp = Mage::getModel('core/date')->timestamp(time());
        $datestamp = strtotime(date('Y-m-d', $timestamp));

        $dateformat = $this->getDateFormat();

        switch ($dateformat) {
            case '%Y-%m-%d':
                $yyy = substr($date, 0, 4);
                $mm = substr($date, 5, 2);
                $dd = substr($date, 8, 2);
                $shipdate = $yyy . '-' . $mm . '-' . $dd;
                break;
            case '%d-%m-%Y':
                $yyy = substr($date, 6, 4);
                $mm = substr($date, 3, 2);
                $dd = substr($date, 0, 2);
                $shipdate = $yyy . '-' . $mm . '-' . $dd;
                break;
            case '%Y-%d-%m':
                $yyy = substr($date, 0, 4);
                $mm = substr($date, 8, 2);
                $dd = substr($date, 5, 2);
                $shipdate = $yyy . '-' . $mm . '-' . $dd;
                break;
            default:
                $shipdate = substr($date, 6, 4) . '-' . substr($date, 0, 2) . '-' . substr($date, 3, 2);
                break;
        }

        //check specialday
        $specialdays = Mage::getModel('storepickup/specialday')->getCollection()
                                                               ->addFieldToFilter('store_id', array('finset' => $store_id));
        $check_special = false;
        foreach ($specialdays as $specialday) {
            $specialdayFrom = str_replace('-', '', $specialday->getDate());
            $specialdayTo = str_replace('-', '', $specialday->getSpecialdayDateTo());
            for ($j = (int) $specialdayFrom; $j <= (int) $specialdayTo; $j++) {
                if ($j == (int) str_replace('-', '', $shipdate)) {
                    $shipdate = $j;
                    $check_special = true;
                    break;
                }
            }
        }

        $html = '';

        if ($check_special) {

            $yyy = substr((string) $shipdate, 0, 4);
            $mm = substr((string) $shipdate, 4, 2);
            $dd = substr((string) $shipdate, 6, 2);
            $shipdate = $yyy . '-' . $mm . '-' . $dd;

            $shipdateTime = strtotime($shipdate);

            $listTime = $this->prepareListTimeSpecialday($shipdateTime, $datestamp, $store_id);
            $html .= '<option value="" selected >' . $this->__('Select Pickup Time') . '</option>';

            if (count($listTime)) {
                foreach ($listTime as $value => $label) {
                    if ($this->getTimeFormat() == '12') {
                        $label = date("g:i A", strtotime($label));
                    }
                    $html .= '<option value="' . $value . '">' . $label . '</option>';
                }
            }

            return $html;
        }
        $shipdateTime = strtotime($shipdate);

        if ($datestamp > $shipdateTime) {
            //return json_encode(array('message'=>$this->__('Invalid date!')));
        }

        //valid date
        $listTime = $this->prepareListTime($shipdateTime, $datestamp, $store_id);

        switch ($listTime) {
            case -1:    //early shipping time
                return json_encode(array('message' => $this->__('early date')));
                break;
            case -2:    //closed
                return json_encode(array('message' => $this->__('Store will be closed on this day')));
                break;
            case -3:    //holiday
                $holidays = Mage::getModel('storepickup/holiday')->getCollection()
                                                                 ->addFieldToFilter('store_id', array('finset' => $store_id));
                foreach ($holidays as $holiday) {
                    $dateFrom = str_replace('-', '', $holiday->getDate());
                    $dateTo = str_replace('-', '', $holiday->getHolidayDateTo());
                    for ($i = (int) $dateFrom; $i <= (int) $dateTo; $i++) {
                        $holiday_date[] = $i;
                        $comment = $holiday->getComment();
                    }
                }

                if ($comment) {
                    $commenthtml = str_replace(' ', '_', $comment);
                } else {
                    $commenthtml = $this->__('Holiday!');
                }

                return json_encode(array('message' => $commenthtml));
                break;
        }

        $dayOfWeek = date('w', strtotime($shipdate));
        $store = Mage::getModel('storepickup/store')->load($store_id);
        switch ($dayOfWeek) {
            case 0:
                $day = 'Sunday';
                $open_break = "";
                $close_break = "";
                if (($store->getSundayOpen() == $store->getSundayClose())
                    || $store->getSundayStatus() != 1) {
                    $commenthtml = $this->__('Sunday closed.');
                    return json_encode(array('message' => $commenthtml));
                } else {
                    $open_break = $store->getSundayOpenBreak();
                    $close_break = $store->getSundayCloseBreak();
                }
                break;
            case 1:
                $day = 'Monday';
                $open_break = "";
                $close_break = "";
                if (($store->getMondayOpen() == $store->getMondayClose())
                    || $store->getMondayStatus() != 1) {

                    $commenthtml = $this->__('Monday closed.');
                    return json_encode(array('message' => $commenthtml));
                } else {
                    $open_break = $store->getMondayOpenBreak();
                    $close_break = $store->getMondayCloseBreak();
                }
                break;
            case 2:
                $day = 'Tuesday';
                $open_break = "";
                $close_break = "";
                if (($store->getTuesdayOpen() == $store->getTuesdayClose())
                    || $store->getTuesdayStatus() != 1) {
                    $commenthtml = $this->__('Tuesday closed.');
                    return json_encode(array('message' => $commenthtml));
                } else {
                    $open_break = $store->getTuesdayOpenBreak();
                    $close_break = $store->getTuesdayCloseBreak();
                }
                break;
            case 3:
                $day = 'Wednesday';
                $open_break = "";
                $close_break = "";
                if (($store->getWednesdayOpen() == $store->getWednesdayClose())
                    || $store->getWednesdayStatus() != 1) {
                    $commenthtml = $this->__('Wednesday closed.');
                    return json_encode(array('message' => $commenthtml));
                } else {
                    $open_break = $store->getWednesdayOpenBreak();
                    $close_break = $store->getWednesdayCloseBreak();
                }
                break;
            case 4:
                $day = 'Thursday';
                $open_break = "";
                $close_break = "";
                if (($store->getThursdayOpen() == $store->getThursdayClose())
                    || $store->getThursdayStatus() != 1) {
                    $commenthtml = $this->__('Thursday closed.');
                    return json_encode(array('message' => $commenthtml));
                } else {
                    $open_break = $store->getThursdayOpenBreak();
                    $close_break = $store->getThursdayCloseBreak();
                }
                break;
            case 5:
                $day = 'Friday';
                $open_break = "";
                $close_break = "";
                if (($store->getFridayOpen() == $store->getFridayClose())
                    || $store->getFridayStatus() != 1) {
                    $commenthtml = $this->__('Friday closed.');
                    return json_encode(array('message' => $commenthtml));
                } else {
                    $open_break = $store->getFridayOpenBreak();
                    $close_break = $store->getFridayCloseBreak();
                }
                break;
            case 6:
                $day = 'Saturday';
                $open_break = "";
                $close_break = "";
                if (($store->getSaturdayOpen() == $store->getSaturdayClose())
                    || $store->getSaturdayStatus() != 1) {
                    $commenthtml = $this->__('Saturday closed.');
                    return json_encode(array('message' => $commenthtml));
                } else {
                    $open_break = $store->getSaturdayOpenBreak();
                    $close_break = $store->getSaturdayCloseBreak();
                }
                break;
        }

        $html .= '<option value="" selected >' . $this->__('Select Pickup Time') . '</option>';

        if (count($listTime)) {
            $data = Mage::getSingleton('checkout/session')->getData('storepickup_session');
        }
        $data = $data['date'];
        $today = date("F j, Y");
        $todaytime = strtotime($today);
        $todayTime = date("h:i");
        $datatime = strtotime($data);
        $open = explode(':', $open_break);
        if (strlen($open[0]) == 1) {
            $open[0] = '0' . $open[0];
        }
        if (strlen($open[1]) == 1) {
            $open[1] = '0' . $open[1];
        }
        $open_break = join(':', $open);
        $close = explode(':', $close_break);
        if (strlen($close[0]) == 1) {
            $close[0] = '0' . $close[0];
        }
        if (strlen($close[1]) == 1) {
            $close[1] = '0' . $close[1];
        }
        $close_break = join(':', $close);
        foreach ($listTime as $value => $label) {
            if ($this->getTimeFormat() == '12') {
                $label = date("g:i A", strtotime($label));
            }
            if ($todaytime == $datatime) {
                if ($value >= $todayTime && ($value <= $open_break || $value >= $close_break)) {
                    $html .= '<option value="' . $value . '">' . $label . '</option>';
                }
            }
            if ($value <= $open_break || $value >= $close_break) {
                $html .= '<option value="' . $value . '">' . $label . '</option>';
            }
        }

        return $html;
    }

    public function generateTimes($mintime, $maxtime, $sys_min_time, $interval_time = 30)
    {

        //$sys_min_time = strtotime(date('H:i:s',$sys_min_time));

        $listTime = array();

        $i = $mintime;

        while ($i <= $maxtime) {
            if ($i >= $sys_min_time) {
                $time = date('H:i', $i);
                $listTime[$time] = $time;
            }

            $i += $interval_time;
        }

        return $listTime;
    }

    public function getStorepickupByOrderId($order_id)
    {
        $storepickup = null;
        if (!$order_id) {
            return "";
        }

        $storeorder = Mage::getModel('storepickup/storeorder')->getCollection()
                                                              ->addFieldToFilter('order_id', $order_id)
                                                              ->getFirstItem();
        $storeId = $storeorder->getStoreId();
        //Zend_Debug::dump($storeId);
        if ($storeId) {
            $storepickup = Mage::getModel('storepickup/store')->load($storeId);
        }

        return $storepickup;
    }

    public function getSysTime($timeHI)
    {
        $day = Mage::getModel('core/date')->timestamp(time());

        $timeHI = explode(':', $timeHI);

        $time = mktime($timeHI[0], $timeHI[1], 0, date('m', $day), date('d', $day), date('Y', $day));

        return $time;
    }

    public function getMinTime($shipdate, $store_id)
    {
        $timestamp = Mage::getModel('core/date')->timestamp(time());

        $dayOfWeek = date('w', strtotime($shipdate));

        $store = Mage::getModel('storepickup/store')->load($store_id);

        switch ($dayOfWeek) {
            case 0:
                $day = 'Sunday';
                $minimun_gap = $store->getSundayTimeInterval();
                break;
            case 1:
                $day = 'Monday';
                $minimun_gap = $store->getMondayTimeInterval();
                break;
            case 2:
                $day = 'Tuesday';
                $minimun_gap = $store->getTuesdayTimeInterval();
                break;
            case 3:
                $day = 'Wednesday';
                $minimun_gap = $store->getWednesdayTimeInterval();
                break;
            case 4:
                $day = 'Thursday';
                $minimun_gap = $store->getThursdayTimeInterval();
                break;
            case 5:
                $day = 'Friday';
                $minimun_gap = $store->getFridayTimeInterval();
                break;
            case 6:
                $day = 'Saturday';
                $minimun_gap = $store->getSaturdayTimeInterval();
                break;
        }

        $minimun_gap = $minimun_gap ? $minimun_gap : 30;
        $minimun_gap = intval($minimun_gap) * 60;

        return $timestamp + $minimun_gap;
    }

    public function convertTimeToSecond($timeHI)
    {
        $timeHI = explode(':', $timeHI);
        if (isset($timeHI[0]) && isset($timeHI[1])) {
            return (intval($timeHI[0]) * 3600 + intval($timeHI[1]) * 60);
        }

    }

    public function getMin($list)
    {
        if (!count($list)) {
            return null;
        }

        $min = -1;
        foreach ($list as $item) {
            if ($min == -1) {
                $min = $item;
            } elseif ($item < $min) {
                $min = $item;
            }

        }

        return $min;
    }

    public function getMax($list)
    {
        if (!count($list)) {
            return null;
        }

        $max = 0;
        foreach ($list as $item) {
            if ($item > $max) {
                $max = $item;
            }

        }

        return $max;
    }

    public function getFinalSku($sku)
    {
        try {
            $sku = Mage::helper('core/string')->splitInjection($sku);
            return $sku;
        } catch (Exception $e) {
            return $sku;
        }
    }

    public function getCustomerAddress($addressDefault = false)
    {
        $cSession = Mage::getSingleton('customer/session');

        $cart = Mage::getSingleton('checkout/cart');
        if ($cart->getQuote() && !$addressDefault) {
            return $cart->getQuote()->getShippingAddress();
        }

        if ($cSession->isLoggedIn()) {
            $address = $cSession->getCustomer()->getDefaultShippingAddress();
            if ($address) {
                return $address;
            }

        }

        $address = Mage::getSingleton('adminhtml/session_quote')->getQuote()->getShippingAddress();
        if ($address->getQuoteId()) {
            return $address;
        }

    }

    public function getStoreByLocation()
    {
        if (Mage::getModel('storepickup/shipping_storepickup')->getConfigData('active_gapi')) {
            $stores = Mage::getSingleton('storepickup/store')->filterStoresUseGAPI();
        } else {
            $stores = Mage::getSingleton('storepickup/store')->convertToList();
        }
        return $stores;
    }

    public function getImageUrl($id_store)
    {
        $collection = Mage::getModel('storepickup/image')->getCollection()->addFieldToFilter('store_id', $id_store)->addFieldToFilter('del', 2);
        $url = array();
        foreach ($collection as $item) {
            if ($item->getData('name')) {
                $url[] = Mage::getBaseUrl('media') . 'storepickup/image/' . $id_store . '/' . $item->getData('options') . '/' . $item->getData('name');
            }
        }
        return $url;
    }

    public function getSpecialDays($id)
    {
        $specialdays = Mage::getModel('storepickup/specialday')
            ->getCollection()
            ->addFieldToFilter('store_id', array('finset' => $id));

        //$day_show = Mage::getStoreConfig('storelocator/general/show_spencial_days', Mage::app()->getStore()->getStoreId());
        /// $dateLimit = date('Y-m-d');
        //Zend_debug::dump($dateLimit);die();
        //$dateLimit = str_replace('-', '', $dateLimit);
        $count = 0;
        $days = array();
        foreach ($specialdays as $specialday) {
            $dateFrom = str_replace('-', '', $specialday->getDate());
            $dateTo = str_replace('-', '', $specialday->getSpecialdayDateTo());
            for ($i = $dateFrom; $i <= $dateTo; $i++) {

                $yyy = substr((string) $i, 0, 4);
                $mm = substr((string) $i, 4, 2);
                $dd = substr((string) $i, 6, 2);
                if (0 < (int) $dd && (int) $dd < 32) {
                    $j = $yyy . '-' . $mm . '-' . $dd;
                    $days[$count]['date'] = $j;
                    $days[$count]['time_open'] = $specialday->getSpecialdayTimeOpen();
                    $days[$count]['time_close'] = $specialday->getSpecialdayTimeClose();
                    $days[$count]['name'] = $specialday->getSpecialName();

                    $count++;
                }

            }
        }

        for ($k = 0; $k < count($days); $k++) {
            for ($l = $k + 1; $l < count($days); $l++) {
                if (strtotime($days[$l]['date']) < strtotime($days[$k]['date'])) {
                    $temp = $days[$k];
                    $days[$k] = $days[$l];
                    $days[$l] = $temp;
                }
            }
        }
        return $days;
    }
    public function getHolidayDays($id)
    {
        $holidays = Mage::getModel('storepickup/holiday')
            ->getCollection()
            ->addFieldToFilter('store_id', array('finset' => $id));
        // Zend_debug::dump($holidays->getData());die();
        $specialdays = $this->getSpecialDays($id);

        $days = array();
        // $day_show = Mage::getStoreConfig('storelocator/general/show_spencial_days', Mage::app()->getStore()->getStoreId());
        // $dateLimit = date('Y-m-d', strtotime('+' . $day_show . ' day'));
        //$dateLimit = str_replace('-', '', $dateLimit);
        $count = 0;
        foreach ($holidays as $holiday) {

            $dateFrom = str_replace('-', '', $holiday->getDate());
            $dateTo = str_replace('-', '', $holiday->getHolidayDateTo());
            for ($i = $dateFrom; $i <= $dateTo; $i++) {

                $yyy = substr((string) $i, 0, 4);
                $mm = substr((string) $i, 4, 2);
                $dd = substr((string) $i, 6, 2);
                if (0 < (int) $dd && (int) $dd < 32) {
                    $j = $yyy . '-' . $mm . '-' . $dd;
                    $check_specialday = false;
                    foreach ($specialdays as $specialday) {
                        if ($j == $specialday['date']) {
                            $check_specialday = true;
                        }
                    }

                }

                if (!$check_specialday) {
                    $days[$count]['date'] = $j;
                    $days[$count]['name'] = $holiday->getHolidayName();
                }
                $count++;

            }
        }

        for ($k = 0; $k < count($days); $k++) {
            for ($l = $k + 1; $l < count($days); $l++) {
                if (isset($days[$l]) && isset($days[$k])) {
                    if (strtotime($days[$l]['date']) < strtotime($days[$k]['date'])) {
                        $temp = $days[$k];
                        $days[$k] = $days[$l];
                        $days[$l] = $temp;
                    }
                }

            }
        }

        return $days;
    }

    public function getImageUrlJS()
    {
        $url = Mage::getBaseUrl('media') . 'storepickup/image/';
        return $url;
    }

    public function getDataImage($id)
    {
        $collection = Mage::getModel('storepickup/image')->getCollection()->addFilter('store_id', $id);
        return $collection;
    }

    public function getImagePath($id, $option)
    {
        $path = Mage::getBaseDir('media') . DS . 'storepickup' . DS . 'image' . DS . $id . DS . $option;
        return $path;
    }

    public function getImagePathCache($id, $option)
    {
        $path = Mage::getBaseDir('media') . DS . 'storepickup' . DS . 'image' . DS . 'cache' . DS . $id . DS . $option;
        return $path;
    }

    public function createImage($image, $id, $last, $option)
    {
        try {
            /* Starting upload */
            $uploader = new Varien_File_Uploader('images' . $option);

            // Any extention would work
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
            $uploader->setAllowRenameFiles(true);
            // Set the file upload mode
            // false -> get the file directly in the specified folder
            // true -> get the file in the product like folders
            //    (file.jpg will go in something like /media/f/i/file.jpg)
            $uploader->setFilesDispersion(false);

            // We set media as the upload dir
            $path = $this->getImagePath($id, $last);
            // var_dump($path);die();
            $uploader->save($path, $image);
            /* $pathcache = $this->getImagePathCache($id, $last);
        $path_resze = $pathcache. DS .$image;
        $imageObj = new Varien_Image($path. DS .$image);
        $imageObj->constrainOnly(TRUE);
        $imageObj->keepAspectRatio(TRUE);
        $imageObj->keepFrame(FALSE);
        $imageObj->resize(350, 312);
        $imageObj->save($path_resze); */
        } catch (Exception $e) {

        }
    }

    public function DeleteOldImage()
    {
        $image_info = Mage::getModel('storepickup/image')->getCollection()->addFilter('del', 1);
        foreach ($image_info as $item) {
            $id = $item->getData('store_id');
            $option = $item->getData('options');
            $image = $item->getData('name');

            $image_path = $this->getImagePath($id, $option) . DS . $image;
            $image_path_cache = $this->getImagePathCache($id, $option) . DS . $image;
            try {
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
                if (file_exists($image_path_cache)) {
                    unlink($image_path_cache);
                }
            } catch (Exception $e) {

            }
        }
    }

    public function SaveImage($image, $id_store, $file)
    {
        foreach ($image as $item) {
            //if($item !=)
            $file_format = substr($file['images' . $item['options']]['name'], strlen($file['images' . $item['options']]['name']) - 4, 4);
            $fileName = substr($file['images' . $item['options']]['name'], 0, strlen($file['images' . $item['options']]['name']) - 4);

            $mod = Mage::getModel('storepickup/image');
            $name_image = Mage::helper('storepickup/url')->characterSpecial($fileName) . $file_format;

            if ($item['delete'] == 0) {
                $last = $mod->getCollection()->getLastItem()->getData('options') + 1;
                $mod->setData('store_id', $id_store);
                if (($name_image != "") && isset($name_image) != null) {
                    $mod->setData('name', $name_image);
                    $this->createImage($name_image, $id_store, $last, $item['options']);

                    $mod->setData('del', 2);
                    $mod->setData('options', $last);
                    $mod->save();
                }

            } else if ($item['delete'] == 2) {
                if (($name_image != "") && isset($name_image) != null) {
                    $mod->setData('name', $name_image)->setId($item['id']);
                    $this->createImage($name_image, $id_store, $item['options'], $item['options']);
                }
                //$mod->setData('link', $item['link'])->setId($item['id']);
                $mod->setData('del', $item['delete'])->setId($item['id']);
                $mod->save();
            } else {
                if ($item['id'] != 0) {
                    if (($name_image != "") && isset($name_image) != null) {
                        $mod->setData('name', $name_image)->setId($item['id']);
                        $this->createImage($name_image, $id_store, $item['options'], $item['options']);
                    }
                    $mod->setData('del', $item['delete'])->setId($item['id']);
                    $mod->save();
                }
            }
        }
        $this->DeleteOldImage();
    }

//    public function setImageBig($radio, $id = null) {
    //
    //        $collection = Mage::getModel('storepickup/image')->getCollection()
    //                        ->addFieldToFilter('store_id',$id);
    //        foreach ($collection as $item) {
    //            $model = Mage::getModel('storepickup/image');
    //
    //            if ($item->getOptions() == $radio) {
    //                $model->setData('statuses', 1)->setId($item->getId());
    //            } else {
    //                $model->setData('statuses', 0)->setId($item->getId());
    //            }
    //            $model->save();
    //        }
    //
    //    }
    public function getBigImagebyStore($id)
    {
        $collection = Mage::getModel('storepickup/image')
            ->getCollection()
            ->addFieldToFilter('store_id', $id);

        $url = "";
        foreach ($collection as $item) {
            if ($item->getData('name')) {
                if ($item->getData('statuses') == 1) {
                    $url = Mage::getBaseUrl('media') . 'storepickup/images/' . $item->getData('name');
                    break;
                } else {
                    $url = Mage::getBaseUrl('media') . 'storepickup/images/' . $item->getData('name');
                }
            }
        }

        return $url;
    }

    /**
     *     @param orderid
     *     return 0 is send mail right
     * */
    public function checkEmailOwner($order_id)
    {

        $storepickup = null;
        if (!$order_id) {
            return 1;
        }

        $storeorder = Mage::getModel('storepickup/storeorder')->getCollection()
                                                              ->addFieldToFilter('order_id', $order_id)
                                                              ->getFirstItem();
        //Zend_debug::dump(is_null($storeorder->getStoreId()));die();
        $storeId = $storeorder->getStoreId();
        if (is_null($storeId) == true) {
            return 1;
        }
        if ($storeId) {
            $storepickup = Mage::getModel('storepickup/store')->load($storeId);
        }

        if ($storepickup->getData('status_order') == 2) {
            return 1;
        } else {
            return 0;
        }

    }

    public function getTimeFormat()
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        return Mage::getStoreConfig('carriers/storepickup/time_format', $storeId);
    }

    public function getUnitmeasurement()
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        return Mage::getStoreConfig('carriers/storepickup/unit_measurement', $storeId);
    }

    /* Edit by Tien*/
    public function getConfig($configname)
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        return Mage::getStoreConfig('carriers/storepickup/' . $configname, $storeId);
    }
    /* End by Tien*/

    public function translateDate($array)
    {
        foreach ($array as $key => $value) {
            $array[$key] = $this->__($value);
        }
        return $array;
    }



    /*
     *
     *
     * BEGIN Fix for SUPEE 8788 incompatibility issue
     * http://magento.stackexchange.com/questions/142006/issue-in-admin-panel-after-supee-patch-8788-installation/142013#142013
     *
     *
     * */
    protected function _isNoFlashUploader()
    {
        return class_exists("Mage_Uploader_Block_Abstract");
    }

    public function getFlowMin()
    {
        return $this->_isNoFlashUploader() ? "lib/uploader/flow.min.js" : null;
    }

    public function getFustyFlow()
    {
        return $this->_isNoFlashUploader() ? "lib/uploader/fusty-flow.js" : null;
    }

    public function getFustyFlowFactory()
    {
        return $this->_isNoFlashUploader() ? "lib/uploader/fusty-flow-factory.js" : null;
    }

    public function getAdminhtmlUploaderInstance()
    {
        return $this->_isNoFlashUploader() ? "mage/adminhtml/uploader/instance.js" : null;
    }
    /*
     *
     *
     * END Fix for SUPEE 8788 incompatibility issue
     * http://magento.stackexchange.com/questions/142006/issue-in-admin-panel-after-supee-patch-8788-installation/142013#142013
     *
     *
     * */
}
