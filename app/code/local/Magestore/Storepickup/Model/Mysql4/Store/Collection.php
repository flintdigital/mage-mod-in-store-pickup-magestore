<?php

class Magestore_Storepickup_Model_Mysql4_Store_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_store_id = null;
    protected $_addedTable = array();
	
	public function setStoreId($value){
		$this->_store_id = $value;
		return $this;
	}
	
	public function getStoreId(){
		return $this->_store_id;
	}
    
    public function _construct()
    {
        parent::_construct();
        if ($storeId = Mage::app()->getStore()->getId()) {
            $this->setStoreId($storeId);
        }
        $this->_init('storepickup/store');
    }
    
    protected function _afterLoad(){
    	parent::_afterLoad();
    	if ($storeId = $this->getStoreId()) {
            foreach ($this->_items as $item){
                $item->setStoreId($storeId)->loadStoreValue();
            }
        }
    	return $this;
    }
    
    public function addFieldToFilter($field, $condition=null) { 
        $attributes = array(
            'store_name',
            'status',
            'description',
            'address',
            'city',
        );
        $storeId = $this->getStoreId();
        if (in_array($field, $attributes) && $storeId) {
            if (!in_array($field, $this->_addedTable)) {
                $this->getSelect()
                    ->joinLeft(array($field => $this->getTable('storepickup/value')),
                        "main_table.store_id = $field.storepickup_id" .
                        " AND $field.store_id = $storeId" .
                        " AND $field.attribute_code = '$field'",
                        array()
                    );
                $this->_addedTable[] = $field;
            }
            return $this->addNonReturnedFilter($field, $condition);
        }
        if ($field == 'store_id') {
            $field = 'main_table.store_id';
        }
        return parent::addFieldToFilter($field, $condition);
    }
	public function addNonReturnedFilter($field,$condition)
	{
		/** @var Namespace_Module_Model_Resource_Model_Collection $this */
		$expression = 'IF('.$field.'.value IS NULL, main_table.'.$field.', '.$field.'.value)';
		$condition = $this->_getConditionSql($expression, $condition);
		$this->_select->where($condition);

		return $this;
	}
}