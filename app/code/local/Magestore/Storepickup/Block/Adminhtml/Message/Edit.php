<?php

class Magestore_Storepickup_Block_Adminhtml_Message_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'storepickup';        
        $this->_controller = 'adminhtml_message';        
       // $this->_updateButton('save', 'label', Mage::helper('storepickup')->__('Save Item'));
       // $this->_updateButton('delete', 'label', Mage::helper('storepickup')->__('Delete Message'));	
        $this->removeButton('save');
        $this->removeButton('delete');
        $this->removeButton('reset'); 
        $id = Mage::registry('store_data')->getId(); 
        $model = Mage::getModel('storepickup/message');
        $idd = $model->load($id)->getData('store_id');       
        $url = "'";
        $url .=  $this->getUrl('*/storepickup_store/edit', array('id' => $idd));
        $url .= "'";
        $this->_updateButton('back', 'onclick' ,'setLocation('.$url.')');
    }
    public function getUrlViewStore(){
        
    }
    public function getHeaderText()
    {
        if( Mage::registry('store_data') && Mage::registry('store_data')->getId() ) {
            return Mage::helper('storepickup')->__("Edit Message from %s",$this->htmlEscape(Mage::registry('store_data')->getData('name')));
        } else {
            return Mage::helper('storepickup')->__('Add Store');
        }
    }
	public function removeButton($button_name)
	{
		$this->_removeButton($button_name);
	}
	
}