<?php
/**
 * Config Fieldset Block
 *
 * @category    Magestore
 *
 * @package     Magestore_Storepickup
 *
 * @author      Magestore Developer
 */
class Magestore_Storepickup_Block_Adminhtml_System_Config_Form_Fieldset
 extends Mage_Adminhtml_Block_System_Config_Form_Fieldset {

	public function render(Varien_Data_Form_Element_Abstract $element) {
		$html = parent::render($element);

		if ($storepickup = $this->getRequest()->getParam('storepickup')) {
			$html .= Mage::helper('adminhtml/js')->getScript('
				;
				Event.observe(window,"load",function(){
					if(config_edit_form) {
                	config_edit_form.select(".open").invoke("click");
					}
	                if($("carriers_storepickup-head") && !$("carriers_storepickup-head").hasClassName("open")){
	                    $("carriers_storepickup-head").click();
	                }
				});
				'
			);
		}
		return $html;
	}
}
?>
