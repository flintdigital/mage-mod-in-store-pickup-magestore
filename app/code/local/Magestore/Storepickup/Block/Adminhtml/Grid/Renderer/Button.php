<?php

class Magestore_Storepickup_Block_Adminhtml_Grid_Renderer_Button extends Mage_Adminhtml_Block_Widget implements Varien_Data_Form_Element_Renderer_Interface
{
	protected $_element;
	
	/**
	 * constructor
	*/
	public function __construct(){
		$this->setTemplate('storepickup/renderer/button.phtml');
	}
	
	/*
	 * renderer
	*/
	public function render(Varien_Data_Form_Element_Abstract $element){
		$this->setElement($element);
		return $this->toHtml();
	}
	
	/**
	 * get and set element
	*/
	public function setElement(Varien_Data_Form_Element_Abstract $element){
		$this->_element = $element;
		return $this;
	}
	public function getElement(){
		return $this->_element;
	}
	
	/*
	 * get value of element
	*/
	public function getValues($id){
                return Mage::getModel('storepickup/image')->getCollection()->addFieldToFilter('store_id', $id)->addFieldToFilter('del', 2);		
	}
	
	/*
	 * get button's html to show
	*/
	public function getAddButtonHtml(){
		$button = $this->getLayout()->createBlock('adminhtml/widget_button')
			->setData(array(
				'label'	=> $this->__('Add Image'),
				'onclick'	=> 'return '.$this->getElement()->getName().'Control.addItem()',
				'class'	=> 'add'
			));
		$button->setName('add_'.$this->getElement()->getName().'_button');
		$this->setChild('add_button',$button);
		return $this->getChildHtml('add_button');
	}
	
	public function getStatuses(){
		return Mage::getSingleton('storepickup/status')->getOptionArray();
	}
}