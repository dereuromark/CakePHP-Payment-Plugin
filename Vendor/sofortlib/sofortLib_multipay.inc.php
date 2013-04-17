<?php
/**
 * Setup a multipay payment session
 * after the configuration of multiple payment methods you will receive
 * an url and a transaction id, your customer should be redirected to this
 * url you can use the transaction id for future reference of this payment
 *
 * example by usage:
 * $objMultipay = new SofortLib_Multipays('my-API-KEY');
 * $objMultipay->setSofortueberweisung(); 					//OR setSofortrechnung(), setSofortvorkasse() etc.
 * $objMultipay->set...($param);  							//set params for PNAG-API (watch API-documentation for needed params)
 * $objMultipay->add...($param);							//add params for PNAG-API (watch API-documentation for needed params)
 * $errorsAndWarnings = $objMultipay->validateRequest();	//send param against the PNAG-API without setting an order
 * ... make own validation of $errorsAndWarnings and if ok ...
 * $objMultipay->sendRequest();								//set the order at PNAG
 * $errorsAndWarnings =	$objMultipay->getErrors();			//should not occur, if validation was ok
 * ... make own validation of $errorsAndWarnings and if ok ...
 * ... finish order in the shopsystem
 *
 * Copyright (c) 2011 Payment Network AG
 *
 * $Date: 2011-11-30 10:40:45 +0100 (Wed, 30 Nov 2011) $
 * @version SofortLib 1.3.0  $Id: sofortLib_multipay.inc.php 2418 2011-11-30 09:40:45Z dehn $
 * @author Payment Network AG http://www.payment-network.com (integration@sofort.com)
 *
 */
class SofortLib_Multipay extends SofortLib_Abstract
{
	var $paymentMethods = array();
	var $transactionId = '';
	var $paymentUrl = '';
	var $parameters = array();


	/**
	 * create a new payment object
	 * @param string $apikey your API key
	 * @param int $projectId your project id
	 */
	function SofortLib_Multipay($apikey='') {
		list($userid, $projectId, $apikey) = explode(':', $apikey);
		$apiUrl = (getenv('sofortApiUrl') != '') ? getenv('sofortApiUrl') : 'https://api.sofort.com/api/xml';
		$this->SofortLib($userid, $apikey, $apiUrl);
		$this->parameters['project_id'] = $projectId;
	}


	/**
	 * the language code will help in determing what language to
	 * use when displaying the payment form, other data like
	 * browser settings and ip will be used as well
	 *
	 * @param string $arg de|en|nl|fr ...
	 * @return SofortLib_Multipay
	 */
	function setLanguageCode($arg) {
		$this->parameters['language_code'] = $arg;
		return $this;
	}


	/**
	 * timeout how long this transaction configuration will be valid for
	 * this is the time between the generation of the payment url and
	 * the user completing the form, should be at least two to three minutes
	 * defaults to unlimited if not set
	 *
	 * @param int $arg timeout in seconds
	 * @return SofortLib_Multipay
	 */
	function setTimeout($arg) {
		$this->parameters['timeout'] = $arg;
		return $this;
	}


	/**
	 * set the email address of the customer
	 * this will be used for sofortvorkasse and sofortrechnung
	 *
	 * @param string $arg email address
	 * @return SofortLib_Multipay
	 */
	function setEmailCustomer($arg) {
		$this->parameters['email_customer'] = $arg;
		return $this;
	}


	/**
	 * set the phone number of the customer
	 *
	 * @param string $arg phone number
	 * @return SofortLib_Multipay
	 */
	function setPhoneNumberCustomer($arg) {
		$this->parameters['phone_customer'] = $arg;
		return $this;
	}


	/**
	 * add another variable this can be your internal order id or similar
	 *
	 * @param string $arg the contents of the variable
	 * @return SofortLib_Multipay
	 */
	function addUserVariable($arg) {
		$this->parameters['user_variables'][] = $arg;
		return $this;
	}


