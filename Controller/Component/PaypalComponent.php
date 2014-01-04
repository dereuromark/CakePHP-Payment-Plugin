<?php
App::uses('Component', 'Controller');

/**
 * Paypal Payments Component Using cURL
 *
 * inspired by PayPal-Code-Samples and Parris Khachi
 * see https://www.paypal.com/IntegrationCenter/sdk/PayPal_PHP_NVP_Samples.zip
 * see http://bakery.cakephp.org/articles/view/paypal-payments-component-using-curl
 *
 * @version         $Revision: 94 $ ($Date: 2010-02-14 15:51:06 +0100 (So, 14 Feb 2010) $)
 * @author          Created by Daniel Quappe on 11.02.2010 14:08:36. Last Editor: $Author: dan $
 * @copyright       Copyright (c) 2010 Daniel Quappe. All rights reserved.
 */

/**
 * TODO: move call part to Lib..
 * Paypal Payment API Component class file.
 * Added new methods
 */
class PaypalComponent extends Component {

	/* Benutzte Zusatz-Komponenten */
	public $components = array();

	/* Klassen-Member */
	public $Controller = null;

	public $urls = array(
		'sandbox' => array(
			'url' => 'https://www.sandbox.paypal.com/webscr',
			'api' => 'https://api-3t.sandbox.paypal.com/nvp'
		),
		'live' => array(
			'url' => 'https://www.paypal.com/cgi-bin/webscr',
			'api' => 'https://api-3t.paypal.com/nvp'
		),
		'ok' => '',
		'nok' => ''
	);

	public $settings = array(
		'live' => false,
		'use_proxy' => false,
		'proxy_host' => '',
		'proxy_port' => '',
		'currency_code' => 'EUR',
		'locale' => 'DE',
		'username' => '',
		'signature' => '',
		'password' => '',
		'img' => '',
		'shipping' => false # do not ask for address etc
	);

	const VERSION = 56.0;
	const MAX_LOGO_WIDTH = 750;
	const MAX_LOGO_HEIGHT = 90;

	public function __construct(ComponentCollection $collection, $settings = array()) {
		$configSettings = (array)Configure::read('PayPal');
		$settings = array_merge($this->settings, $configSettings, (array)$settings);
		$this->Controller = $collection->getController();
		parent::__construct($collection, $settings);
	}

	/**
	 * Initialize component
	 *
	 * @return array
	 * @author Daniel Quappe
	 */
	public function initialize(Controller $Controller, $settings = array()) {
		$this->Controller = $Controller;
	}

	/**
	 * go the express checkout
	 * @see https://www.paypal.com/en_US/ebook/PP_NVPAPI_DeveloperGuide/Appx_fieldreference.html#2830886
	 */
	public function redirect($token) {
		$this->Controller->redirect($this->_url() .
		Router::querystring(array('cmd' => '_express-checkout', 'token' => $token)), '302');
	}

	/**
	 * get current settings
	 * @param key
	 */
	public function get($key) {
		if (!isset($this->settings[$key])) {
			trigger_error('invalid setting value requested');
			return false;
		}
		return $this->settings[$key];
	}

	/**
	 * get the appropriate frontend url
	 */
	public function _url() {
		$type = ($this->settings['live'] ? 'live' : 'sandbox');
		return $this->urls[$type]['url'];
	}

	/**
	 * get the appropriate api url
	 */
	public function _api() {
		$type = ($this->settings['live'] ? 'live' : 'sandbox');
		return $this->urls[$type]['api'];
	}

	/**
	 * @param path
	 * @return boolean success
	 * The image has a maximum size of 750 pixels wide by 90 pixels high.
	 */
	public function validateImage($path) {
		list ($width, $height) = @getimagesize($path);
		if ($width > 0 && $height > 0 && $width <= self::MAX_LOGO_WIDTH && $height <= self::MAX_LOGO_HEIGHT) {
			return true;
		}
		return false;
	}

