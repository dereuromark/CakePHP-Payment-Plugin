<?php
App::uses('Component', 'Controller');

/**
 * @see http://code.google.com/intl/de-DE/apis/checkout/samples/Google_Checkout_Sample_Code_PHP.html
 */
class GoogleComponent extends Component {

	public $components = array();

	public $controller = null;

	public $live = false;

	public $urls = array(
		'sandbox' => 'https://sandbox.google.com/checkout',
		'live' => 'https://checkout.google.com',
	);
	const VERSION = 0;
	const SCHEMA = 'http://checkout.google.com/schema/2';

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

	public function __construct(ComponentCollection $Collection, $settings = array()) {
		parent::__construct($Collection, $settings);
		# modify urls if neccessary
	}

	/**
	 * Initialize component
	 *
	 * @return array
	 * @author Daniel Quappe
	 */
	public function initialize(Controller $controller, $settings = array()) {
		/* Saving the controller reference for later use (as usual, if necessary) */
		$this->controller = &$controller;
	}

	/**
	 * go the express checkout
	 */
	public function redirect() {
		$this->controller->redirect(Configure::read('Paysafecard.Paysafecard_URL') .
		Router::querystring(array('cmd' => '_express-checkout')),
		'302'
	);
	}

	/**
	 * SetExpressCheckout
	 *
	 * @param array   $nvpDataArray Daten-Array
	 * @return array  Ergebnis-Array
	 * @author Daniel Quappe
	 */
	public function setExpressCheckout() {
	}

	/**
	 * GetExpressCheckoutDetails
	 *
	 * @param string   $token Verifizierungs-TOKEN
	 * @return array   Ergebnis-Array
	 * @author Daniel Quappe
	 */
	public function getExpressCheckoutDetails() {
	}

	public function doExpressCheckoutPayment() {
	}

	public function GetCurlResponse($request, $postUrl) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $postUrl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

	/*
	 * This "if" block, which sets the HTTP Basic Authentication scheme
	 * and HTTP headers, only executes for Order Processing API requests
	 * and for server-to-server Checkout API requests.
	 */
	$pos = strpos($postUrl, "request");
	if ($pos == true) {

		// Set HTTP Basic Authentication scheme
		curl_setopt($ch, CURLOPT_USERPWD, $GLOBALS["merchant_id"] .
			":" . $GLOBALS["merchant_key"]);

		// Set HTTP headers
		$header = array();
		$header[] = "Content-type: application/xml";
		$header[] = "Accept: application/xml";
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

	}

	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $request);

	// Execute the API request.
	$response = curl_exec($ch);

	/*
	 * Verify that the request executed successfully. Note that a
	 * successfully executed request does not mean that your request
	 * used properly formed XML.
	 */
	if (curl_errno($ch)) {
		trigger_error(curl_error($ch), E_USER_ERROR);
	} else {
		curl_close($ch);
	}

	// Return the response to the API request
	return $response;
	}

}
