<?php
/**
 * Copyright (c) 2011 Payment Network AG
 *
 * $Date: 2011-12-07 10:12:45 +0100 (Wed, 07 Dec 2011) $
 * @version SofortLib 1.3.0  $Id: class.invoice.inc.php 2584 2011-12-07 09:12:45Z rotsch $
 * @author Payment Network AG http://www.payment-network.com (integration@sofort.com)
 *
 */

//used for comparison of shopsystem with pnag-server
define('REDUCE_QUANTITY_IN_SHOPSYSTEM', 	'REDUCE_QUANTITY_IN_SHOPSYSTEM');
define('REDUCE_QUANTITY_ON_PNAG_SERVER', 	'REDUCE_QUANTITY_ON_PNAG_SERVER');
define('REDUCE_UNITPRICE_IN_SHOPSYSTEM', 	'REDUCE_UNITPRICE_IN_SHOPSYSTEM');
define('REDUCE_UNITPRICE_ON_PNAG_SERVER', 	'REDUCE_UNITPRICE_ON_PNAG_SERVER');
define('DELETE_ITEM_IN_SHOPSYSTEM', 		'DELETE_ITEM_IN_SHOPSYSTEM');
define('DELETE_ITEM_ON_PNAG_SERVER', 		'DELETE_ITEM_ON_PNAG_SERVER');
define('UNKNOWN_COMPARISON_FAULT', 			'UNKNOWN_COMPARISON_FAULT');

/**
 * Abstraction of an invoice
 * Helper class to ease the use of "Rechnung by sofort"
 * Encapsulates Multipay, TransactionData and ConfirmSR to handle everything there is about "Rechnung by sofort"
 * @see SofortLib_Multipay
 * @see SofortLib_TransactionData
 * @see SofortLib_ConfirmSr
 */
class PnagInvoice extends PnagAbstractDocument {

	/**
	 *
	 * Multipay-Object to handle API calls
	 * @var object
	 * @private
	 */
	var $multipay = null;

	/**
	 * Object TransactionData to handle information about transactions
	 * @var object
	 * @private
	 */
	var $transactionData = null;

	/**
	 * Object Confirm_SR to handle sofortrechnung/rechnung by sofort items
	 * Handling of Sofortrechung
	 * @var object
	 * @private
	 */
	var $confirmSr = null;

	/**
	 *
	 * Some kind of a bitmask, first four elements, divider, next elements
	 * Every combination must be unique to represent a unique state
	 * @var array
	 * @private
	 */
	var $statusMask = array(
		'pending' => 1,
		'received' => 2,
		'refunded' => 4,
		'loss' => 8,
		// ^--Status--^ //
		'not_credited_yet' => 16,
		'not_credited' => 32,
		'refunded' => 64,
		'compensation' => 128,
		'credited' => 256,
		'canceled' => 512,
		'confirm_invoice' => 1024,
		'confirm_period_expired' => 2048,
		'wait_for_money' => 4096,
		'reversed' => 8192,
		'rejected' => 16384,
	);

	/**
	 *
	 * The invoices' status (might be one of pending, received, refunded, loss)
	 * @var string
	 * @private
	 */
	var $status = '';

	/**
	 *
	 * The invoices' payment status (might be one of not_credited_yet, not_credited, ...)
	 * @var string
	 * @private
	 */
	var $status_reason = '';

	/**
	 *
	 * transaction id
	 * @var string
	 * @private
	 */
	var $transactionId = '';

	/**
	 * api key given in project setup in payment network backend
	 * @var string
	 * @private
	 */
	var $apiKey = '';

	/**
	 *
	 * api url
	 * @var string
	 * @private
	 */
	var $apiUrl = '';

	/**
	 * time
	 * @var string
	 * @private
	 */
	var $time = '';

	/**
	 * payment method
	 * @var string
	 * @private
	 */
	var $payment_method = '';

	/**
	 * The resulting url to the invoice (PDF)
	 * @var string
	 * @private
	 */
	var $invoiceUrl = '';

	/**
	 *
	 * Constructor
	 * @param string $apiKey
	 * @param string $transactionId
	 * @param string $apiUrl
	 */
	function PnagInvoice($apiKey, $transactionId = '') {
		$this->transactionId = $transactionId;
		$this->apiKey = $apiKey;
		$apiUrl = (getenv('sofortApiUrl') != '') ? getenv('sofortApiUrl') : 'https://api.sofort.com/api/xml';
		$this->apiUrl = $apiUrl;

		$this->multipay = new SofortLib_Multipay($this->apiKey, $this->apiUrl);
		if($transactionId != '') {
			$this->transactionData = $this->_setupTransactionData();
			$this->confirmSr = $this->_setupConfirmSr();
		}

		return $this;
	}


	/**
	 * Setter for transactionId
	 * @param $transactionId
	 * @public
	 */
	function setTransactionId($transactionId) {
		$this->transactionId = $transactionId;
		$this->transactionData = $this->_setupTransactionData();
		$this->confirmSr = $this->_setupConfirmSr();
		return $this;
	}


