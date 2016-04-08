<?php

class Magestore_Storepickup_Model_Source_Listsearchtabs{
    public function toOptionArray(){
	return array(
            0   => array(
                        'value'=> 0,
                        'label' => 'Search by distance'
                    ),
            1   => array(
                        'value'=> 1,
                        'label' => 'Search by area'
                    ),
//            2   => array(
//                        'value'=> 2,
//                        'label' => 'Search by date'
//                    ),
        );
    }
}