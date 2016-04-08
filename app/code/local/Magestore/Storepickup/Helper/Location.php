<?php
class Magestore_Storepickup_Helper_Location extends Mage_Core_Helper_Abstract
{

    public function getListCountry()
    {
        $listCountry = array();

        $collection = Mage::getResourceModel('directory/country_collection')
            ->loadByStore();

        if (count($collection)) {
            foreach ($collection as $item) {
                $listCountry[$item->getId()] = $item->getName();
            }
        }

        asort($listCountry);

        return $listCountry;
    }

    public function getOptionCountry()
    {
        $optionCountry = array();

        $collection = Mage::getResourceModel('directory/country_collection')
            ->loadByStore();

        if (count($collection)) {
            foreach ($collection as $item) {
                $optionCountry[] = array('value' => $item->getId(), 'label' => $item->getName());
            }
        }

        uasort($optionCountry, function ($a, $b) {
            if ($a['label'] == $b['label']) {
                return 0;
            }
            return $a['label'] < $b['label'] ? -1 : 1;
        });

        return $optionCountry;
    }

    public function getOptionLocation()
    {
        $options = array(array('value' => 0, 'label' => $this->__('None')));

        $list = $this->getListLocation();

        if (count($list)) {
            foreach ($list as $value => $label) {
                $options[] = array('value' => $value, 'label' => $label);
            }
        }

        return $options;
    }

    public function getTopStore($_stores, $num)
    {
        $tops = array();
        while (count($_stores) && $num) {
            $store = $this->_getTop($_stores);
            if ($store->getId()) {
                unset($_stores[$store->getData('index')]);
                $tops[] = $store;
                $num--;
            }
        }

        return $tops;
    }

    protected function _getTop($_stores)
    {
        $object = new Varien_Object();
        $object->setData('distance', 9999999999);
        foreach ($_stores as $index => $_store) {
            if ($_store->getData('distance') < $object->getData('distance')) {
                $object = $_store;
                $object->setData('index', $index);
            }
        }

        return $object;

    }

    public function toOptions($collection)
    {
        $options = '';

        $parent_id = '';
        $location = Mage::registry('location_data');
        if ($location) {
            $parent_id = $location->getParentId();
        }

        if (count($collection)) {
            foreach ($collection as $item) {
                $selected = '';
                if ($item->getId() == $parent_id) {
                    $selected = "selected";
                }

                $options .= '<option value="' . $item->getId() . '" ' . $selected . '>' . $item->getName() . '</option>';
            }
        }

        return $options;
    }
}