	/**
	 * Construct the SofortLib_TransactionData object
	 * Collect every order's item and set it accordingly
	 * TransactionData is used encapsulated in this class to retrieve information about the order's details
	 * @return object SofortLib_TransactionData
	 * @private
	 */
	function _setupTransactionData() {

		$obj = new SofortLib_TransactionData($this->apiKey, $this->apiUrl);

		$this->_deleteLocalPnagArticles();  //delete "old" pnag-articles before updating them
		$response = $obj->setTransaction($this->transactionId);
		$response->sendRequest();
		if (!isset ($obj->response[0] ) ) {
			return false;
		} else {
			$transactionData = $obj->response[0];
		}

		$this->setStatus($transactionData['status']);
		$this->setStatusReason($transactionData['status_reason']);
		$this->setTransaction($this->transactionId);
		$this->setTime($transactionData['time']);
		$this->setPaymentMethod($transactionData['payment_method']);
		$this->setInvoiceUrl($transactionData['invoice_url']);

		if (isset ($transactionData['item'] ) ) {
			$itemArray = $transactionData['item'];
		} else {
			$itemArray = array();
		}
		// should there be any items, fetch them accordingly
		if(is_array($itemArray) && !empty($itemArray)) {
			foreach($itemArray as $item) {
				$this->setPnagArticle ($item['item_id'], $item['product_number'], $item['product_type'], $item['title'], $item['description'], $item['quantity'], $item['unit_price'], $item['tax']);
			}
		}

		return $obj;
	}


	/**
	 * Initialize SofortLib_ConfirmSR
	 * @private
	 * @return Object SofortLib_ConfirmSr
	 */
	function _setupConfirmSr() {
		$obj = new SofortLib_ConfirmSr($this->apiKey, $this->apiUrl);
		$obj->setTransaction($this->transactionId);

		return $obj;
	}


	/**
	 * Refreshes the TransactionData with the data directly from the pnag-server
	 * @return boolean
	 */
	function refreshTransactionData() {
		$this->transactionData = $this->_setupTransactionData();
		return true;
	}


	/**
	 * Enabling logging for all encapsed SofortLib components
	 * @public
	 * @return boolean
	 */
	function enableLog() {
		(is_a($this->multipay, 'SofortLib')) ? $this->multipay->enableLog() : '';
		(is_a($this->transactionData, 'SofortLib')) ? $this->transactionData->enableLog() : '';
		(is_a($this->confirmSr, 'SofortLib')) ? $this->confirmSr->enableLog() : '';
		return true;
	}


	/**
	 * Disable logging for all encapsed SofortLib components
	 * @public
	 * @return boolean
	 */
	function disableLog() {
		(is_a($this->multipay, 'SofortLib')) ? $this->multipay->disableLog() : '';
		(is_a($this->transactionData, 'SofortLib')) ? $this->transactionData->disableLog() : '';
		(is_a($this->confirmSr, 'SofortLib')) ? $this->confirmSr->disableLog() : '';
		return true;
	}


	/**
	 * Log the given String into log.txt
	 * Notice: logging must be enabled -> use enableLog();
	 * @param string $msg - Message to log
	 * @return bool - true=logged ELSE false=logging failed
	 * @public
	 */
	function log($msg){
		if (is_a($this->multipay, 'SofortLib')) {
			$this->multipay->log($msg);
			return true;
		} else if (is_a($this->transactionData, 'SofortLib')) {
			$this->transactionData->log($msg);
			return true;
		} else if (is_a($this->confirmSr, 'SofortLib')) {
			$this->confirmSr->log($msg);
			return true;
		}
		return false;  //logging failed
	}


	/**
	 * Log the given String into error_log.txt
	 * Notice: logging must be enabled -> use enableLog();
	 * @param string $msg - Message to log
	 * @return bool - true=logged ELSE false=logging failed
	 * @public
	 */
	function logError($msg){
		if (is_a($this->multipay, 'SofortLib')) {
			$this->multipay->logError($msg);
			return true;
		} else if (is_a($this->transactionData, 'SofortLib')) {
			$this->transactionData->logError($msg);
			return true;
		} else if (is_a($this->confirmSr, 'SofortLib')) {
			$this->confirmSr->logError($msg);
			return true;
		}
		return false;  //logging failed
	}


	/**
	 * Log the given String into warning_log.txt
	 * Notice: logging must be enabled -> use enableLog();
	 * @param string $msg - Message to log
	 * @return bool - true=logged ELSE false=logging failed
	 * @public
	 */
	function logWarning($msg){
		if (is_a($this->multipay, 'SofortLib')) {
			$this->multipay->logWarning($msg);
			return true;
		} else if (is_a($this->transactionData, 'SofortLib')) {
			$this->transactionData->logWarning($msg);
			return true;
		} else if (is_a($this->confirmSr, 'SofortLib')) {
			$this->confirmSr->logWarning($msg);
			return true;
		}
		return false;  //logging failed
	}


	/**
	 * Wrapper function for cancelling this invoice via multipay (SofortLib)
	 * @return Ambigious boolean/Array
	 * @todo fix returned value array, empty array
	 * @public
	 */
	function cancelInvoice($transactionId = '') {
		if($transactionId != '') {
			$this->transactionId = $transactionId;
			$this->confirmSr = $this->_setupConfirmSr();
		}
		if($this->confirmSr != null) {
			$this->confirmSr->cancelInvoice();
			$this->confirmSr->setComment('Vollstorno');
			$this->confirmSr->sendRequest();
			$this->transactionData = $this->_setupTransactionData();
			return $this->getErrors();
		}
		return false;
	}


