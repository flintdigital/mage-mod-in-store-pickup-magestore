<?php
class Magestore_Storepickup_Model_Adminhtml_Comment
{
    public function getCommentText(){
        $comment = 'To register a Google Map API key, please follow the guide <a href="'.Mage::getBlockSingleton('adminhtml/widget')->getUrl('*/storepickup_guide/index').'">here</a>';
        return $comment;
    }
}