	/**
	 * set data of account
	 *
	 * @param string $bank_code bank code of bank
	 * @param string $account_number account number
	 * @param string $holder Name/Holder of this account
	 * @return SofortLib_Multipay $this
	 */
	function setSenderAccount($bank_code, $account_number, $holder) {
		$this->parameters['sender'] = array('holder' => $holder, 'account_number' => $account_number, 'bank_code' => $bank_code);
		return $this;
	}


	/**
	 * amount of this payment
	 *
	 * @param double $arg
	 * @param string $currency currency of this transaction, default EUR
	 * @return SofortLib_Multipay $this
	 */
	function setAmount($arg, $currency = 'EUR') {
		$this->parameters['amount'] = $arg;
		$this->parameters['currency_code'] = $currency;
		return $this;
	}


	/**
	 * set the reason values of this transfer
	 *
	 * @param string $arg max 27 characters
	 * @param string $arg2 max 27 characters
	 * @return SofortLib_Multipay $this
	 */
	function setReason($arg, $arg2='') {
		$arg = preg_replace('#[^a-zA-Z0-9+-\.,]#', ' ', $arg);
		$arg = substr($arg, 0, 27);
		$arg2 = preg_replace('#[^a-zA-Z0-9+-\.,]#', ' ', $arg2);
		$arg2 = substr($arg2, 0, 27);
		
		$this->parameters['reasons'][0] = $arg;
		$this->parameters['reasons'][1] = $arg2;
		return $this;
	}


	/**
	 * the customer will be redirected to this url after a successful
	 * transaction, this should be a page where a short confirmation is
	 * displayed
	 *
	 * @param string $arg the url after a successful transaction
	 * @return SofortLib_Multipay
	 */
	function setSuccessUrl($arg) {
		$this->parameters['success_url'] = $arg;
		return $this;
	}


	/**
	 * the customer will be redirected to this url if he uses the
	 * abort link on the payment form, should redirect him back to
	 * his cart or to the payment selection page
	 *
	 * @param string $arg url for aborting the transaction
	 * @return SofortLib_Multipay
	 */
	function setAbortUrl($arg) {
		$this->parameters['abort_url'] = $arg;
		return $this;
	}


	/**
	 * if the customer takes too much time or if your timeout is set too short
	 * he will be redirected to this page
	 *
	 * @param string $arg url
	 * @return SofortLib_Multipay
	 */
	function setTimeoutUrl($arg) {
		$this->parameters['timeout_url'] = $arg;
		return $this;
	}


	/**
	 * set the url where you want notification about status changes
	 * being sent to. Use SofortLib_Notification and SofortLib_TransactionData
	 * to further process that notification
	 *
	 * @param string $arg url
	 * @return SofortLib_Multipay
	 */
	function setNotificationUrl($arg) {
		$this->parameters['notification_urls'] = array($arg);
		return $this;
	}


	/**
	 * you can set set multiple urls for receiving notifications
	 * this might be helpfull if you have several systems for processing
	 * an order (e.g. an ERP system)
	 *
	 * @param string $arg url
	 * @return SofortLib_Multipay
	 */
	function addNotificationUrl($arg) {
		$this->parameters['notification_urls'][] = $arg;
		return $this;
	}


	/**
	 * set the email address where you want notification about status changes
	 * being sent to.
	 *
	 * @param string $arg email address
	 * @return SofortLib_Multipay
	 */
	function setNotificationEmail($arg) {
		$this->parameters['notification_emails'] = array($arg);
		return $this;
	}


	/**
	 * you can set set multiple emails for receiving notifications
	 *
	 * @param string $arg email
	 * @return SofortLib_Multipay
	 */
	function addNotificationEmail($arg) {
		$this->parameters['notification_emails'][] = $arg;
		return $this;
	}


	/**
	 * set the version of this payment module
	 * this is helpfull so the support staff can easily
	 * find out if someone uses an outdated module
	 *
	 * @param string $arg version string of your module
	 * @return SofortLib_Multipay
	 */
	function setVersion($arg) {
		$this->parameters['interface_version'] = $arg;
		return $this;
	}


