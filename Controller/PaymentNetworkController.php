<?php
App::uses('PaymentAppController', 'Payment.Controller');
class PaymentNetworkController extends PaymentAppController {

	public $helpers = array('Tools.Numeric');

	public $uses = array();

	public function beforeFilter() {
		parent::beforeFilter();

		if (!Configure::read('PaymentNetwork')) {
			throw new NotFoundException();
		}

		if (isset($this->Auth)) {
			$this->Auth->allow('abort', 'success', 'notification');
		}
	}

	public function success($id = null) {
		$Transaction = ClassRegistry::init('Payment.Transaction');
		if (!$id || !($tId = $Transaction->field('id', array('transaction_id' => $id)))) {
			throw new NotFoundException();
		}
		$this->log('OK: ' . $id . ' - ' . returns($this->request->params), 'payment');
		$this->_afterSuccessRedirect($tId);
	}

	public function abort($id = null) {
		$this->log('ABORT: ' . $id . ' - ' . returns($this->request->params), 'payment');
		$this->redirect(array('plugin' => '', 'controller' => 'orders', 'action' => 'cancel', $id));
	}

	//use abort?
	/*
	public function timeout($id = null) {
		$this->log('TIMEOUT: '.$id.' - '.returns($this->request->params), 'payment');
		die('TIMEOUT');
	}
	*/

	/**
	 * needs to ACK with a 200 code
	 */
	public function notification($tId = null) {
		if (empty($this->request->data)) {
			throw new MethodNotAllowedException();
		}
		App::import('Component', 'Payment.PaymentNetwork');
		$this->PaymentNetwork = new PaymentNetworkComponent($this->Components);
		$this->PaymentNetwork->initialize($this);
		$response = $this->PaymentNetwork->classicResponse();
		if ($response->isError()) {
			$this->log('Payment ' . $tId . ': ' . $response->getError(), 'sofortbanking');
			throw new MethodNotAllowedException();
		}
		$foreignId = $response->getUserVariable(0);
		$model = $response->getUserVariable(1);
		$transactionId = $response->getTransaction();
		if (empty($transactionId)) {
			throw new NotFoundException();
		}
		$this->Transaction = ClassRegistry::init('Payment.Transaction');
		$transaction = $this->Transaction->find('first', array(
			'conditions' => array('model' => $model, 'foreign_id' => $foreignId),
			//'order' => array('status')
		));
		if (empty($transaction)) {
			throw new NotFoundException();
		}
		# update transaction - we finally have a transaction id:
		$data = array(
			'transaction_id' => $transactionId,
			'payment_status' => 'Completed', //$response->getStatus(),
			//'reason_code' => $response->getStatusReason(),
			//'status' => $this->PaymentNetwork->translateStatus($response->getStatus())
			'status' => Transaction::STATUS_COMPLETED,
		);
		$this->Transaction->updateSofortbanking($transaction['Transaction']['id'], $data);

		$this->_afterSofortbankingNotification($transaction['Transaction']['id']);

		$this->response->body(__('sofortbankingSuccessful'));
		$this->response->send();
		die();
	}

/*
EXAMPLE DATA
	[transaction] => 47747-118264-4EFC8664-1328
	[user_id] => 47747
	[project_id] => 118264
	[sender_holder] => Musterman, Petra
	[sender_account_number] => 23XXXXXX02
	[sender_bank_code] => 888XXXXX
	[sender_bank_name] => Demo Bank
	[sender_bank_bic] => PNAGXXXXXXX
	[sender_iban] => DE96888XXXXXXXXXXXXX02
	[sender_country_id] => DE
	[recipient_holder] => ordofood GmbH
	[recipient_account_number] => 2XXXXX26
	[recipient_bank_code] => 702XXXXX
	[recipient_bank_name] => KreissparkXXXXX
	[recipient_bank_bic] => BYLAXXXXXXX
	[recipient_iban] => DE44702XXXXXXXXXXXXX26
	[recipient_country_id] => DE
	[international_transaction] => 0
	[amount] => 4.99
	[currency_id] => EUR
	[reason_1] => Bestellung
	[reason_2] => Food Vouchers
	[security_criteria] => 1
	[user_variable_0] => 4efc84a5-7be0-4764-b2fe-2c5a51a9ad11
	[user_variable_1] =>
	[user_variable_2] =>
	[user_variable_3] => http://test.ordofood.de/payment/payment_network/success
	[user_variable_4] => http://test.ordofood.de/payment/payment_network/abort
	[user_variable_5] => http://test.ordofood.de/payment/payment_network/notification
	[email_recipient] =>
	[created] => 2011-12-29 16:25:55
	[hash] => 950613d2ad9ca1b7dd4d85145c9e8c3dda97d4a6
*/

/****************************************************************************************
 * ADMIN functions
 ****************************************************************************************/

	/**
	 *
	 */
	public function admin_index() {
		App::import('Component', 'Payment.PaymentNetwork');
		$this->PaymentNetwork = new PaymentNetworkComponent($this->Components);
		$this->PaymentNetwork->initialize($this);
		$transactions = $this->PaymentNetwork->getLastTransactions();
		die(returns($transactions));
	}

/****************************************************************************************
 * protected/internal functions
 ****************************************************************************************/

/****************************************************************************************
 * deprecated/test functions
 ****************************************************************************************/

}
