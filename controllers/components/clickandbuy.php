<?php

/**
 * 
 * 2010-09-19 ms
 */
class ClickandbuyComponent extends Object {

	var $components = array();

	var $controller = null;
	
	var $live = false;
	var $urls = array(
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

	
	function __construct() {
		parent::__construct();
		
		# modify urls if neccessary
	}


	/**
	 * Initialize component 
	 *  
	 * @access public 
	 * @return array 
	 * @author Daniel Quappe 
	 */
	function initialize(&$controller, $settings = array()) {
		/* Saving the controller reference for later use (as usual, if necessary) */
		$this->controller = &$controller;
	}
	
	/**
	 * go the express checkout
	 * 2010-09-19 ms
	 */
	function redirect() {
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
	function setExpressCheckout() {
		
	}

	/**
	 * GetExpressCheckoutDetails 
	 * 
	 * @param string   $token Verifizierungs-TOKEN 
	 * @return array   Ergebnis-Array 
	 * @access public 
	 * @author Daniel Quappe 
	 */
	function getExpressCheckoutDetails() {
		
	}


	function doExpressCheckoutPayment() {
		
	}
	

}