	/**
	 * add sofortueberweisung as payment method
	 * @param double $amount this amount only applies to this payment method
	 * @return SofortLib_Multipay $this
	 */
	function setSofortueberweisung($amount='') {
		$this->paymentMethods[] = 'su';
		if(!array_key_exists('su', $this->parameters) || !is_array($this->parameters['su'])) {
			$this->parameters['su'] = array();
		}
		if(!empty($amount)) {
			$this->parameters['su']['amount'] = $amount;
		}
		return $this;
	}


	/**
	 * add sofortueberweisung as payment method
	 * adds customer protection
	 * @param double $amount this amount only applies to this payment method
	 * @return SofortLib_Multipay $this
	 */
	function setSofortueberweisungCustomerprotection($customerProtection = true) {
		$this->paymentMethods[] = 'su';
		if(!array_key_exists('su', $this->parameters) || !is_array($this->parameters['su'])) {
			$this->parameters['su'] = array();
		}
		
		$this->parameters['su']['customer_protection'] = $customerProtection ? 1 : 0;
		return $this;
	}


	/**
	 * add sofortlastschrift as payment method
	 * @param double $amount this amount only applies to this payment method
	 * @return SofortLib_Multipay $this
	 */
	function setSofortlastschrift($amount='') {
		$this->paymentMethods[] = 'sl';
		if(!array_key_exists('sl', $this->parameters) || !is_array($this->parameters['sl'])) {
			$this->parameters['sl'] = array();
		}
		if(!empty($amount)) {
			$this->parameters['sl']['amount'] = $amount;
		}
		return $this;
	}


	/**
	 * set the address of the customer for address validation,
	 * this should be the invoice address of the customer
	 *
	 * @param string $firstname
	 * @param string $lastname
	 * @param string $street
	 * @param string $streetNumber
	 * @param string $zipcode
	 * @param string $city
	 * @param int $salutation [2|3] 2=Mr. 3=Mrs.
	 * @param string $country country code, only DE allowed at the moment
	 * @return SofortLib_Multipay $this
	 */
	function setSofortlastschriftAddress($firstname, $lastname, $street, $streetNumber, $zipcode, $city, $salutation, $country = 'DE') {
		$this->parameters['sl']['invoice_address']['salutation'] = $salutation;
		$this->parameters['sl']['invoice_address']['firstname'] = $firstname;
		$this->parameters['sl']['invoice_address']['lastname'] = $lastname;
		$this->parameters['sl']['invoice_address']['street'] = $street;
		$this->parameters['sl']['invoice_address']['street_number'] = $streetNumber;
		$this->parameters['sl']['invoice_address']['zipcode'] = $zipcode;
		$this->parameters['sl']['invoice_address']['city'] = $city;
		$this->parameters['sl']['invoice_address']['country_code'] = $country;
		return $this;
	}


	/**
	 * add lastschrift as payment method
	 * @param double $amount this amount only applies to this payment method
	 * @return SofortLib_Multipay $this
	 */
	function setLastschrift($amount='') {
		$this->paymentMethods[] = 'ls';
		if(!array_key_exists('ls', $this->parameters) || !is_array($this->parameters['ls'])) {
			$this->parameters['ls'] = array();
		}
		if(!empty($amount)) {
			$this->parameters['ls']['amount'] = $amount;
		}
		return $this;
	}


	function setLastschriftBaseCheckDisabled() {
		$this->parameters['ls']['base_check_disabled'] = 1;
		return $this;
	}


	function setLastschriftExtendedCheckDisabled() {
		$this->parameters['ls']['extended_check_disabled'] = 1;
		return $this;
	}


	function setLastschriftMobileCheckDisabled() {
		$this->parameters['ls']['mobile_check_disabled'] = 1;
		return $this;
	}


