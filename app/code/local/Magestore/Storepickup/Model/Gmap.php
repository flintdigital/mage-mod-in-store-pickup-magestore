<?php

class Magestore_Storepickup_Model_Gmap extends Varien_Object {

    const GURL = "https://maps.googleapis.com/maps/api/geocode/xml?address=";
    const GDISTANCE_URL = "https://maps.google.com/maps/nav?q=";
    const GPARAM = "&sensor=true&key=";
    const FOMAT_ADDRESS = "{{street}},{{city}},{{region}} {{zipcode}},{{country}}";
    const URL = "https://maps.googleapis.com/maps/api/distancematrix/xml?";
    const DEF = "&mode=driving&sensor=true&key=";

    public function getCoordinates($address = null) {
        $address = $address ? $address : $this->getAddress();

        $this->setAddress($address);

        if (!$address)
            return;

        $address = $this->getFormatedAddress();
        // Zend_debug::dump($address);
        $url = self::GURL;
        $url .= $address;
        $url .= self::GPARAM;
        $url .= $this->getGKey(); //echo $url;exit;
        $loop = 0;
        try {
            do {
                $result = Mage::helper('storepickup/url')->getResponseBody($url);
                $result = utf8_encode(htmlspecialchars_decode($result));
                $xml = simplexml_load_string($result);
                $status_code = (string) $xml->status;
                if ($status_code == 'OK') {
                    usleep(100000);
                }
                $loop++;
            } while ($status_code == 'OK' && $loop < 2);
            if ($status_code != 'OK')
                return array();

            $coordinates = array();
            $coordinates[1] = (string) $xml->result->geometry->location->lat;
            $coordinates[0] = (string) $xml->result->geometry->location->lng;

            // $coordinates = explode(',',$coordinates);

            return array('lat' => $coordinates[1], 'lng' => $coordinates[0]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getDistance($spoint, $dpoint) {
        if (!isset($spoint['lat']) || !isset($spoint['lng']) || !isset($dpoint['lat']) || !isset($dpoint['lng']))
            return false;

        $url = self::GDISTANCE_URL;
        $url .= 'from:' . $spoint['lat'] . ',' . $spoint['lng'] . '%20to:' . $dpoint['lat'] . ',' . $dpoint['lng'];
        $url .= self::GPARAM;
        $url .= $this->getGKey();

        $loop = 0;
        do {
            $result = Mage::helper('storepickup/url')->getResponseBody($url);

            $result = str_replace('\\u', '%23', $result);

            $result = Zend_Json_Decoder::decode($result);

            $status_code = $result['Status']['code'];

            $loop++;
        } while ($status_code == '620' && $loop < 1);

        if ($status_code != '200')
            return false;

        $distance = $result['Directions']['Distance']['meters'];
        $distance = intval($distance);

        return $distance;
    }

    public function getFormatedAddress() {
        $address = $this->getAddress();

        $formatedaddress = self::FOMAT_ADDRESS;
        $formatedaddress = str_replace('{{street}}', $address['street'], $formatedaddress);
        $formatedaddress = str_replace('{{city}}', $address['city'], $formatedaddress);
        $formatedaddress = str_replace('{{region}}', $address['region'], $formatedaddress);
        $formatedaddress = str_replace('{{zipcode}}', $address['zipcode'], $formatedaddress);
        $formatedaddress = str_replace('{{country}}', $address['country'], $formatedaddress);

        $formatedaddress = str_replace(' ', '%20', $formatedaddress);

        return $formatedaddress;
    }

    public function getGKey() {
        $shippingmethod = Mage::getModel('storepickup/shipping_storepickup');
        return $shippingmethod->getConfigData('gkey');
    }

    /**
     *
     * @param type $spoint
     * @param type $collection
     * @return type  $data_distance : store distance form adress ship to adress stores
     */
    function getDistanceStore($spoint, $collection) {

        $data_distance = array();
        $dpoint['lat'] = "0";
        $dpoint['lng'] = "0";

        // $url = self::URL;
		$url = 'https://maps.googleapis.com/maps/api/distancematrix/json?';
        $url .= 'origins=' . $spoint['lat'] . ',' . $spoint['lng'] . '&destinations=' . $dpoint['lat'] . ',' . $dpoint['lng'];
        foreach ($collection as $store) {
            $url .= "|" . $store->getStoreLatitude() . ',' . $store->getStoreLongitude();
        }
		
        $unit = Mage::helper('storepickup/data')->getUnitmeasurement();
        $url .= '&units=' . $unit;
        $locale = Mage::getStoreConfig('general/locale/code', Mage::app()->getStore()->getId());
        $url .= '&language=' . $locale;
        $url .= self::DEF;
        $url .= $this->getGKey();
		
		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$geoloc = curl_exec($ch);
			
			$json = json_decode($geoloc);
			$elements = $json->rows[0]->elements;//->element[0];
			// zend_debug::dump($elements);die();
			// return $json->rows[0]->elements[0]->distance->text;
			$size = sizeof($elements);
            for ($i = 1; $i < $size; $i ++) {
                $data_distance[$i]['text'] = (string) $elements[$i]->distance->text;
                $data_distance[$i]['value'] = (string) $elements[$i]->distance->value;
            }
		} catch (Exception $e) {
            return null;
        }
		
        // try {
            // $result = Mage::helper('storepickup/url')->getResponseBody($url);
            // $xml = simplexml_load_string($result);
			// zend_debug::dump($xml);die();
            // $elements = $xml->row->element;
            // $size = sizeof($elements);
            // for ($i = 1; $i < $size; $i ++) {
                // $data_distance[$i]['text'] = (string) $elements[$i]->distance->text;
                // $data_distance[$i]['value'] = (string) $elements[$i]->distance->value;
            // }
        // } catch (Exception $e) { 
            // return null;
        // }

        // Zend_debug::dump($data_distance);
        return $data_distance;
    }

    function getDistanceCustomer($spoint, $dpoint) {
        // $url = self::URL;
		$url = 'https://maps.googleapis.com/maps/api/distancematrix/json?';
        $url .= 'origins=' . $spoint['lat'] . ',' . $spoint['lng'] . '&destinations=' . $dpoint['lat'] . ',' . $dpoint['lng'];
        $unit = Mage::helper('storepickup/data')->getUnitmeasurement();
        $url .= '&units=' . $unit;
        $locale = Mage::getStoreConfig('general/locale/code', Mage::app()->getStore()->getId());
        $url .= '&language=' . $locale;
        $url .= self::DEF;
        $url .= $this->getGKey();
		try {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$geoloc = curl_exec($ch);
		
		$json = json_decode($geoloc);
		return $json->rows[0]->elements[0]->distance->text;
		} catch (Exception $e) {
            return null;
        }
        // try {

            // $result = Mage::helper('storepickup/url')->getResponseBody($url);
            // $xml = simplexml_load_string($result);
            // $elements = $xml->row->element;

            // return $elements->distance->text;
        // } catch (Exception $e) {
            // return null;
        // }
    }

}