	/**
	 * Wrapper function for confirming this invoice via multipay (SofortLib)
	 * @param $transactionId - optional parameter for confirming a transaction on the fly
	 * @return Ambigious boolean/Array
	 * @todo fix returned value array, empty array
	 * @public
	 */
	function confirmInvoice($transactionId = '') {
		if($transactionId != '') {
			$this->transactionId = $transactionId;
			$this->confirmSr = $this->_setupConfirmSr();
		}
		if($this->confirmSr != null) {
			$this->confirmSr->confirmInvoice();
			$this->confirmSr->setComment('Invoice confirmed');
			$this->confirmSr->sendRequest();
			$this->transactionData = $this->_setupTransactionData();
			return $this->getErrors();
		}
		return false;
	}

/* ########################## WRAPPER FUNCTIONS MULTIPAY ########################## */

	/**
	 * Wrapper for SofortLib_Multipay::addSofortrechnungItem
	 * @see SofortLib_Multipay
	 * @public
	 * @param $itemId - MUST be unique!
	 * @param $productNumber
	 * @param $title
	 * @param $unit_price - float precision 2
	 * @param $productType
	 * @param $description
	 * @param $quantity - int
	 * @param $tax
	 */
	function addItemToInvoice($itemId, $productNumber, $title, $unit_price, $productType = 0, $description = '', $quantity = 1, $tax = 19) {
		$unit_price = round($unit_price, 2);
		$this->multipay->addSofortrechnungItem($itemId, $productNumber, $title, $unit_price, $productType, $description, $quantity, $tax);
		//$item_id, $product_number = 0, $product_type = '-', $title = '', $description = '', $quantity = 0, $unit_price = '', $tax = '19'
		$this->setShopArticle($itemId, $productNumber, $productType, $title, $description, $quantity, $unit_price, $tax);
		// add the sum of each added product on invoice to total sum
		$this->setAmount($this->getAmount() + ($unit_price * $quantity), $this->currency);
	}


	/**
	 * Remove an item from the invoice
	 * @public
	 * @param $itemId
	 * @return boolean
	 */
	function removeItemfromInvoice($itemId) {
		$return = false;
		$i = 0;
		foreach($this->shopArticles as $item) {
			if($item->item_id == $itemId) {
				unset($this->shopArticles[$i]);
				$this->setAmount($this->getAmount() - $this->getItemAmount($itemId));
				$return = $this->multipay->removeSofortrechnungItem($itemId);
			}
			$i++;
		}
		return $return;
	}


	function updateInvoiceItem($itemId, $quantity, $unit_price) {
		$return = false;
		foreach($this->shopArticles as $item) {
			if($item->item_id == $itemId) {
				$oldPrice = $item->unit_price * $item->quantity;
				$item->unit_price = $unit_price;
				$item->quantity = $quantity;
				$newPrice = $unit_price * $quantity;
				$this->setAmount($this->getAmount() - $oldPrice + $newPrice);
				$return = $this->multipay->updateSofortrechnungItem($itemId, $quantity, $unit_price);
			}
		}
		return $return;
	}


	function getItemAmount($itemId) {
		return $this->multipay->getSofortrechnungItemAmount($itemId);
	}


	/**
	 * Wrapper for SofortLib_Multipay::setSofortrechnungShippingAddress
	 * @see SofortLib_Multipay
	 * @public
	 * @param $firstname
	 * @param $lastname
	 * @param $street
	 * @param $streetNumber
	 * @param $zipcode
	 * @param $city
	 * @param $salutation
	 * @param $country
	 */
	function addShippingAddresss($firstname, $lastname, $street, $streetNumber, $zipcode, $city, $salutation, $country = 'DE') {
		$this->multipay->setSofortrechnungShippingAddress($firstname, $lastname, $street, $streetNumber, $zipcode, $city, $salutation, $country);
	}


	/**
	 * Wrapper for SofortLib_Multipay::setSofortrechnungInvoiceAddress
	 * @see SofortLib_Multipay
	 * @public
	 * @param $firstname
	 * @param $lastname
	 * @param $street
	 * @param $streetNumber
	 * @param $zipcode
	 * @param $city
	 * @param $salutation
	 * @param $country
	 */
	function addInvoiceAddress($firstname, $lastname, $street, $streetNumber, $zipcode, $city, $salutation, $country = 'DE') {
		$this->multipay->setSofortrechnungInvoiceAddress($firstname, $lastname, $street, $streetNumber, $zipcode, $city, $salutation, $country = 'DE');
	}


	/**
	 * Wrapper function for SofortLib_Multipay::setSofortrechnungOrderId
	 * @see SofortLib_Multipay
	 * @public
	 * @param $arg
	 */
	function setOrderId($arg) {
		$this->multipay->setSofortrechnungOrderId($arg);
	}


	/**
	 * Wrapper function for SofortLib_Multipay::setSofortrechnungCustomerId
	 * @public
	 * @param $arg
	 */
	function setCustomerId($arg) {
		$this->multipay->setSofortrechnungCustomerId($arg);
	}


	/**
	 * Wrapper function for SofortLib_Multipay::setPhoneNumberCustomer
	 * @public
	 * @param $arg
	 */
	function setPhoneNumberCustomer($arg) {
		$this->multipay->setPhoneNumberCustomer($arg);
	}


	/**
	 * Wrapper function for SofortLib_Multipay::setEmailCustomer
	 * @public
	 * @param $arg
	 */
	function setEmailCustomer($arg) {
		$this->multipay->setEmailCustomer($arg);
	}


	/**
	 * Wrapper function for SofortLib_Multipay::addUserVariable
	 * @public
	 * @param $arg
	 */
	function addUserVariable($arg) {
		$this->multipay->addUserVariable($arg);
	}