	/**
	 * set the address of the customer for address validation,
	 * this should be the invoice address of the customer
	 *
	 * @param string $firstname
	 * @param string $lastname
	 * @param string $street
	 * @param string $streetNumber
	 * @param string $zipcode
	 * @param string $city
	 * @param int $salutation [2|3] 2=Mr. 3=Mrs.
	 * @param string $country country code, only DE allowed at the moment
	 *
	 * @return SofortLib_Multipay object
	 */
	function setLastschriftAddress($firstname, $lastname, $street, $streetNumber, $zipcode, $city, $salutation, $country = 'DE') {
		$this->parameters['ls']['invoice_address']['salutation'] = $salutation;
		$this->parameters['ls']['invoice_address']['firstname'] = $firstname;
		$this->parameters['ls']['invoice_address']['lastname'] = $lastname;
		$this->parameters['ls']['invoice_address']['street'] = $street;
		$this->parameters['ls']['invoice_address']['street_number'] = $streetNumber;
		$this->parameters['ls']['invoice_address']['zipcode'] = $zipcode;
		$this->parameters['ls']['invoice_address']['city'] = $city;
		$this->parameters['ls']['invoice_address']['country_code'] = $country;
		return $this;
	}


	/**
	 * add SofortDauerauftrag as a payment method
	 *
	 * @return SofortLib_Multipay object
	 */
	function setSofortDauerauftrag() {
		$this->paymentMethods[] = 'sa';
		if(!array_key_exists('sa', $this->parameters) || !is_array($this->parameters['sa'])) {
			$this->parameters['sa'] = array();
		}
		return $this;
	}


	/**
	 *
	 * Set the date to start "Dauerauftrag"
	 * date must be compliant to ISO 8601 (e.g. 2011-06-21)
	 * @param $arg - string date
	 */
	function setSofortDauerauftragStartDate($arg) {
		$this->parameters['sa']['start_date'] = $arg;
		return $this;
	}


	/**
	 *
	 * Enter description here ...
	 * @param $arg
	 * @return object
	 */
	function setSofortDauerauftragTotalPayments($arg) {
		$this->parameters['sa']['total_payments'] = $arg;
		return $this;
	}


	/**
	 *
	 * set the minimum amount for Dauerauftrag
	 * @param $arg
	 * @return object
	 */
	function setSofortDauerauftragMinimumPayments($arg) {
		$this->parameters['sa']['minimum_payments'] = $arg;
		return $this;
	}


	/**
	 *
	 * set the interval for payment
	 * @param $arg
	 * @return object
	 */
	function setSofortDauerauftragInterval($arg) {
		$this->parameters['sa']['interval'] = $arg;
		return $this;
	}


	/**
	 * add sofortrechnung as payment method
	 * if you use this payment method you have to provide
	 * the customer address and cart as well
	 * the total amount of this payment method will
	 * be determined by the total of the cart
	 *
	 * @return SofortLib_Multipay object
	 */
	function setSofortrechnung() {
		$this->paymentMethods[] = 'sr';
		if(!array_key_exists('sr', $this->parameters) || !is_array($this->parameters['sr'])) {
			$this->parameters['sr'] = array();
		}
		return $this;
	}


	/**
	 * add sofortvorkasse as payment method
	 * @param double $amount this amount only applies to this payment method
	 *
	 * @return SofortLib_Multipay objet
	 */
	function setSofortvorkasse($amount='') {
		$this->paymentMethods[] = 'sv';
		if(!array_key_exists('sv', $this->parameters) || !is_array($this->parameters['sv'])) {
			$this->parameters['sv'] = array();
		}
		if(!empty($amount)) {
			$this->parameters['sv']['amount'] = $amount;
		}
		return $this;
	}


	/**
	 * add sofortvorkasse as payment method
	 * adds customer protection
	 * @param double $amount this amount only applies to this payment method
	 * @return SofortLib_Multipay $this
	 */
	function setSofortvorkasseCustomerprotection($customerProtection = true) {
		$this->paymentMethods[] = 'sv';
		if(!array_key_exists('sv', $this->parameters) || !is_array($this->parameters['sv'])) {
			$this->parameters['sv'] = array();
		}
		$this->parameters['sv']['customer_protection'] = $customerProtection ? 1 : 0;
		return $this;
	}


	/**
	 * set the customer id which will appear on top of the invoice
	 * @param int $arg
	 * @return SofortLib_Multipay $this
	 */
	function setSofortrechnungCustomerId($arg) {
		$this->parameters['sr']['customer_id'] = $arg;
		return $this;
	}


