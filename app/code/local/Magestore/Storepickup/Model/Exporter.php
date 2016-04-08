<?php

class Magestore_Storepickup_Model_Exporter extends Varien_Object
{
	var $_fieldstr = 'store_name,store_manager,store_email,store_phone,store_fax,description,address,address_2,region,state,city,suburb,zipcode,country,store_latitude,store_longitude,monday_status,monday_time_interval,monday_open,monday_close,tuesday_status,tuesday_time_interval,tuesday_open,tuesday_close,wednesday_status,wednesday_time_interval,wednesday_open,wednesday_close,thursday_status,thursday_time_interval,thursday_open,thursday_close,friday_status,friday_time_interval,friday_open,friday_close,saturday_status,saturday_time_interval,saturday_open,saturday_close,sunday_status,sunday_time_interval,sunday_open,sunday_close,minimum_gap';
	
	public function exportStore()
	{
		$stores = Mage::getResourceModel('storepickup/store_collection');
		
		if(!count($stores))
			return false;
			
		foreach($stores as $store)
		{
			$data[] = $this->getStandData($store);
		}
		
		$csv = '';
		$csv .= $this->_fieldstr ."\n";

		foreach($data as $row)
		{
			$rowstr = implode('","',$row);
			$rowstr = '"'.$rowstr.'"';
			$csv .= $rowstr."\n";
		}
		
		return $csv;
	}
	
	public function getXmlStore()
	{
		$stores = Mage::getResourceModel('storepickup/store_collection');
		$storecollection = array();
		if(!count($stores))
			return false;
		
		foreach($stores as $store)
		{
			$data = $this->getStandData($store);
			$store->setData($data);
			$storecollection[] = $store;
		}
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml.= '<items>';
        foreach ($storecollection as $item) {
            $xml.= $item->toXml();
        }
        $xml.= '</items>';	
		
		return $xml;
	}
	
	public function getStandData($store)
	{
		$data = $store->getData();
		//prepare location
		$data['suburb'] = $store->getSuburb();
		$data['city'] = $store->getCity();
		$data['region'] = $store->getRegion();
		$data['state'] = $store->getState();
		$fields = $this->_getFields();
		
		$export_data = array();
		foreach($fields as $field)
		{
			$value = isset($data[$field]) ? $data[$field] : '';
			$export_data[$field] = $value;
		}
		
		return $export_data;
	}
	
	protected function _getFields()
	{
		if(! $this->getData('fields'))
		{
			$fields = explode(',',$this->_fieldstr);
			$this->setData('fields',$fields);
		}
		
		return $this->getData('fields');
	}
}