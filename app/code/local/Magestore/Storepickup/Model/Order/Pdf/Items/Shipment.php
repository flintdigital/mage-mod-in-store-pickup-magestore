<?php
class Magestore_Storepickup_Model_Order_Pdf_Items_Shipment extends Mage_Sales_Model_Order_Pdf_Items_Abstract {

	/**
	 * Get array of arrays with item prices information for display in PDF
	 * array(
	 *  $index => array(
	 *      'label'    => $label,
	 *      'price'    => $price,
	 *      'subtotal' => $subtotal
	 *  )
	 * )
	 * @return array
	 */
	public function getItemPricesForDisplay() {
		$order = $this->getOrder();
		$item = $this->getItem();

		if (Mage::helper('tax')->displaySalesBothPrices()) {
			$prices = array(
				array(
					'label' => Mage::helper('tax')->__('Excl. Tax') . ':',
					'price' => $order->formatPriceTxt($item->getPrice()),
					'subtotal' => $order->formatPriceTxt($item->getOrderItem()->getRowTotal()),
				),
				array(
					'label' => Mage::helper('tax')->__('Incl. Tax') . ':',
					'price' => $order->formatPriceTxt($item->getOrderItem()->getPriceInclTax()),
					'subtotal' => $order->formatPriceTxt($item->getOrderItem()->getRowTotalInclTax()),
				),
			);
		} elseif (Mage::helper('tax')->displaySalesPriceInclTax()) {
			$prices = array(array(
				'price' => $order->formatPriceTxt($item->getOrderItem()->getPriceInclTax()),
				'subtotal' => $order->formatPriceTxt($item->getOrderItem()->getRowTotalInclTax()),
			));
		} else {
			$prices = array(array(
				'price' => $order->formatPriceTxt($item->getPrice()),
				'subtotal' => $order->formatPriceTxt($item->getOrderItem()->getRowTotal()),
			));
		}
		return $prices;
	}

	/**
	 * Draw item line
	 */
	public function draw() {
		$order = $this->getOrder();
		$item = $this->getItem();
		$pdf = $this->getPdf();
		$page = $this->getPage();
		$lines = array();

		// draw Product name
		$lines[0] = array(array(
			'text' => Mage::helper('core/string')->str_split($item->getName(), 35, true, true),
			'feed' => 35,
		));

		// draw SKU
		$lines[0][] = array(
			'text' => Mage::helper('core/string')->str_split($this->getSku($item), 17),
			'feed' => 290,
			'align' => 'right',
		);

		// draw QTY
		$lines[0][] = array(
			'text' => $item->getQty() * 1,
			'feed' => 435,
			'align' => 'right',
		);

		// draw item Prices
		$i = 0;
		$prices = $this->getItemPricesForDisplay();
		$feedPrice = 395;
		$feedSubtotal = $feedPrice + 170;
		foreach ($prices as $priceData) {
			if (isset($priceData['label'])) {
				// draw Price label
				$lines[$i][] = array(
					'text' => $priceData['label'],
					'feed' => $feedPrice,
					'align' => 'right',
				);
				// draw Subtotal label
				$lines[$i][] = array(
					'text' => $priceData['label'],
					'feed' => $feedSubtotal,
					'align' => 'right',
				);
				$i++;
			}
			// draw Price
			$lines[$i][] = array(
				'text' => $priceData['price'],
				'feed' => $feedPrice,
				'font' => 'bold',
				'align' => 'right',
			);
			// draw Subtotal
			$lines[$i][] = array(
				'text' => $priceData['subtotal'],
				'feed' => $feedSubtotal,
				'font' => 'bold',
				'align' => 'right',
			);
			$i++;
		}

		// draw Tax
		$lines[0][] = array(
			'text' => $order->formatPriceTxt($item->getOrderItem()->getTaxAmount()),
			'feed' => 495,
			'font' => 'bold',
			'align' => 'right',
		);

		// custom options
		$options = $this->getItemOptions();
		if ($options) {
			foreach ($options as $option) {
				// draw options label
				$lines[][] = array(
					'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 40, true, true),
					'font' => 'italic',
					'feed' => 35,
				);

				if ($option['value']) {
					if (isset($option['print_value'])) {
						$_printValue = $option['print_value'];
					} else {
						$_printValue = strip_tags($option['value']);
					}
					$values = explode(', ', $_printValue);
					foreach ($values as $value) {
						$lines[][] = array(
							'text' => Mage::helper('core/string')->str_split($value, 30, true, true),
							'feed' => 40,
						);
					}
				}
			}
		}

		$lineBlock = array(
			'lines' => $lines,
			'height' => 20,
		);

		$page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
		$this->setPage($page);
	}
}