	/**
	 * set the order id which will appear on top of the invoice
	 * @param int $arg
	 * @return SofortLib_Multipay $this
	 */
	function setSofortrechnungOrderId($arg) {
		$this->parameters['sr']['order_id'] = $arg;
		return $this;
	}


	/**
	 * set the invoice address of the customer
	 *
	 * @param string $firstname
	 * @param string $lastname
	 * @param string $street
	 * @param string $streetNumber
	 * @param string $zipcode
	 * @param string $city
	 * @param int $salutation [2|3] 2=Mr. 3=Mrs.
	 * @param string $country country code, only DE allowed at the moment
	 * @return SofortLib_Multipay $this
	 */
	function setSofortrechnungInvoiceAddress($firstname, $lastname, $street, $streetNumber, $zipcode, $city, $salutation, $country = 'DE') {
		$this->parameters['sr']['invoice_address']['salutation'] = $salutation;
		$this->parameters['sr']['invoice_address']['firstname'] = $firstname;
		$this->parameters['sr']['invoice_address']['lastname'] = $lastname;
		$this->parameters['sr']['invoice_address']['street'] = $street;
		$this->parameters['sr']['invoice_address']['street_number'] = $streetNumber;
		$this->parameters['sr']['invoice_address']['zipcode'] = $zipcode;
		$this->parameters['sr']['invoice_address']['city'] = $city;
		$this->parameters['sr']['invoice_address']['country_code'] = $country;
		return $this;
	}


	/**
	 * set the shipping address of the customer
	 *
	 * @param string $firstname
	 * @param string $lastname
	 * @param string $street
	 * @param string $streetNumber
	 * @param string $zipcode
	 * @param string $city
	 * @param int $salutation [2|3] 1=Mr. 2=Mrs.
	 * @param string $country country code, only DE allowed at the moment
	 * @return SofortLib_Multipay $this
	 */
	function setSofortrechnungShippingAddress($firstname, $lastname, $street, $streetNumber, $zipcode, $city, $salutation, $country = 'DE') {
		$this->parameters['sr']['shipping_address']['salutation'] = $salutation;
		$this->parameters['sr']['shipping_address']['firstname'] = $firstname;
		$this->parameters['sr']['shipping_address']['lastname'] = $lastname;
		$this->parameters['sr']['shipping_address']['street'] = $street;
		$this->parameters['sr']['shipping_address']['street_number'] = $streetNumber;
		$this->parameters['sr']['shipping_address']['zipcode'] = $zipcode;
		$this->parameters['sr']['shipping_address']['city'] = $city;
		$this->parameters['sr']['shipping_address']['country_code'] = $country;
		return $this;
	}


	/**
	 * add one item to the cart
	 *
	 * @param int $itemId unique item id
	 * @param string $productNumber product number, EAN code, ISBN number or similar
	 * @param string $title description of this title
	 * @param double $unit_price gross price of one item
	 * @param int $productType product type number see manual (0=other, 1=shipping, ...)
	 * @param string $description additional description of this item
	 * @param int $quantity default 1
	 * @param int $tax tax in percent, default 19
	 */
	function addSofortrechnungItem($itemId, $productNumber, $title, $unit_price, $productType = 0, $description = '', $quantity = 1, $tax = 19) {
		$unit_price = number_format($unit_price, 2, '.','');
		$tax = number_format($tax, 2, '.','');
		$quantity = intval($quantity);

		if (empty ($title) ) {
			$this->setError('Title must not be empty. Title: '.$title.', Productnumber: '.$productNumber.', Unitprice: '.$unit_price.', Quantity: '.$quantity.', Description:'.$description);
		}

		$this->parameters['sr']['items'][] = array(
			'item_id' => $itemId,
			'product_number' => $productNumber,
			'product_type' => $productType,
			'title' => $title,
			'description' => $description,
			'quantity' => $quantity,
			'unit_price' => $unit_price,
			'tax' => $tax
		);
	}


	/**
	 * Remove one item from cart
	 * @param $itemId
	 * @return boolean
	 */
	function removeSofortrechnungItem($itemId) {
		$i = 0;
		foreach($this->parameters['sr']['items'] as $item) {
			if($item['item_id'] == $itemId) {

				unset($this->parameters['sr']['items'][$i]);
				return true;
			}
			$i++;
		}
		return false;
	}


