<?php
App::uses('PaymentAppController', 'Payment.Controller');
class PaypalController extends PaymentAppController {

	public $helpers = array('Tools.Numeric');

	public $uses = false;

	public function beforeFilter() {
		parent::beforeFilter();

		if (!Configure::read('PayPal')) {
			throw new NotFoundException();
		}
	}

/****************************************************************************************
 * ADMIN functions
 ****************************************************************************************/

	/**
	 * Paypal admin backend
	 * current paypal balance of the system account
	 * NOTE: everything else in in other plugin paypal_ipn - this is just for testing purposes right now
	 */
	public function admin_index() {
		//$this->Paypal = $this->Components->load('Payment.Paypal');
		$this->Common->loadComponent('Payment.Paypal');
		//App::import('Component', 'Payment.Paypal');
		//$this->Paypal = new PaypalComponent($this->Components);
		$this->Paypal->initialize($this);
		$balance = $this->Paypal->getBalance(array('all' => true));

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
