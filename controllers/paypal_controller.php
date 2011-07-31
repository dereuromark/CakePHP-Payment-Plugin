<?php
class PaypalController extends PaymentAppController {

	var $name = 'Paypal';
	var $helpers = array('Tools.Numeric');
	var $uses = array();

	function beforeFilter() {
		parent::beforeFilter();
		
		# temporary
		if (isset($this->AuthExt)) {
			//$this->AuthExt->allow('*');
		}
	}



/****************************************************************************************
 * ADMIN functions
 ****************************************************************************************/

	/**
	 * paypal admin backend
	 * current paypal balance of the system account
	 * NOTE: everything else in in other plugin paypal_ipn - this is just for testing purposes right now
	 * 2011-07-30 ms
	 */
	function admin_index() {
		App::import('Component', 'Payment.Paypal');
		$this->Paypal = new PaypalComponent();
		$balance = $this->Paypal->getBalance(array('all'=>true));

		$image = array();
		if (($img = Configure::read('PayPal.img'))) {
			$image['result'] = $this->Paypal->validateImage($img);
			$image['url'] = $img;
		}

		$this->set(compact('balance', 'image'));

	}

/****************************************************************************************
 * protected/internal functions
 ****************************************************************************************/





/****************************************************************************************
 * deprecated/test functions
 ****************************************************************************************/


}