	/**
	 * Update one item in cart
	 * @param $itemId
	 * @param $quantity
	 * @param $unit_price
	 * @return boolean
	 */
	function updateSofortrechnungItem($itemId, $quantity, $unit_price) {
		$i = 0;
		foreach($this->parameters['sr']['items'] as $item) {
			if($item['item_id'] == $itemId) {
				$this->parameters['sr']['items'][$i]['quantity'] = $quantity;
				$this->parameters['sr']['items'][$i]['unit_price'] = $unit_price;
				return true;
			}
			$i++;
		}
		return false;
	}


	function getSofortrechnungItemAmount($itemId) {
		$i = 0;
		foreach($this->parameters['sr']['items'] as $item) {
			if($item['item_id'] == $itemId) {
				return $this->parameters['sr']['items'][$i]['quantity'] * $this->parameters['sr']['items'][$i]['unit_price'];
			}
			$i++;
		}
	}


	function setSofortrechnungTimeForPayment($arg) {
		$this->parameters['sr']['time_for_payment'] = $arg;
		return $this;
	}


	/**
	 * makes a request against the pnag-API and returns all API-Fault/Warnings
	 * it doesnt result in an order at pnag!
	 * @return emtpy array if no API-faults/warnings found ELSE array with error-code and error-message
	 */
	function validateRequest($paymentMethod = 'all') {
		$this->parameters['validate_only'] = '1';
		$validationResult = $this->sendRequest();
		unset ($this->parameters['validate_only'] );

		$errors = array();
		if ($this->isError($paymentMethod)) {
			$errors = $this->getErrors($paymentMethod);
		}

		$warnings = array();
		if ($this->isWarning($paymentMethod)) {
			$warnings = $this->getWarnings($paymentMethod);
		}

		$this->deleteAllErrors();
		$this->deleteAllWarnings();

		if (!$errors && !$warnings) {
			return array(); //no errors or warnings found
		} else {
			$returnArray = array();
			$returnArray['errors'] = $errors;
			$returnArray['warnings'] = $warnings;
			return $returnArray;
		}
	}


	function getSofortrechnungItem($itemId) {
		return $this->parameters['sr']['items'][$itemId];
	}


	function getSofortrechnungItems() {
		return $this->parameters['sr']['items'];
	}


	/**
	 * Parser for response from server
	 * this callback will be called for every closing xml-tag
	 * @private
	 */
	function onParseTag($data, $tag){
		switch($tag) {
			case 'transaction':
				$this->transactionId = $data;
				break;
			case 'payment_url':
				$this->paymentUrl = $data;
				break;
			case 'new_transaction':
				//finished parsing everything
			default:
				break;
		}
	}


	/**
	 * after configuration and sending this request
	 * you can use this function to redirect the customer
	 * to the payment form
	 *
	 * @return string url of payment form
	 */
	function getPaymentUrl() {
		return $this->paymentUrl;
	}

	function getPaymentMethod($i = 0) {
		if($i < 0 || $i >= count($this->paymentMethods)) {
			return false;
		}
		return $this->paymentMethods[$i];
	}


	function isSofortueberweisung() {
		return array_key_exists('su', $this->parameters);
	}


	function isSofortvorkasse() {
		return array_key_exists('sv', $this->parameters);
	}


	function isSofortlastschrift() {
		return array_key_exists('sl', $this->parameters);
	}


	function isLastschrift() {
		return array_key_exists('ls', $this->parameters);
	}


	function isSofortdauerauftrag() {
		return array_key_exists('sa', $this->parameters);
	}


	function isSofortrechnung() {
		return array_key_exists('sr', $this->parameters);
	}


	/**
	 * use this id to track the transaction
	 *
	 * @return string transaction id
	 */
	function getTransactionId() {
		return $this->transactionId;
	}


	/**
	 * generate XML message
	 * @return string
	 */
	function toXml() {
		$msg = '<?xml version="1.0" encoding="UTF-8"?>';
		$msg .= $this->_arrayToXml($this->parameters, 'multipay');
		return $msg;
	}
}