	/**
	 * SetExpressCheckout
	 * @see https://www.paypalobjects.com/de_DE/pdf/PayPal-NVP-API-Reference-Germany.pdf - page 37
	 *
	 * @param array   $nvpDataArray Daten-Array
	 * - amount, cancelurl, pendingurl (all recommended, url as array)
	 * - email, desc, custom, invnum, addroverride
	 * @return array  Ergebnis-Array
	 * @author Daniel Quappe
	 */
	public function setExpressCheckout($dataArray) {
		$defaults = array(
			'PAYMENTACTION' => 'Sale',
			'CURRENCYCODE' => $this->settings['currency_code'],
			//'LOCALE' => 'DE',
		);

		$nvpDataArray = array();
		$nvpDataArray['AMT'] = number_format($dataArray['amount'], 2, '.', '');
		$nvpDataArray['RETURNURL'] = Router::url($dataArray['returnurl'], true);
		$nvpDataArray['CANCELURL'] = Router::url($dataArray['cancelurl'], true);

		//$nvpDataArray['PAYMENTACTION'] = 'Sale';
		//$nvpDataArray['CURRENCYCODE'] = $this->settings['currency_code'];

		/* Shop-Logo-URL (optional, wenn moeglich https-URL) */
		if (($hdrimg = $this->settings['img'])) {
			$nvpDataArray['HDRIMG'] = $hdrimg;
		}
		if (!$this->settings['shipping']) {
			$nvpDataArray['NOSHIPPING'] = 1;
		}

		$map = array(
			'amount' => 'AMT',
			'style' => 'PAGESTYLE',
			'bordercolor' => 'HRDBORDERCOLOR',
			'backcolor' => 'HRDBACKCOLOR',
			'flowcolor' => 'HRDFLOWCOLOR',
			'id' => 'INVNUM',
			'confirm_shipping' => 'REQCONFIRMSHIPPING',
			'addroverride' => 'ADDROVERRIDE',
			'custom' => 'CUSTOM',
			'successurl' => 'GIROPAYSUCCESSURL',
			'cancelurl' => 'GIROPAYCANCELURL',
			'pendingurl' => 'BANKTXNPENDINGURL',
			'desc' => 'DESC',
		);
		foreach ($dataArray as $key => $val) {
			if (isset($map[$key])) {
				$nvpDataArray[$map[$key]] = $val;
				unset($dataArray[$key]);
			}
		}

		//$nvpDataArray['EMAIL'] = '';
		if ($locale = $this->settings['locale']) {
			$nvpDataArray['LOCALECODE'] = $locale;
		}
		$nvpDataArray = array_merge($defaults, $nvpDataArray);

		return $this->_hashCall("SetExpressCheckout", $nvpDataArray);
	}

	/**
	 * GetExpressCheckoutDetails
	 *
	 * @param string   $token Verifizierungs-TOKEN
	 * @return array   Ergebnis-Array
	 * @author Daniel Quappe
	 */
	public function getExpressCheckoutDetails($token) {
		if (empty($token)) {
			return false;
		}
		return $this->_hashCall("GetExpressCheckoutDetails", array('TOKEN' => $token));
	}

	/**
	 * DoExpressCheckoutPayment
	 *
	 * @param array   $nvpDataArray Daten-Array
	 * @return array  Ergebnis-Array
	 * @author Daniel Quappe
	 */
	public function doExpressCheckoutPayment($dataArray = array()) {
		$nvpDataArray = array();
		//$nvpDataArray['TOKEN'] = $dataArray['token'];
		//$nvpDataArray['AMT'] = $dataArray['amount'];

		$defaults = array(
			'PAYMENTACTION' => 'Sale',
			'CURRENCYCODE' => $this->settings['currency_code'],
			'IPADDRESS' => env('REMOTE_ADDR'),
			'LOCALE' => $this->settings['locale'],
		);

		$map = array(
			'token' => 'TOKEN',
			'amount' => 'AMT',
			'id' => 'INVNUM',
			'custom' => 'CUSTOM',
			'payerid' => 'PAYERID',
			'desc' => 'DESC',
			'source' => 'BUTTONSOURCE',
			'fmf' => 'RETURNFMFDETAILS',
		);
		foreach ($dataArray as $key => $val) {
			if (isset($map[$key])) {
				$nvpDataArray[$map[$key]] = $val;
				unset($dataArray[$key]);
			}
		}
		$nvpDataArray['AMT'] = number_format($nvpDataArray['AMT'], 2, '.', '');
		if (isset($dataArray['notifyurl'])) {
			$nvpDataArray['NOTIFYURL'] = Router::url($dataArray['notifyurl'], true);
			unset($dataArray['notifyurl']);
		}
		$nvpDataArray = array_merge($defaults, $nvpDataArray, $dataArray);

		//$nvpDataArray['NOTIFYURL'] = ''; //	If you do not specify this value in the request, the notification URL from your Merchant Profile is used, if one exists
		//$nvpDataArray['DESC'] //Description of items the customer is purchasing.
		//$nvpDataArray['INVNUM'] //Your own unique invoice or tracking number. PayPal returns this value to you on DoExpressCheckoutPayment response
		//$nvpDataArray['CUSTOM'] //A free-form field for your own use, such as a tracking number or other value you want PayPal to return on

		return $this->_hashCall("DoExpressCheckoutPayment", $nvpDataArray);
	}

