<?php

class Magestore_Storepickup_Block_Adminhtml_Store_Edit_Tab_Message extends Mage_Adminhtml_Block_Widget_Grid {

	public function __construct() {
		parent::__construct();
		$this->setId('messageGrid');
		$this->setDefaultSort('message_id');
		//$this->setDefaultFilter(array('in_messages'=>0));
		$this->setUseAjax(true);
		$this->setSaveParametersInSession(false);
		// var_dump($this->getUrl('storepickup/adminhtml_message/editmessage'));die();
	}

	protected function _addColumnFilterToCollection($column) {
		// Set custom filter for in category flag
		if ($column->getId() == 'in_messages') {
			$messageIds = $this->getSelectedMessage();
			if (empty($messageIds)) {
				$messageIds = 0;
			}
			if ($column->getFilter()->getValue()) {
				$this->getCollection()->addFieldToFilter('message_id', array('in' => $messageIds));
			} elseif (!empty($messageIds)) {
				$this->getCollection()->addFieldToFilter('message_id', array('nin' => $messageIds));
			}
		} else {
			parent::_addColumnFilterToCollection($column);
		}
		return $this;
	}

	protected function _prepareCollection() {
		$collection = Mage::getModel('storepickup/message')->getCollection()->addFieldToFilter('store_id', $this->getRequest()->getParam('id'));
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns() {
		$this->addColumn('in_message', array(
			'header_css_class' => 'a-center',
			'type' => 'checkbox',
			'field_name' => 'in_message',
//            'values'            => $this->getSelectedMessage(),
			'align' => 'center',
			'index' => 'message_id',
		));
		$this->addColumn('message_id', array(
			'header' => Mage::helper('storepickup')->__('ID'),
			'sortable' => true,
			'width' => '60',
			'index' => 'message_id',
		));
		$this->addColumn('name', array(
			'header' => Mage::helper('storepickup')->__('Name'),
			'width' => '180',
			'index' => 'name',
		));
		$this->addColumn('email', array(
			'header' => Mage::helper('storepickup')->__('Email'),
			'width' => '180',
			'index' => 'email',
			'renderer' => 'storepickup/adminhtml_grid_renderer_email',
		));
		$this->addColumn('message', array(
			'header' => Mage::helper('storepickup')->__('Message'),
			'index' => 'message',
		));
		$this->addColumn('date_sent', array(
			'header' => Mage::helper('storepickup')->__('Contact On'),
			'index' => 'date_sent',
			'type' => 'datetime',
		));
		//$this->addButton('*/*/messageDelete', Mage::helper('storepickup')->__('CSV'));
		return parent::_prepareColumns();
	}
	public function getSelectedMessage() {
		$messages = Mage::getModel('storepickup/message')->getCollection()
		                                                 ->addFieldToFilter('message_id', $this->getRequest()->getParam('id'));
		$messageIds = array();
		if (count($messages)) {
			foreach ($messages as $message) {
				$messageIds[] = $message->getId();
			}
		}

		return $messageIds;
	}
	public function getGridUrl() {
		return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/messagegrid', array('_current' => true));
	}
	public function getRowUrl($row) {

		return $this->getUrl('*/storepickup_message/editmessage', array('id' => $row->getId()));
	}
}