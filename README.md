# mage-mod-in-store-pickup-magestore
Magento 1.9 Instore Pickup Module by MageStore: http://www.magestore.com/magento-store-pickup-extension.html

### Installation
`modgit add mage-mod-in-store-pickup-magestore git@github.com:flintdigital/mage-mod-in-store-pickup-magestore.git`

### Configuration
See: http://www.magestore.com/knowledge-base/article/magento-store-pickup/

### Zebra Compatability
There are several things that have been changed in this module for Zebra Compatibility. 

* Removed the storepickup_support.css in `/Users/nateflint/Sites/mage-mod-in-store-pickup-magestore/app/design/frontend/base/default/layout/storepickup.xml`

* Added pre dispatch to config.xml:
````
<!-- for MD One Step Checkout.  -->
          <controller_action_predispatch_onestepcheckout_index_save_shipping>
                <observers>
                    <md_onestepcheckout_index_save_shipping>
                        <type>singleton</type>
                        <class>storepickup/observer</class>
                        <method>update_shippingaddress</method>
                    </md_onestepcheckout_index_save_shipping>
                </observers>
            </controller_action_predispatch_onestepcheckout_index_save_shipping>
````
* Updated the `Magestore_Storepickup_Model_Observer->update_shippingaddress()` to work with the MD onestep checkout. 
* Updated the `shipping_method.phtml` to change the save address click event.  https://github.com/flintdigital/mage-mod-osc-magedrive/commit/d4b62fb6026881283ff9c8dc09a0905f39dda6dc