	/**
	 * process a credit card payment.
	 * @param array
	 * - amt, firstname, lastname, CREDITCARDTYPE, ACCT, street, zip, ...(required)
	 * - (optional)
	 */
	public function doDirectPayment($nvpDataArray = array()) {
		$nvpDataArray['PAYMENTACTION'] = 'Sale';
		$nvpDataArray['CURRENCYCODE'] = Configure::read('PayPal.currency_code');
		$nvpDataArray['IPADDRESS'] = env('SERVER_NAME');

		$nvpDataArray['RETURNFMFDETAILS'] = 1; // Fraud Management Filter details

		/*
		$nvpDataArray['CREDITCARDTYPE'] = '';
		$nvpDataArray['ACCT'] = '';
		//$nvpDataArray['EXPDATE'] = '';
		//$nvpDataArray['CVV2'] = '';
		$nvpDataArray['FIRSTNAME'] = '';
		$nvpDataArray['LASTNAME'] = '';
		$nvpDataArray['STREET'] = '';
		$nvpDataArray['CITY'] = '';
		$nvpDataArray['STATE'] = '';
		$nvpDataArray['COUNTRYCODE'] = '';
		$nvpDataArray['ZIP'] = '';
		*/
		return $this->_hashCall("DoDirectPayment", $nvpDataArray);
	}

	/**
	 * @param options:
	 * - all (true/false), defaults to false
	 * @return array('currency_code', 'amount', 'timestamp', 'ack', ...)
	 */
	public function getBalance($nvpDataArray = array()) {
		if (!isset($nvpDataArray['RETURNALLCURRENCIES'])) {
			if (empty($nvpDataArray['all']) || $nvpDataArray['all'] !== true) {
				$nvpDataArray['RETURNALLCURRENCIES'] = 0;
			} else {
				$nvpDataArray['RETURNALLCURRENCIES'] = 1;
			}
		}

		if (isset($nvpDataArray['all'])) {
			unset($nvpDataArray['all']);
		}
		$res = $this->_hashCall("GetBalance", $nvpDataArray);
		if (!empty($res['ACK']) && $res['ACK'] === 'Success') {
			$res['TIMESTAMP'] = strtotime($res['TIMESTAMP']);
			$res['TIME'] = date(FORMAT_DB_DATETIME, $res['TIMESTAMP']);

			# test (with only one amount)
			$res['AMOUNT'] = $res['L_AMT0'];
			$res['CURRENCY_CODE'] = $res['L_CURRENCYCODE0'];
		} else {
			$this->setError($res['Error']);
		}
		return $res;
	}

	/**
	 * array(Number=>..., Message=>...)
	 */
	public function setError($error) {
		die(returns($error));
	}

	/**
	 * @param string $id: identifier of transaction
	 * @param string $action: deny/accept (payment)
	 */
	public function managePendingTransactionStatus($id, $action = 'accept') {
		if (empty($id) || $action !== 'accept' && $action !== 'deny') {
			return false;
		}
		$nvpDataArray = array();
		$nvpDataArray['TRANSACTIONID'] = $id;
		$nvpDataArray['ACTION'] = ucfirst($action);
		return $this->_hashCall("ManagePendingTransactionStatus", $nvpDataArray);
	}

	/**
	 * @param string $id: identifier of transaction
	 * @return array('AMT', 'TRANSACTIONID', 'FEEAMT', 'CURRENCYCODE', 'PAYMENTSTATUS', 'PENDINGREASON', 'REASONCODE', 'ORDERTIME', 'PAYMENTTYPE', 'TRANSACTIONTYPE', ...)
	 */
	public function getTransactionDetails($id, $nvpDataArray = array()) {
		if (empty($id) || false) { //TODO: Character length and limitations: 17 single-byte alphanumeric characters.
			return false;
		}
		$nvpDataArray['TRANSACTIONID'] = $id;

		return $this->_hashCall("GetTransactionDetails", $nvpDataArray);
	}

	/**
	 * //TODO
	 * @param email, street, zip (all required)
	 */
	public function verifyAddress($nvpDataArray = array()) {
		//$nvpDataArray['EMAIL'];
		//$nvpDataArray['STREET'];
		//$nvpDataArray['ZIP'];
		//return $this->_hashCall("AddressVerify", $nvpDataArray);
	}

