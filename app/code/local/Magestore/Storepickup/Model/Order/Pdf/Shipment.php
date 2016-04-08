<?php
class Magestore_Storepickup_Model_Order_Pdf_Shipment extends Mage_Sales_Model_Order_Pdf_Shipment {
	/**
	 *	override function insertOrder(). By Hai.Ta 4.4.2013
	 **/
	public function insertOrder(&$page, $obj, $putOrderId = true) {
		if ($obj instanceof Mage_Sales_Model_Order) {
			$shipment = null;
			$order = $obj;
		} elseif ($obj instanceof Mage_Sales_Model_Order_Shipment) {
			$shipment = $obj;
			$order = $shipment->getOrder();
		}
		if ($order->getShippingMethod() != 'storepickup_storepickup') {
			return parent::insertOrder($page, $obj, $putOrderId);
		}
		/* @var $order Mage_Sales_Model_Order */
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0.5));

		$page->drawRectangle(25, 790, 570, 755);

		$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
		$this->_setFontRegular($page);

		if ($putOrderId) {
			$page->drawText(Mage::helper('sales')->__('Order # ') . $order->getRealOrderId(), 35, 770, 'UTF-8');
		}
		//$page->drawText(Mage::helper('sales')->__('Order Date: ') . date( 'D M j Y', strtotime( $order->getCreatedAt() ) ), 35, 760, 'UTF-8');
		$page->drawText(Mage::helper('sales')->__('Order Date: ') . Mage::helper('core')->formatDate($order->getCreatedAtStoreDate(), 'medium', false), 35, 760, 'UTF-8');

		$page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
		$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
		$page->setLineWidth(0.5);
		$page->drawRectangle(25, 755, 275, 730);
		$page->drawRectangle(275, 755, 570, 730);

		/* Calculate blocks info */

		/* Billing Address */
		$billingAddress = $this->_formatAddress($order->getBillingAddress()->format('pdf'));

		/* Payment */
		$paymentInfo = Mage::helper('payment')->getInfoBlock($order->getPayment())
		                                      ->setIsSecureMode(true)
		                                      ->toPdf();
		$payment = explode('{{pdf_row_separator}}', $paymentInfo);
		foreach ($payment as $key => $value) {
			if (strip_tags(trim($value)) == '') {
				unset($payment[$key]);
			}
		}
		reset($payment);

		/* Shipping Address and Method */
		if (!$order->getIsVirtual()) {
			/* Shipping Address */
			$shippingAddress = $this->_formatAddress($order->getShippingAddress()->format('pdf'));

			$shippingMethod = $order->getShippingDescription();
		}

		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
		$this->_setFontRegular($page);
		$page->drawText(Mage::helper('sales')->__('SOLD TO:'), 35, 740, 'UTF-8');

		if (!$order->getIsVirtual()) {
			$page->drawText(Mage::helper('sales')->__('SHIP TO:'), 285, 740, 'UTF-8');
		} else {
			$page->drawText(Mage::helper('sales')->__('Payment Method:'), 285, 740, 'UTF-8');
		}

		if (!$order->getIsVirtual()) {
			$y = 730 - (max(count($billingAddress), count($shippingAddress)) * 10 + 5);
		} else {
			$y = 730 - (count($billingAddress) * 10 + 5);
		}

		$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
		$page->drawRectangle(25, 730, 570, $y);
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
		$this->_setFontRegular($page);
		$this->y = 720;

		foreach ($billingAddress as $value) {
			if ($value !== '') {
				$page->drawText(strip_tags(ltrim($value)), 35, $this->y, 'UTF-8');
				$this->y -= 10;
			}
		}

		if (!$order->getIsVirtual()) {
			$this->y = 720;
			foreach ($shippingAddress as $value) {
				if ($value !== '') {
					$page->drawText(strip_tags(ltrim($value)), 285, $this->y, 'UTF-8');
					$this->y -= 10;
				}

			}

			$page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
			$page->setLineWidth(0.5);
			$page->drawRectangle(25, $this->y, 275, $this->y - 25);
			$page->drawRectangle(275, $this->y, 570, $this->y - 25);

			$this->y -= 15;
			$this->_setFontBold($page);
			$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
			$page->drawText(Mage::helper('sales')->__('Payment Method'), 35, $this->y, 'UTF-8');
			$page->drawText(Mage::helper('sales')->__('Shipping Method:'), 285, $this->y, 'UTF-8');

			$this->y -= 10;
			$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));

			$this->_setFontRegular($page);
			$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

			$paymentLeft = 35;
			$yPayments = $this->y - 15;
		} else {
			$yPayments = 720;
			$paymentLeft = 285;
		}

		foreach ($payment as $value) {
			if (trim($value) !== '') {
				$page->drawText(strip_tags(trim($value)), $paymentLeft, $yPayments, 'UTF-8');
				$yPayments -= 10;
			}
		}

		if (!$order->getIsVirtual()) {
			$this->y -= 15;

			//start change
			$size_image_hight = 590;
			$shiipingInfo = explode("<br/>", $shippingMethod);
			$fix_i = 0;
			foreach ($shiipingInfo as $value) {
				if (strpos($value, '<img') === false) {
					if ($value) {
						$page->drawText($value, 285, $this->y - $fix_i * 10, 'UTF-8');
						$fix_i++;
					}
				} else {
					$url = strpbrk($value, "http");
					$url = explode('/>', $url);
					$url = rtrim($url[0]);
					$fix_i++;
				}
			}
			$this->y -= $fix_i * 10;
			if ($fix_i == 2) {
				$size_image_hight = 615;
			}

			try {
				$image = Mage::helper('storepickup/url')->getResponseBody($url);
				$baseImage = Mage::getBaseDir('media') . DS . 'storepickup' . DS . 'map' . DS . 'map.png';
				file_put_contents($baseImage, $image);
				if (is_file($baseImage)) {
					$image = Zend_Pdf_Image::imageWithPath($baseImage);
					$page->drawImage($image, 285, $this->y - 100, 400, $size_image_hight);
				}
			} catch (Exception $e) {
				$textDisconecnt = Mage::helper('storepickup')->__('Disconnect to Server . Please try againt');
				$page->drawText($textDisconecnt, 285, $this->y - 100, 'UTF-8');
			}

			$yShipments = $this->y;

			$totalShippingChargesText = "(" . Mage::helper('sales')->__('Total Shipping Charges') . " " . $order->formatPriceTxt($order->getShippingAmount()) . ")";

			$page->drawText($totalShippingChargesText, 285, $yShipments - 110, 'UTF-8');
			$yShipments -= 110;

			$tracks = array();
			if ($shipment) {
				$tracks = $shipment->getAllTracks();
			}
			if (count($tracks)) {
				$page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
				$page->setLineWidth(0.5);
				$page->drawRectangle(285, $yShipments, 510, $yShipments - 10);
				$page->drawLine(380, $yShipments, 380, $yShipments - 10);
				//$page->drawLine(510, $yShipments, 510, $yShipments - 10);

				$this->_setFontRegular($page);
				$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
				//$page->drawText(Mage::helper('sales')->__('Carrier'), 290, $yShipments - 7 , 'UTF-8');
				$page->drawText(Mage::helper('sales')->__('Title'), 290, $yShipments - 7, 'UTF-8');
				$page->drawText(Mage::helper('sales')->__('Number'), 385, $yShipments - 7, 'UTF-8');

				$yShipments -= 17;
				$this->_setFontRegular($page, 6);
				foreach ($tracks as $track) {

					$CarrierCode = $track->getCarrierCode();
					if ($CarrierCode != 'custom') {
						$carrier = Mage::getSingleton('shipping/config')->getCarrierInstance($CarrierCode);
						$carrierTitle = $carrier->getConfigData('title');
					} else {
						$carrierTitle = Mage::helper('sales')->__('Custom Value');
					}

					//$truncatedCarrierTitle = substr($carrierTitle, 0, 35) . (strlen($carrierTitle) > 35 ? '...' : '');
					$truncatedTitle = substr($track->getTitle(), 0, 45) . (strlen($track->getTitle()) > 45 ? '...' : '');
					//$page->drawText($truncatedCarrierTitle, 285, $yShipments , 'UTF-8');
					$page->drawText($truncatedTitle, 300, $yShipments, 'UTF-8');
					$page->drawText($track->getNumber(), 395, $yShipments, 'UTF-8');
					$yShipments -= 7;
				}
			} else {
				$yShipments -= 7;
			}

			$currentY = min($yPayments, $yShipments);
			// replacement of Shipments-Payments rectangle block
			$page->drawLine(25, $this->y + 15, 25, $currentY);
			$page->drawLine(25, $currentY, 570, $currentY);
			$page->drawLine(570, $currentY, 570, $this->y + 15);

			$this->y = $currentY;
			$this->y -= 15;
		}
	}

	/**
	 * Draw header for item table
	 *
	 * @param Zend_Pdf_Page $page
	 * @return void
	 */
	protected function _drawHeader(Zend_Pdf_Page $page) {
		/* Add table head */
		$this->_setFontRegular($page, 10);
		$page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
		$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
		$page->setLineWidth(0.5);
		$page->drawRectangle(25, $this->y, 570, $this->y - 15);
		$this->y -= 10;
		$page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));

		//columns headers
		$lines[0][] = array(
			'text' => Mage::helper('sales')->__('Products'),
			'feed' => 35,
		);

		$lines[0][] = array(
			'text' => Mage::helper('sales')->__('SKU'),
			'feed' => 290,
			'align' => 'right',
		);

		$lines[0][] = array(
			'text' => Mage::helper('sales')->__('Qty'),
			'feed' => 435,
			'align' => 'right',
		);

		$lines[0][] = array(
			'text' => Mage::helper('sales')->__('Price'),
			'feed' => 360,
			'align' => 'right',
		);

		$lines[0][] = array(
			'text' => Mage::helper('sales')->__('Tax'),
			'feed' => 495,
			'align' => 'right',
		);

		$lines[0][] = array(
			'text' => Mage::helper('sales')->__('Subtotal'),
			'feed' => 565,
			'align' => 'right',
		);

		$lineBlock = array(
			'lines' => $lines,
			'height' => 5,
		);

		$this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
		$this->y -= 20;
	}

	/**
	 * Return PDF document
	 *
	 * @param  array $shipments
	 * @return Zend_Pdf
	 */
	public function getPdf($shipments = array()) {
		$this->_beforeGetPdf();
		$this->_initRenderer('shipment');

		$pdf = new Zend_Pdf();
		$this->_setPdf($pdf);
		$style = new Zend_Pdf_Style();
		$this->_setFontBold($style, 10);
		foreach ($shipments as $shipment) {
			if ($shipment->getStoreId()) {
				Mage::app()->getLocale()->emulate($shipment->getStoreId());
				Mage::app()->setCurrentStore($shipment->getStoreId());
			}
			$page = $this->newPage();
			$order = $shipment->getOrder();
			/* Add image */
			$this->insertLogo($page, $shipment->getStore());
			/* Add address */
			$this->insertAddress($page, $shipment->getStore());
			/* Add head */
			$this->insertOrder(
				$page,
				$shipment,
				Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID, $order->getStoreId())
			);
			/* Add document text and number */
			$this->insertDocumentNumber(
				$page,
				Mage::helper('sales')->__('Packingslip # ') . $shipment->getIncrementId()
			);
			/* Add table */
			$this->_drawHeader($page);
			/* Add body */
			foreach ($shipment->getAllItems() as $item) {
				// var_dump($item->getData());
				if ($item->getOrderItem()->getParentItem()) {
					continue;
				}
				/* Draw item */
				$this->_drawItem($item, $page, $order);
				$page = end($pdf->pages);
			}
			$invoice = $shipment->getOrder()->getInvoiceCollection()->getLastItem();
			/* Add totals */
			$this->insertTotals($page, $invoice);
		}
		$this->_afterGetPdf();
		if ($shipment->getStoreId()) {
			Mage::app()->getLocale()->revert();
		}
		return $pdf;
	}
}