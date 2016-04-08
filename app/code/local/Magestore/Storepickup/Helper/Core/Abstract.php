<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magestore
 * @package     Magestore_Storepickup
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rewrite Abstract helper
 *
 * @author      Magestore team
 */
class Magestore_Storepickup_Helper_Core_Abstract extends Mage_Core_Helper_Data
{
   
    /**
     * Escape html entities
     *
     * @param   mixed $data
     * @param   array $allowedTags
     * @return  mixed
     */
    public function escapeHtml($data, $allowedTags = null)
    {
		$title = Mage::getStoreConfig('carriers/storepickup/title',Mage::app()->getStore()->getStoreId());
        $checkImageTag = strpos($data,'maps.google.com');
        $checkBR = strpos($data,$title.' - Free') ;        
        if ($checkBR || $checkImageTag) {    
            return $data;
        }
        return parent::escapeHtml($data, $allowedTags);
    }

    
}