	/**
	 * Wrapper function for SofortLib_Multipay::setNotificationUrl
	 * @public
	 * @param $arg
	 */
	function setNotificationUrl($arg) {
		$this->multipay->setNotificationUrl($arg);
	}


	/**
	 * Wrapper function for SofortLib_Multipay::setAbortUrl
	 * @public
	 * @param $arg
	 */
	function setAbortUrl($arg) {
		$this->multipay->setAbortUrl($arg);
	}


	/**
	 * Wrapper function for SofortLib_Multipay::setSuccessUrl
	 * @public
	 * @param $arg
	 */
	function setSuccessUrl($arg) {
		$this->multipay->setSuccessUrl($arg);
	}


	/**
	 * Wrapper function for SofortLib_Multipay::setReason
	 * @public
	 * @param $arg string
	 * @param $arg2 string
	 */
	function setReason($arg, $arg2 = '') {
		$this->multipay->setReason($arg, $arg2);
	}


	/**
	 * Wrapper function for SofortLib_Multipay::setAmount
	 * @public
	 * @param $arg float
	 * @param $currency string
	 */
	function setAmount($arg, $currency = 'EUR') {
		$this->multipay->setAmount($arg, $currency);
	}


	/**
	 * current total amount of the given order-articles
	 * @return float - sum (price, total) of all articles
	 */
	function getAmount() {
		if(isset($this->multipay->parameters['amount'])) {
			return $this->multipay->parameters['amount'];
		}
		return 0.0;
	}


	/**
	 * @deprecated
	 * Set the time for payment
	 * @param int $arg - days
	 */
	function setTimeForPayment($arg) {
		$this->time = $arg;
		$this->multipay->setSofortrechnungTimeForPayment($arg);
		return $this;
	}


	/**
	 * get the endprice (total) of all items at pnag
	 * @public
	 * @return float if ok else -1
	 */
	function getPnagTotal() {
		if(is_a($this->transactionData, 'SofortLib')) {
			return $this->transactionData->getPnagTotal();
		} else {
			return -1;
		}
	}


	/**
	 * Wrapper function for SofortLib_Multipay::setSofortrechnung
	 * @public
	 */
	function setSofortrechnung() {
		$this->multipay->setSofortrechnung();
	}


	/**
	 * Wrapper function for SofortLib_Multipay::getPaymentUrl
	 * @public
	 * @return url string
	 */
	function getPaymentUrl() {
		return $this->multipay->getPaymentUrl();
	}


	/**
	 * Wrapper function for SofortLib_Multipay::getPaymentUrl
	 * @public
	 * @return url string
	 */
	function getTransactionId() {
		return $this->multipay->getTransactionId();
	}


	/**
	 * Wrapper function for SofortLib_Multipay::toXml
	 * @public
	 * @return xml
	 */
	function toXml() {
		return $this->multipay->toXml();
	}


	/**
	 * Check, if errors occured
	 * @public
	 * @return boolean
	 */
	function isError() {
		if ($this->multipay) {
			if ($this->multipay->isError('sr')) {
				return true;
			}
		} else if ($this->confirmSr) {
			if ($this->confirmSr->isError('sr')) {
				return true;
			}
		} else if ($this->transactionData) {
			if ($this->transactionData->isError('sr')) {
				return true;
			}
		}
		return false;
	}


	/**
	 * Check, if warnings occured
	 * @public
	 * @return boolean
	 */
	function isWarning() {
		if ($this->multipay) {
			if ($this->multipay->isWarning('sr')) {
				return true;
			}
		} else if ($this->confirmSr) {
			if ($this->confirmSr->isWarning('sr')) {
				return true;
			}
		} else if ($this->transactionData) {
			if ($this->transactionData->isWarning('sr')) {
				return true;
			}
		}
		return false;
	}


	/**
	 * returns one error (as String!)
	 */
	function getError() {
		if($this->multipay) {
			if ($this->multipay->isError('sr')) {
				return $this->multipay->getError('sr');
			}
		}
		if ($this->confirmSr) {
			if ($this->confirmSr->isError('sr')) {
				return $this->confirmSr->getError('sr');
			}
		}
		if ($this->transactionData) {
			if ($this->transactionData->isError('sr')) {
				return $this->transactionData->getError('sr');
			}
		}
		return '';
	}


	/**
	 * collect all errors and returns them
	 * @return array - all errors
	 * @public
	 */
	function getErrors() {
		$allErrors = array();
		if($this->multipay) {
			if ($this->multipay->isError('sr')) {
				$allErrors = array_merge ($this->multipay->getErrors('sr'), $allErrors);
			}
		}
		if ($this->confirmSr) {
			if ($this->confirmSr->isError('sr')) {
				$allErrors = array_merge ($this->confirmSr->getErrors('sr'), $allErrors);
			}
		}
		if ($this->transactionData) {
			if ($this->transactionData->isError('sr')) {
				$allErrors = array_merge ($this->transactionData->getErrors('sr'), $allErrors);
			}
		}
		return $allErrors;
	}


	/**
	 * @public
	 * collects all warnings and returns them
	 * @return array
	 */
	function getWarnings() {
		$allWarnings = array();
		if($this->multipay) {
			if ($this->multipay->isWarning('sr')) {
				$allWarnings = array_merge ($this->multipay->getWarnings('sr'), $allWarnings);
			}
		}
		if ($this->confirmSr) {
			if ($this->confirmSr->isWarning('sr')) {
				$allWarnings = array_merge ($this->confirmSr->getWarnings('sr'), $allWarnings);
			}
		}
		if ($this->transactionData) {
			if ($this->transactionData->isWarning('sr')) {
				$allWarnings = array_merge ($this->transactionData->getWarnings('sr'), $allWarnings);
			}
		}
		return $allWarnings;
	}


