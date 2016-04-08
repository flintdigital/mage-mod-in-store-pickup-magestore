<?php

class Magestore_Storepickup_Block_Adminhtml_Store_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	public function __construct() {
		parent::__construct();

		$this->_objectId = 'id';
		$this->_blockGroup = 'storepickup';
		$this->_controller = 'adminhtml_store';

		$this->_updateButton('save', 'label', Mage::helper('storepickup')->__('Save Store'));
		$this->_updateButton('delete', 'label', Mage::helper('storepickup')->__('Delete Store'));

		$this->_addButton('saveandcontinue', array(
			'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
			'onclick' => 'saveAndContinueEdit()',
			'class' => 'save',
		), -100);

		$this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
			//edit back button in import
			function backEdit()
			{
				window.history.back();
			}
                  function saveApplyForOtherDays(){
                        var status = $('monday_status').value;
                        var time_interval = $('monday_time_interval').value;
                        var open_hour = $('monday_open_hour').value;
                        var open_minute = $('monday_open_minute').value;

                         var close_hour = $('monday_close_hour').value;
                        var close_minute = $('monday_close_minute').value;

                         var open_hour_break=$('monday_open_break_hour').value;
                         var open_minute_break = $('monday_open_break_minute').value;

                        var close_hour_break = $('monday_close_break_hour').value;
                        var close_minute_break = $('monday_close_break_minute').value;
                        for(i=0;i<=6;i++) {
                            if( document.getElementsByClassName('status_day')[i])
                                document.getElementsByClassName('status_day')[i].value= status;
                            if(document.getElementsByClassName('time_interval'))
                                 document.getElementsByClassName('time_interval')[i].value= time_interval;
                            if(document.getElementsByClassName('hour_open'))
                                 document.getElementsByClassName('hour_open')[i].value= open_hour;
                            if(document.getElementsByClassName('minute_open'))
                                 document.getElementsByClassName('minute_open')[i].value= open_minute;
                            if(document.getElementsByClassName('hour_close'))
                                 document.getElementsByClassName('hour_close')[i].value= close_hour;
                            if(document.getElementsByClassName('minute_close'))
                                 document.getElementsByClassName('minute_close')[i].value= close_minute;

                            if(document.getElementsByClassName('hour_open_break'))
                                 document.getElementsByClassName('hour_open_break')[i].value= open_hour_break;
                            if(document.getElementsByClassName('minute_open_break'))
                                 document.getElementsByClassName('minute_open_break')[i].value= open_minute_break;
                            if(document.getElementsByClassName('hour_close_break'))
                                 document.getElementsByClassName('hour_close_break')[i].value= close_hour_break;
                            if(document.getElementsByClassName('minute_close_break'))
                                 document.getElementsByClassName('minute_close_break')[i].value= close_minute_break;
                        };
                        $$('.status_day').each(function(el){
                            hideCloseDate(el);
                        });
                        alert('" . Mage::helper('adminhtml')->__('Apply other store was successfully') . "');
                    }
        ";
	}

	public function getHeaderText() {
		if (Mage::registry('store_data') && Mage::registry('store_data')->getId()) {
			return Mage::helper('storepickup')->__("Edit store '%s'", $this->htmlEscape(Mage::registry('store_data')->getData('store_name')));
		} else {
			return Mage::helper('storepickup')->__('Add Store');
		}
	}
	public function removeButton($button_name) {
		$this->_removeButton($button_name);
	}

}