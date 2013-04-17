<?php
App::uses('Component', 'Controller');

/**
 * @see http://code.google.com/p/phpclickandbuyapi/wiki/Documentation
 * 2010-09-19 ms
 */
class ClickandbuyComponent extends Component {

	public $components = array();

	public $controller = null;

	public $live = false;
	public $urls = array(
		'sandbox' => array(
			'url' => '',
			'api' => ''
		),
		'live' => array(
			'url' => '',
			'api' => ''
		),
		'ok' => '',
		'nok' => ''
	);
	const VERSION = 0;


	public function __construct(ComponentCollection $Collection, $settings = array()) {
		parent::__construct($Collection, $settings);

		# modify urls if neccessary
	}


	/**
	 * Initialize component
	 *
	 * @access public
	 * @return array
	 * @author Daniel Quappe
	 */
	public function initialize(Controller $controller, $settings = array()) {
		/* Saving the controller reference for later use (as usual, if necessary) */
		$this->controller = &$controller;
	}

	/**
	 * go the express checkout
	 * 2010-09-19 ms
	 */
	public function redirect() {
		$this->controller->redirect(Configure::read('Clickandbuy.Clickandbuy_URL').
		Router::querystring(array('cmd' => '_express-checkout')),
		'302'
	);
	}



	/**
	 * SetExpressCheckout
	 *
	 * @param array   $nvpDataArray Daten-Array
	 * @return array  Ergebnis-Array
	 * @access public
	 * @author Daniel Quappe
	 */
	public function setExpressCheckout() {

	}

	/**
	 * GetExpressCheckoutDetails
	 *
	 * @param string   $token Verifizierungs-TOKEN
	 * @return array   Ergebnis-Array
	 * @access public
	 * @author Daniel Quappe
	 */
	public function getExpressCheckoutDetails() {

	}


	public function doExpressCheckoutPayment() {

	}


}