	/**
	 * Validate your parameters against API
	 * @return array - any validationerrors and -warnings
	 * @public
	 */
	function validateRequest() {
		$errorsAndWarnings = $this->multipay->validateRequest('sr');
		return $errorsAndWarnings;
	}


	/**
	 * send the order to pnag (-> buy your products)
	 * @return empty array if ok ELSE array with errors and/or warnings
	 * @public
	 */
	function checkout() {
		$this->multipay->sendRequest();
		// set the resulting transaction id
		$this->transactionId = $this->multipay->transactionId;
		$this->transactionData = $this->_setupTransactionData();

		$errors = array();
		if ($this->isError()) {
			$errors = $this->getErrors();
		}

		$warnings = array();
		if ($this->isWarning()) {
			$warnings = $this->getWarnings();
		}

		if (!empty($errors) &&  !empty($warnings)) {
			return array(); //no errors or warnings found
		} else {
			$returnArray = array();
			$returnArray['errors'] = $errors;
			$returnArray['warnings'] = $warnings;
			return $returnArray;
		}
	}


	function getTransactionInfo() {
		if(is_a($this->transactionData, 'SofortLib')) {
			$this->transactionData->setTransaction($this->transactionId);
			$this->sendRequest();
			return $this->transactionData->response;
		} else {
			$this->transactionData = $this->_setupTransactionData();
		}
		return array();
	}
/* ########################## WRAPPER FUNCTIONS MULTIPAY ########################## */


	/**
	 * Wrapper function for removing an article via multipay (SofortLib)
	 * Currently not implemented in PNAG API 06/2011
	 * @param $articleId int
	 * @public
	 * return array
	 */
	/*
	function removeArticle($articleId) {
		if($this->confirmSr != null) {
			$this->confirmSr->removeItem($articleId, -1);
			$this->confirmSr->confirmInvoice();
			$this->confirmSr->setComment('Article '.$articleId.' removed');
			$this->confirmSr->sendRequest();
			$this->_setupTransactionData();  //TODO --> $this->transactionData = $this->_setupTransactionData(); ???
			return $this->getErrors();
		}
		return array();
	}
	*/

	/**
	 * Wrapper function for changing the quantity of an article via multipay (SofortLib)
	 * Currently not implemented in PNAG API 06/2011
	 * @param $articleId int
	 * @param $quantity int
	 * @public
	 * @return array
	 */
	 /*
	function changeArticleQuantity($articleId, $quantity = 0) {
		if($this->confirmSr != null || $quantity < 1) {
			$this->confirmSr->removeItem($articleId, $quantity);
			$this->confirmSr->confirmInvoice();
			$this->confirmSr->setComment('Article '.$articleId.', changed quantity to: '.$quantity);
			$this->confirmSr->sendRequest();
			$this->_setupTransactionData();
			return $this->getErrors();
		}
		return array();
	}
	*/

	/**
	 * Check if differences between shop and PNAG exist
	 * An empty array is being returned if there are no differences at all.
	 * In any other case, an array containing differences is being returned
	 * @return array
	 * @public
	 */
	function checkPnagShopArticleDifferences() {

		$shopArticles = $this->shopArticles;
		$pnagArticles = $this->pnagArticles;

		//check if shop-articles are all in response and have the same params
		$notFoundInResponse = array();
		foreach ($shopArticles as $shopArticle){

			$itemId = $shopArticle->item_id;
			$quantity = $shopArticle->quantity;
			$unitPrice = $shopArticle->unit_price;

			$articleFound = false;
			$equalPnagArticle = array();
			foreach ($pnagArticles as $key => $pnagArticle){
				if ($pnagArticle->checkParams ($itemId, $quantity, $unitPrice) ) {
					$articleFound = true;
					break;
				}

				if ($pnagArticle->item_id == $itemId) {
					$equalPnagArticle = $pnagArticle;
					$pnagArticles[$key]->setDifferenceFound (1);
				}
			}
			if (!$articleFound){
				// Article was not found on PNAG, so we keep it
				$differentArticle = array();
				$differentArticle['foundInResponse'] = $equalPnagArticle;
				$differentArticle['foundInShop'] = $shopArticle;
				$notFoundInResponse[] = $differentArticle;
			}
		}
		//check if pnag-articles are all in shopsystem
		$notFoundInShopsystem = array();
		foreach ($pnagArticles as $article) {

			//check if some differences have been found before - would result in double entries
			if ($article->getDifferenceFound())
				continue;

			$itemId = $article->item_id;
			$quantity = $article->quantity;
			$unitPrice = $article->unit_price;

			$articleFound = false;
			$equalShopArticle = array();
			foreach ($shopArticles as $shopArticle){
				if ($shopArticle->checkParams ($itemId, $quantity, $unitPrice) ) {
					$articleFound = true;
					break;
				}

				if ($shopArticle->item_id == $itemId) {
					$equalShopArticle = $shopArticle;
				}
			}
			if (!$articleFound){
				$differentArticle = array();
				$differentArticle['foundInShop'] = $equalShopArticle;
				$differentArticle['foundInResponse'] = $article;
				$notFoundInShopsystem[] = $differentArticle;
			}
		}


		//no differences found -> return empty array
		if (count($notFoundInShopsystem) == 0 && count($notFoundInResponse) == 0 ) {
			return array();
		}

		//this array contains any differences
		$differences = array();

		// hints on how to solve differences
		foreach ($notFoundInResponse as $key => $value){
			$notFoundInResponse[$key]['syncAction'] = $this->_getDetailedDifferenceProblem ($value ['foundInResponse'], $value ['foundInShop'] );
			$differences[] = $value;
		}
		foreach ($notFoundInShopsystem as $key => $value){
			$notFoundInShopsystem[$key]['syncAction'] = $this->_getDetailedDifferenceProblem ($value ['foundInResponse'], $value ['foundInShop'] );
			$differences[] = $value;
		}
		return $differences;
	}


