<?php

/**
 * Catalog product form gallery content
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magestore_Storepickup_Block_Adminhtml_Gallery_Content extends Mage_Adminhtml_Block_Widget {

    public function __construct() {
        parent::__construct();
    }

    protected function _prepareLayout() {
        $this->setChild('uploader', $this->getLayout()->createBlock('adminhtml/media_uploader')
        );

        /*
         *
         *
         * BEGIN Fix for SUPEE 8788 incompatibility issue
         * http://magento.stackexchange.com/questions/142006/issue-in-admin-panel-after-supee-patch-8788-installation/142013#142013
         *
         *
         * */
        $url = Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/adminhtml_gallery/upload');

        if (class_exists("Mage_Uploader_Block_Abstract")) {
            // PATCH SUPEE-8788 or Magento 1.9.3
            $this->getUploader()->getUploaderConfig()
                ->setFileParameterName('image')
                ->setTarget($url);

            $browseConfig = $this->getUploader()->getButtonConfig();
            $browseConfig
                ->setAttributes(
                    array("accept"  =>  $browseConfig->getMimeTypesByExtensions('gif, png, jpeg, jpg'))
                );
        } else {
            $this->getUploader()->getConfig()
                ->setUrl($url)
                ->setFileField('image')
                ->setFilters(array(
                    'images' => array(
                        'label' => Mage::helper('adminhtml')->__('Images (.gif, .jpg, .png)'),
                        'files' => array('*.gif', '*.jpg','*.jpeg', '*.png')
                    )
                ));
        }

        /*
         *
         *
         * END Fix for SUPEE 8788 incompatibility issue
         * http://magento.stackexchange.com/questions/142006/issue-in-admin-panel-after-supee-patch-8788-installation/142013#142013
         *
         *
         * */

        return parent::_prepareLayout();
    }

    /**
     * Retrive uploader block
     *
     * @return Mage_Adminhtml_Block_Media_Uploader
     */
    public function getUploader() {
        return $this->getChild('uploader');
    }

    /**
     * Retrive uploader block html
     *
     * @return string
     */
    public function getUploaderHtml() {
        return $this->getChildHtml('uploader');
    }

    public function getJsObjectName() {
        return $this->getHtmlId() . 'JsObject';
    }

    public function getAddImagesButton() {
        return $this->getButtonHtml(
                        Mage::helper('storepickup')->__('Add New Images'), $this->getJsObjectName() . '.showUploader()', 'add', $this->getHtmlId() . '_add_images_button'
        );
    }

    public function getImagesJson() {
        $id = $this->getRequest()->getParam('id');
        $collections = Mage::getModel('storepickup/image')->getCollection()->addFilter('store_id', $id);
        $collections->setOrder('options', 'ASC');
        $image = array();
        $i = 0;
        foreach ($collections as $obj) {
            $image[$i]['value_id'] = $obj->getImageId();
            $image[$i]['file'] = $obj->getName();
            $image[$i]['label'] = '';
            $image[$i]['position'] = $obj->getOptions();
            $image[$i]['disabled'] = '';
            $image[$i]['label_default'] = '';
            $image[$i]['position_default'] = '';
            $image[$i]['base_default'] = '';
            $image[$i]['url'] = Mage::getSingleton('storepickup/system_config_upload')->getMediaUrl($obj->getName());
            $i++;
        }
        return Mage::helper('core')->jsonEncode($image);
    }

    public function getImagesValuesJson() {
        $values = array();
         $id = $this->getRequest()->getParam('id');
          $collections = Mage::getModel('storepickup/image')->getCollection()
                  ->addFilter('store_id', $id)
                  ->addFilter('statuses', 1);
           foreach ($collections as $obj) {
               $values['base'] = $obj->getName();
           }
        return Mage::helper('core')->jsonEncode($values);
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getImageTypes() {
        return array('base' => array('label' => 'Base', 'field' => 'storepickup[base_image]'));
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getMediaAttributes() {
        return array();
        return $this->getElement()->getDataObject()->getMediaAttributes();
    }

    public function getImageTypesJson() {
        return Mage::helper('core')->jsonEncode($this->getImageTypes());
    }

}