	//TODO
	public function billOutstandingAmount($nvpDataArray = array()) {

		//return $this->_hashCall("BillOutstandingAmount", $nvpDataArray)
	}

	//TODO
	public function refundTransaction($nvpDataArray = array()) {

		//return $this->_hashCall("RefundTransaction", $nvpDataArray)
	}

	//TODO
	public function getBillingAgreementCustomerDetails() {
		//return $this->_hashCall("GetBillingAgreementCustomerDetails", $nvpDataArray)
	}

	/**
	 * Zentrale cURL-Methode zur Kommunikation mit Paypal
	 *
	 * @param string  $methodName   API-Methode
	 * @param array   $nvpDataArray Name-Value-Pair-Data
	 * @return array  $nvpResArray  Ergebnis-Array
	 * @author Daniel Quappe
	 */
	public function _hashCall($methodName = '', $nvpDataArray = array()) {
		/* Init */
		$nvpReqArray = array();
		$nvpResArray = array();
		$response = null;

		/* PayPal-API-Credentials etc. */
		$nvpReqArray['METHOD'] = $methodName;
		$nvpReqArray['VERSION'] = self::VERSION; //Configure::read('PayPal.VERSION');
		$nvpReqArray['PWD'] = Configure::read('PayPal.password');
		$nvpReqArray['USER'] = Configure::read('PayPal.username');
		$nvpReqArray['SIGNATURE'] = Configure::read('PayPal.signature');

		// Setting the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->_api());
		curl_setopt($ch, CURLOPT_VERBOSE, 1);

		// Turning off the server and peer verification (TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);

		// If USE_PROXY constant set to TRUE in core.php, then only proxy will be enabled.
		// Set proxy name to PROXY_HOST and port number to PROXY_PORT in core.php
		if (Configure::read('PayPal.USE_PROXY') === true) {
			curl_setopt($ch, CURLOPT_PROXY, Configure::read('PayPal.PROXY_HOST') . ":" . Configure::read('PayPal.PROXY_PORT'));
		}

		/* Kombinieren der NVP-Daten mit den PayPal-Credentials
		* (inklusive Moeglichkeit zum Ueberschreiben einzelner Default-Werte, falls notwendig) */
		$nvpReqArray = array_merge($nvpReqArray, $nvpDataArray);

		//pr($nvpReqArray);

		// Setting the nvpReqArray as POST FIELD to curl
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($nvpReqArray, null, '&'));

		// Getting response from server
		$response = curl_exec($ch);

		// Converting $response to an Associative Array
		$nvpResArray = $this->_deformatNVP($response);

		if (curl_errno($ch)) {
			$nvpResArray = $this->_buildError(curl_errno($ch), curl_error($ch), $nvpResArray);
		} else {
			curl_close($ch);
		}
		return $nvpResArray;
	}

	/**
	 * Saves error parameters
	 *
	 * @param string  $errorNo  Error-Number
	 * @param string  $errorMsg Error-Description
	 * @param array   $resArray Data-Array
	 * @return array  $resArray Extended Data-Array
	 * @author Daniel Quappe
	 */
	public function _buildError($errorNo = '', $errorMsg = '', $resArray = array()) {
		$resArray['Error']['Number'] = $errorNo;
		$resArray['Error']['Message'] = $errorMsg;
		return $resArray;
	}

	/** This function will take NVPString and convert it to an Associative Array and it will decode the response.
	 * It is usefull to search for a particular key and displaying arrays.
	 * @nvpstr is NVPString.
	 * @nvpArray is Associative Array.
	 */
	public function _deformatNVP($nvpstr = '') {
		$intial = 0;
		$nvpArray = array();

		while (strlen($nvpstr)) {
			//postion of Key
			$keypos = strpos($nvpstr, '=');

			//position of value
			$valuepos = strpos($nvpstr, '&') ? strpos($nvpstr, '&') : strlen($nvpstr);

			/*getting the Key and Value values and storing in a Associative Array*/
			$keyval = substr($nvpstr, $intial, $keypos);
			$valval = substr($nvpstr, $keypos + 1, $valuepos - $keypos - 1);

			//decoding the respose
			$nvpArray[urldecode($keyval)] = urldecode($valval);
			$nvpstr = substr($nvpstr, $valuepos + 1, strlen($nvpstr));
		}
		return $nvpArray;
	}
}