	/**
	 * needs two (nearly same) articles-objects and returns info, how to fix the sync-problem
	 * e.g. shoparticle has a quantity of 4 - pnag-article has a quantity of 6
	 * @param object - $responseArticle
	 * @param object - $shopsystemArticle
	 * @return string - predifined constants how to fix the problem (separated by comma)
	 * @private
	 */
	function _getDetailedDifferenceProblem ($responseArticle, $shopsystemArticle) {
		$return = '';  //includes 1 or more pre-defined constants to sync the data

		if (empty ($responseArticle) && empty ($shopsystemArticle) )
			$return .= ',' . UNKNOWN_COMPARISON_FAULT; //unknown fault

		//Article exists only on pnag-server and was deleted on shopsystem
		if (empty ($shopsystemArticle) && !empty ($responseArticle) )
			$return .= ',' . DELETE_ITEM_ON_PNAG_SERVER;

		//Article exists in shopsystem but not on pnag-server
		if (empty ($responseArticle) && !empty ($shopsystemArticle) )
			$return .= ',' . DELETE_ITEM_IN_SHOPSYSTEM;

		// two articles were given?
		if (is_object($shopsystemArticle) && is_a ($shopsystemArticle, 'PnagArticle')  &&
		    is_object($responseArticle)   && is_a ($responseArticle,   'PnagArticle') ) {

		    if ($shopsystemArticle->getItemId() != $responseArticle->getItemId() ) //for security - should normally be checked before call of this function
				$return .= ',' . UNKNOWN_COMPARISON_FAULT;

			if ($shopsystemArticle->getQuantity() < $responseArticle->getQuantity() && $shopsystemArticle->getQuantity() != 0)
				$return .= ',' . REDUCE_QUANTITY_ON_PNAG_SERVER;

			if ($responseArticle->getQuantity() < $shopsystemArticle->getQuantity() && $responseArticle->getQuantity() != 0)
				$return .= ',' . REDUCE_QUANTITY_IN_SHOPSYSTEM;

			if ($shopsystemArticle->getQuantity() == 0)
				$return .= ',' . DELETE_ITEM_ON_PNAG_SERVER;

			if ($responseArticle->getQuantity() == 0)
				$return .= ',' . DELETE_ITEM_IN_SHOPSYSTEM;

			if ($shopsystemArticle->getUnitPrice() < $responseArticle->getUnitPrice() )
				$return .= ',' . REDUCE_UNITPRICE_IN_SHOPSYSTEM; //REDUCE_UNITPRICE_ON_PNAG_SERVER; //currently not available!

			if ($responseArticle->getUnitPrice() < $shopsystemArticle->getUnitPrice() )
				$return .= ',' . REDUCE_UNITPRICE_IN_SHOPSYSTEM;
		}

		if (empty ($return) ) {
			return $return;
		} else {
			$return = substr($return, 1); //delete first comma
			return $return;
		}
	}


