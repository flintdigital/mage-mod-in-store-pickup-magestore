<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category     Magestore
 * @package     Magestore_Storelocator
 *
 * @copyright     Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Storelocator Helper
 *
 * @category     Magestore
 * @package     Magestore_Storepickup
 *
 * @author      Magestore Developer
 */
class Magestore_Storepickup_Helper_Image extends Mage_Core_Helper_Abstract {

    public function saveImageStore($value, $base, $storeId) {
        if (!is_array($value) && strlen($value) > 0) {
            $value = Mage::helper('core')->jsonDecode($value);
        }
        if (!is_array($value)) {
            $value = array();
        }

        if (!is_array($base) && strlen($base) > 0) {
            $base = Mage::helper('core')->jsonDecode($base);
        }
        if (!isset($base['base'])) {
            $base['base'] = '';
        }

        foreach ($value as $image) {

            if (isset($image['removed']) && $image['removed'] == 1) {

                if (!isset($image['value_id'])) {
                    $path = $this->_getConfig()->getTmpMediaPath($image['file']);
                    $path = trim($path, '.tmp');
                } else {
                    $path = $this->_getConfig()->getMediaPath($image['file']);
                }

                try {
                    if (file_exists($path)) {
                        unlink($path);
                    }
                } catch (Exception $e) {

                }
                if (isset($image['value_id'])) {
                    $mod = Mage::getModel('storepickup/image');
                    $mod->setId($image['value_id'])
                        ->delete();
                }
            }
            if (!isset($image['value_id'])) {

                if (!isset($image['removed']) || (isset($image['removed']) && $image['removed'] != 1)) {
                    $newFile = $this->_moveImageFromTmp($image['file']);
                    $mod = Mage::getModel('storepickup/image');
                    $mod->setData('name', $newFile);
                    if ($base['base'] == $image['file']) {
                        $mod->setData('statuses', 1);
                    } else {
                        $mod->setData('statuses', 0);
                    }
                    $mod->setData('store_id', $storeId);
                    $mod->setData('options', $image['position']);
                    $mod->setData('del', 2);
                    $mod->save();
                }
            } else {
                $mod = Mage::getModel('storepickup/image');
                $mod->setData('image_id', $image['value_id']);
                if ($base['base'] == $image['file']) {
                    $mod->setData('statuses', 1);
                } else {
                    $mod->setData('statuses', 0);
                }
                $mod->save();
            }
        }
    }

    protected function _moveImageFromTmp($file) {
        $ioObject = new Varien_Io_File();
        $destDirectory = dirname($this->_getConfig()->getMediaPath($file));
        try {
            $ioObject->open(array('path' => $destDirectory));
        } catch (Exception $e) {
            $ioObject->mkdir($destDirectory, 0777, true);
            $ioObject->open(array('path' => $destDirectory));
        }

        if (strrpos($file, '.tmp') == strlen($file) - 4) {
            $file = substr($file, 0, strlen($file) - 4);
        }
        $destFile = $this->_getUniqueFileName($file, $ioObject->dirsep());

        $ioObject->mv(
            $this->_getConfig()->getTmpMediaPath($file), $this->_getConfig()->getMediaPath($destFile)
        );

        return str_replace($ioObject->dirsep(), '/', $destFile);
    }

    protected function _getUniqueFileName($file, $dirsep) {

        $destFile = dirname($file) . $dirsep
        . Mage_Core_Model_File_Uploader::getNewFileName($this->_getConfig()->getMediaPath($file));

        return $destFile;
    }

    protected function _getConfig() {

        return Mage::getSingleton('storepickup/system_config_upload');
    }

}
