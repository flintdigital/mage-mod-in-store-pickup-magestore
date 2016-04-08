<?php

class Magestore_Storepickup_Helper_Url extends Mage_Core_Helper_Abstract {

    public function getResponseBody($url) {
        if (ini_get('allow_url_fopen') != 1) {
            @ini_set('allow_url_fopen', '1');
        }

        if (ini_get('allow_url_fopen') != 1) {
            $ch = curl_init();
            if (preg_match('/^https:/i', $url)) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            }
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            $contents = curl_exec($ch);
            curl_close($ch);
        } else {
            $contents = file_get_contents($url);
        }

        return $contents;
    }

    public function getStoreViewUrl($storeName, $id) {
        $allStores = Mage::app()->getStores();
        $storepickup = Mage::getModel('storepickup/store')
                ->load($id);
         $url_suffix = Mage::getStoreConfig('catalog/seo/product_url_suffix', Mage::app()->getStore()->getStoreId());
       
         foreach ($allStores as $_eachStoreId => $val) {
       
        $request_path = 'storepickup/' . $storeName .'.'. $url_suffix;

       
            $rewrite = Mage::getModel('core/url_rewrite')->getCollection()
                    ->addFieldToFilter('id_path', $storeName)
                    ->addFieldToFilter('store_id', $_eachStoreId)
                    ->getFirstItem();
            $request_path1=$rewrite->getRequestPath();
        
       
//        if(!$rewrite->getId()){
//            $rewrite->updateUrlKey();
//        }
          // Zend_debug::dump($rewrite->getData());die();     
        if ($storepickup->getUrlIdPath() != $storeName) {
            $storeName = $storepickup->getUrlIdPath();
            $storepickup->save();
            $request_path = 'storepickup/' . $storeName  . $url_suffix;
        }
        }
        return Mage::getUrl($request_path1, array("_secure" => true));
    }

    public function characterSpecial($character) {
        $character = iconv('UTF-8', 'ASCII//TRANSLIT', $character);
        $input = array("ñ", " ", "à", "á", "ạ", "ả", "ã", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ", "ă", "ằ", "ắ"
            , "ặ", "ẳ", "ẵ", "è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề", "ế", "ệ", "ể", "ễ", "ì", "í", "ị", "ỉ", "ĩ",
            "ò", "ó", "ọ", "ỏ", "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ"
            , "ờ", "ớ", "ợ", "ở", "ỡ",
            "ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ",
            "ỳ", "ý", "ỵ", "ỷ", "ỹ",
            "đ",
            "À", "Á", "Ạ", "Ả", "Ã", "Â", "Ầ", "Ấ", "Ậ", "Ẩ", "Ẫ", "Ă"
            , "Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ",
            "È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ",
            "Ì", "Í", "Ị", "Ỉ", "Ĩ",
            "Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ"
            , "Ờ", "Ớ", "Ợ", "Ở", "Ỡ",
            "Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ",
            "Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ",
            "Đ", "ê", "ù", "à", '.', '-', "'", "[À-Å]", "Æ", "Ç", "[È-Ë]", "/[Ì-Ï]/", "/Ð/", "/Ñ/", "/[Ò-ÖØ]/", "/×/", "/[Ù-Ü]/", "/[Ý-ß]/", "/[à-å]/", "/æ/", "/ç/", "/[è-ë]/", "/[ì-ï]/", "/ð/", "/ñ/", "/[ò-öø]/", "/÷/", "/[ù-ü]/", "/[ý-ÿ]/", "?");
        $output = array("n", "-", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a"
            , "a", "a", "a", "a", "a", "a",
            "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e",
            "i", "i", "i", "i", "i",
            "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o"
            , "o", "o", "o", "o", "o",
            "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u",
            "y", "y", "y", "y", "y",
            "d",
            "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A"
            , "A", "A", "A", "A", "A",
            "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E",
            "I", "I", "I", "I", "I",
            "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O"
            , "O", "O", "O", "O", "O",
            "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U",
            "Y", "Y", "Y", "Y", "Y",
            "D", "e", "u", "a", '-', '-', "", "A", "AE", "C", "E", "I", "D", "N", "O", "X", "U", "Y", "a", "ae", "c", "e", "i", "d", "n", "o", "x", "u", "y", "");

        return str_replace($input, $output, $character);
    }

}