	/**
	 * collects all article (updated and deleted) and send them to pnag-server
	 * @see getShoparticlesToUpdate() (is the same for the shopsystem)
	 * @public
	 */
	function updateInvoice() {
		$differences = $this->checkPnagShopArticleDifferences();

		if($differences) {

			//delete all article-updates, that must not be done on pnag-server
			foreach ($differences as $key => $difference) {

				$currentSyncActions = explode(',', $difference ['syncAction'] );
				$newSyncAction = array();
				foreach ($currentSyncActions as $currentSyncAction) {
					switch ($currentSyncAction){
						case 'REDUCE_QUANTITY_ON_PNAG_SERVER': 	$newSyncAction[] = 'REDUCE_QUANTITY_ON_PNAG_SERVER';
																break;
						case 'REDUCE_UNITPRICE_ON_PNAG_SERVER':	$newSyncAction[] = 'REDUCE_UNITPRICE_ON_PNAG_SERVER';  //currently not available!
																break;
						case 'DELETE_ITEM_ON_PNAG_SERVER': 		$newSyncAction[] = 'DELETE_ITEM_ON_PNAG_SERVER';
																break;
						case 'UNKNOWN_COMPARISON_FAULT': 		return MODULE_PAYMENT_SOFORT_SR_UNKNOWN_COMPARISON_FAULT_NO_SYNC;
					}
				}
				if (empty ($newSyncAction) ) {
					unset ($differences [$key] );
				} else {
					$differences [$key]['syncAction'] = implode (',', $newSyncAction);
				}
			}

			$pnagArticles = $this->pnagArticles; //we work with a copy

			//find out, which articles have to be updated or deleted
			foreach ($differences as $key => $difference) { //$difference has both article-objects
				$foundActions = explode(',', $difference ['syncAction'] );  //e.g. 'REDUCE_QUANTITY_ON_PNAG_SERVER,DELETE_ITEM_ON_PNAG_SERVER'
				$makeAction = '';
				foreach ($foundActions as $foundAction) {
					switch ($foundAction ) {
						case 'REDUCE_QUANTITY_ON_PNAG_SERVER': 	$newQuantity = $differences [$key]['foundInShop']->getQuantity();
																$differences [$key] ['foundInResponse']->setQuantity ($newQuantity);
																$makeAction = "update";
																break;
						case 'REDUCE_UNITPRICE_ON_PNAG_SERVER': $newUnitPrice = $differences [$key]['foundInShop']->getUnitPrice();   //currently not available!
																$differences [$key] ['foundInResponse']->setUnitPrice ($newUnitPrice);
																$makeAction = "update";
																break;
						case 'DELETE_ITEM_ON_PNAG_SERVER':		$itemId = $differences [$key]['foundInResponse']->getItemId();
																$makeAction = "delete";
																break 2;  //if "delete" other checks are needless
					}
				}
				switch ($makeAction) {
					case 'update':	$updatedArticle = $differences [$key] ['foundInResponse'];
									$pnagArticles = $this->_updateArticleFromArray ($pnagArticles, $updatedArticle);
									break;
					case 'delete':	$itemId = $differences [$key]['foundInResponse']->getItemId();
									$pnagArticles = $this->_deleteArticleFromArray ($pnagArticles, $itemId);
									break;
				}
			}

			if($this->confirmSr != null) {
				foreach($pnagArticles as $article) {
					$this->confirmSr->addItem($article->item_id, $article->product_number, $article->product_type, $article->title, $article->description, $article->quantity, $article->unit_price, $article->tax);
				}
				//$this->confirmSr->enableLog();
				$this->confirmSr->setComment('Cart updated');
				$this->confirmSr->sendRequest();

				$this->transactionData = $this->_setupTransactionData();
				return $this->getErrors();
			}
			return false;
		}
		return array();	// TODO: check return value, array needed?
	}


	/**
	 * collects all article (observes before updated and deleted articles) for the shopsystem and returns them
	 * @return string - in case of error
	 * @return array - emtpy if nothing to update
	 * @return array - including all articles with the correct data to update in the shopsystem
	 * @see updateInvoice() (is the same for the pnag-server)
	 * @public
	 */
	function getArticlesToUpdateInShop() {

		$differences = $this->checkPnagShopArticleDifferences();

		if ($differences) {

			//delete all article-updates, that must not be done in shopsystem
			foreach ($differences as $key => $difference) {
				$currentSyncActions = explode(',', $difference ['syncAction'] );
				$newSyncAction = array();
				foreach ($currentSyncActions as $currentSyncAction) {
					switch ($currentSyncAction){
						case 'REDUCE_QUANTITY_IN_SHOPSYSTEM': 	$newSyncAction[] = 'REDUCE_QUANTITY_IN_SHOPSYSTEM';
																break;
						case 'REDUCE_UNITPRICE_IN_SHOPSYSTEM':	$newSyncAction[] = 'REDUCE_UNITPRICE_IN_SHOPSYSTEM';
																break;
						case 'DELETE_ITEM_IN_SHOPSYSTEM': 		$newSyncAction[] = 'DELETE_ITEM_IN_SHOPSYSTEM';
																break;
						case 'UNKNOWN_COMPARISON_FAULT': 		return MODULE_PAYMENT_SOFORT_SR_UNKNOWN_COMPARISON_FAULT_NO_SYNC;
					}
				}
				if (empty ($newSyncAction) ) {
					unset ($differences [$key] );
				} else {
					$differences [$key]['syncAction'] = implode (',', $newSyncAction);
				}
			}

			$shopArticles = $this->getShopArticles();

			//find out, which articles have to be updated or deleted
			foreach ($differences as $key => $difference) { //$difference has both article-objects
				$foundActions = explode(',', $difference ['syncAction'] );  //e.g. 'REDUCE_QUANTITY_IN_SHOPSYSTEM,DELETE_ITEM_IN_SHOPSYSTEM'
				$makeAction = '';
				foreach ($foundActions as $foundAction) {
					switch ($foundAction ) {
						case 'REDUCE_QUANTITY_IN_SHOPSYSTEM': 	$newQuantity = $differences [$key]['foundInResponse']->getQuantity();
																$differences [$key] ['foundInShop']->setQuantity ($newQuantity);
																$makeAction = "update";
																break;
						case 'REDUCE_UNITPRICE_IN_SHOPSYSTEM': 	$newUnitPrice = $differences [$key]['foundInResponse']->getUnitPrice();
																$differences [$key] ['foundInShop']->setUnitPrice ($newUnitPrice);
																$makeAction = "update";
																break;
						case 'DELETE_ITEM_IN_SHOPSYSTEM':		$itemId = $differences [$key]['foundInShop']->getItemId();
																$makeAction = "delete";
																break 2;  //if "delete" other checks are needless
					}
				}
				switch ($makeAction) {
					case 'update':	$updatedArticle = $differences [$key] ['foundInShop'];
									$shopArticles = $this->_updateArticleFromArray ($shopArticles, $updatedArticle);
									break;
					case 'delete':	$itemId = $differences [$key]['foundInShop']->getItemId();
									$shopArticles = $this->_setArticleIsDeleted ($shopArticles, $itemId);
									break;
				}
			}
			return $shopArticles;
		}
		return array();
	}


