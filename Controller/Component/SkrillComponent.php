<?php
App::uses('Component', 'Controller');

/**
 * Skrill alias Moneybookers
 */
class SkrillComponent extends Component {

	/* Benutzte Zusatz-Komponenten */
	public $components = array();

	/* Klassen-Member */
	public $controller = null;

	public $urls = array(
		'sandbox' => 'http://www.moneybookers.com/app/test_payment.pl',
		'live' => 'https://www.moneybookers.com/app/payment.pl',
		//'ok' => '',
		//'nok' => ''
	);

	public $settings = array(
		'live' => false,
		'currency_code' => 'EUR',
		'locale' => 'DE',
		'email' => '',
		'name' => '',
		//'shipping' => false, # do not ask for address etc
		'prepare_only' => 1,
		'img' => '',
		'notifyurl' => array('admin' => false, 'plugin' => 'payment', 'controller' => 'transactions', 'action' => 'notify', 'skrill'),
	);

	const MAX_LOGO_WIDTH = 200;
	const MAX_LOGO_HEIGHT = 50;

	/*
	public function __construct(ComponentCollection $Collection, $settings = array()) {
		parent::__construct($Collection, $settings);

	}
	*/

	/**
	 * Initialize component
	 *
	 * @return array
	 * @author Daniel Quappe
	 */
	public function initialize(Controller $controller, $settings = array()) {
		/* Saving the controller reference for later use (as usual, if necessary) */

		$configSettings = (array)Configure::read('Skrill');
		$this->settings = array_merge($this->settings, $configSettings, $settings);
		# modify urls if neccessary

		$this->controller = &$controller;
	}

	/**
	 * Go the express checkout
	 *
	 * @oparam token $sId from setExpressCheckout
	 * @see http://www.moneybookers.com/merchant/de/moneybookers_gateway_manual.pdf
	 */
	public function redirect($token) {
		$this->controller->redirect($this->_url() .
		Router::querystring(array('sid' => $token)),
		'302'
	);
	}

	/**
	 * Get current settings
	 *
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
		return $this->urls[$type];
	}

	/**
	 * @param path
	 * @return boolean success
	 * The image has a maximum size of 200 pixels wide by 50 pixels high.
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
	 *
	 * @see http://www.moneybookers.com/merchant/de/moneybookers_gateway_manual.pdf
	 *
	 * @param array   $nvpDataArray Daten-Array
	 * - amount, cancelurl, successurl, detail1_description, detail1_test (all necessary, url as array)
	 * - confirmation_node, 'detail2-5_description', 'detail2-5_text'
	 * @return array  Ergebnis-Array
	 * @author Daniel Quappe
	 */
	public function setExpressCheckout($dataArray = array()) {
		$defaults = array(
			'currency' => $this->settings['currency_code'],
			'language' => $this->settings['locale'],
			'pay_to_email' => $this->settings['email'],
			'recipient_description' => $this->settings['name'],
			'prepare_only' => (int)$this->settings['prepare_only'],
			'status_url' => $this->settings['notifyurl'],
		);
		$nvpDataArray = array();

		$map = array(
			'currency_code' => 'currency',
			'locale' => 'language',
			'successurl' => 'return_url',
			'cancelurl' => 'cancel_url',
			'notifyurl' => 'status_url',
			//'img' => 'logo_url',
		);
		foreach ($dataArray as $key => $val) {
			if (isset($map[$key])) {
				$nvpDataArray[$map[$key]] = $val;
				unset($dataArray[$key]);
			}
		}
		$nvpDataArray = array_merge($defaults, $nvpDataArray, $dataArray);
		$nvpDataArray['amount'] = number_format($nvpDataArray['amount'], 2, '.', '');
		if (($hdrimg = $this->settings['img'])) {
			$nvpDataArray['logo_url'] = $hdrimg;
		}
		if (isset($nvpDataArray['status_url'])) {
			$nvpDataArray['status_url'] = Router::url($nvpDataArray['status_url'], true);
		}
		if (isset($nvpDataArray['cancel_url'])) {
			$nvpDataArray['cancel_url'] = Router::url($nvpDataArray['cancel_url'], true);
		}
		if (isset($nvpDataArray['return_url'])) {
			$nvpDataArray['return_url'] = Router::url($nvpDataArray['return_url'], true);
		}

		//echo returns($nvpDataArray); die();
		App::uses('HttpSocket', 'Network/Http');
		$HttpSocket = new HttpSocket();
		$sId = $HttpSocket->post($this->_url(), $nvpDataArray);
		//echo returns($sId);
		//echo returns($HttpSocket->response);
		//die();
		if (empty($sId)) {
			return false;
		}
		return $sId;
		//return $this->_hashCall("SetExpressCheckout", $nvpDataArray);
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
		//$nvpDataArray['INVNUM'] //Your own unique invoice or tracking number. Skrill returns this value to you on DoExpressCheckoutPayment response
		//$nvpDataArray['CUSTOM'] //A free-form field for your own use, such as a tracking number or other value you want Skrill to return on

		return $this->_hashCall("DoExpressCheckoutPayment", $nvpDataArray);
	}

	/**
	 * array(Number=>..., Message=>...)
	 */
	public function setError($error) {
		die(returns($error));
	}

	/**
	 * Zentrale cURL-Methode zur Kommunikation mit Skrill
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

		/* Skrill-API-Credentials etc. */

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
		if (Configure::read('Skrill.USE_PROXY') === true) {
			curl_setopt($ch, CURLOPT_PROXY, Configure::read('Skrill.PROXY_HOST') . ":" . Configure::read('Skrill.PROXY_PORT'));
		}

		/* Kombinieren der NVP-Daten mit den Credentials
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
	 *
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

	const STATUS_PENDING = '0';
	const STATUS_PROCESSED = '2';
	const STATUS_CANCELED = '-1';
	const STATUS_CHARGEBACK = '-3';

}