	/**
	 * replaces in given $articlesArray the first found article with given $updateArticle ($itemIds must be equivalent)
	 * @param array - $articlesArray
	 * @param object - $updatedArticle
	 * @return array - updated $articlesArray
	 * @private
	 */
	function _updateArticleFromArray ($articlesArray, $updatedArticle) {
		foreach ($articlesArray as $key => $article) {
			if ($article->getItemId() == $updatedArticle->getItemId()) {
				$articlesArray [$key] = $updatedArticle;
				break;
			}
		}
		return $articlesArray;
	}


	/**
	 * deletes in given $articlesArray the first found given $itemId
	 * @param array $articlesArray - Array within article-objects
	 * @param String/Int $itemId - ItemId to delete
	 * @return $articlesArray
	 * @private
	 */
	function _deleteArticleFromArray ($articlesArray, $itemId) {
		foreach ($articlesArray as $key => $article) {
			if ($article->getItemId() == $itemId){
				unset ($articlesArray [$key] );
				break;
			}
		}
		return $articlesArray;
	}


	/**
	 * sets in given $articlesArray the first found article-obj to "$isDeleted=1" (if $itemId is found)
	 * @param array $articlesArray - Array within article-objects
	 * @param String/Int $itemId - ItemId to set "isDeleted"
	 * @return $articlesArray
	 * @private
	 */
	function _setArticleIsDeleted ($articlesArray, $itemId) {
		foreach ($articlesArray as $key => $article) {
			if ($article->getItemId() == $itemId){
				$articlesArray [$key]->setIsDeleted (1);
				break;
			}
		}
		return $articlesArray;
	}


	/**
	 * Initializes the statusMask
	 * Sort of a bitmask
	 * @private
	 */
	function _initBitmask() {
		$statusMask = $this->statusMask;
		$this->statusMask = array();

		$i = 0;
		foreach($statusMask as $mask) {
			$this->statusMask[$mask] = pow(2, $i);
			$i++;
		}
	}


	/**
	 * Output the resulting invoice as pdf
	 * @public
	 */
	function getInvoice() {
		header('Content-type: application/pdf');
		header('Content-Disposition: attachment; filename="invoice.pdf"');
		echo file_get_contents($this->invoiceUrl);
		return true;
	}


	/**
	 * Getter for retrieving the invoice's url
	 * @public
	 * @return url string
	 */
	function getInvoiceUrl() {
		return $this->invoiceUrl;
	}


	/**
	 * Setter for status
	 * @public
	 * @param $status
	 * @return object
	 */
	function setStatus($status) {
		$this->status = $status;
		return $this;
	}


	/**
	 * Setter for status_reason
	 * @param $status_reason
	 * @return object
	 */
	function setStatusReason($status_reason) {
		$this->status_reason = $status_reason;
		return $this;
	}


	/**
	 * Setter for trasaction
	 * @param $transaction
	 * @return object
	 */
	function setTransaction($transaction) {
		$this->transaction = $transaction;
		return $this;
	}


	/**
	 * Setter for time
	 * @param $time
	 * @public
	 * return object
	 */
	function setTime($time) {
		$this->time = $time;
		return $this;
	}


	/**
	 * Setter for interface version
	 * Wrapper for class Multipay to set version according to shop module and it's interface version
	 * @param $arg e.g. 'pn_xtc_5.0.0'
	 */
	function setVersion($arg) {
		$this->multipay->setVersion($arg);
	}


	/**
	 * Setter for payment_method
	 * @param $paymentMethod
	 * @return object
	 */
	function setPaymentMethod($paymentMethod) {
		$this->payment_method = $paymentMethod;
		return $this;
	}


	/**
	 * Setter for invoiceUrl
	 * @public
	 * @param $invoiceUrl
	 * @return object
	 */
	function setInvoiceUrl($invoiceUrl) {
		$this->invoiceUrl = $invoiceUrl;
		return $this;
	}


	/**
	 * Uses the statusMask to "calculate" the current invoice's payment status
	 * @public
	 * return int
	 */
	function getInvoiceStatus() {
		return $this->_calcInvoiceStatusCode();
	}


	/**
	 *
	 * Calculate the current invoice's payment status using bitwise OR
	 * @return int
	 * @private
	 */
	function _calcInvoiceStatusCode() {
		return $this->statusMask[$this->status] | $this->statusMask[$this->status_reason];
	}


	/**
	 * Getter for payment_method
	 * @public
	 * @return string
	 */
	function getPaymentMethod() {
		return $this->payment_method;
	}


	/**
	 * Getter for status_reason
	 * @public
	 * @return string
	 */
	function getStatusReason() {
		return $this->status_reason;
	}


	/**
	 * Getter for status
	 * @public
	 * @return string
	 */
	function getStatus() {
		return $this->status;
	}


	/**
	 * Getter for shopArticles
	 * @public
	 * @return array
	 */
	function getShopArticles () {
		return $this->shopArticles;
	}


	/**
	 * Getter for pnagArticles
	 * @public
	 * @return array
	 */
	function getPnagArticles() {
		return $this->pnagArticles;
	}


	/**
	 * return TransactionData, the invoice is working with
	 * NOTICE: if status changed (removeArticle, InvoiceConfirmed etc.) it returns always the FRESH TransactionData from pnag-server
	 * @return object
	 * @see $this->refreshTransactionData();
	 */
	function getTransactionData() {
		if ($this->transactionData) {
			return $this->transactionData;
		} else {
			return false;
		}
	}
}
?>