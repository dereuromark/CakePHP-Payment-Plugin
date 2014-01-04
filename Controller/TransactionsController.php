<?php
App::uses('PaymentAppController', 'Payment.Controller');
class TransactionsController extends PaymentAppController {

	public $paginate = array();

	public function beforeFilter() {
		if (isset($this->Auth)) {
			$this->Auth->allow('notify');
		}
		parent::beforeFilter();
	}

/****************************************************************************************
 * USER functions
 ****************************************************************************************/

/*
	public function index() {
		$this->Transaction->recursive = 0;
		//$this->paginate['conditions']['']

		$transactions = $this->paginate();
		$this->set(compact('transactions'));
	}
*/

	public function view($id = null) {
		$this->Transaction->recursive = 0;
		if (empty($id) || !($transaction = $this->Transaction->find('first', array('conditions' => array('Transaction.id' => $id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		$this->Record = ClassRegistry::init('Payment.' . $transaction['Transaction']['model']);
		$record = $this->Record->get($transaction['Transaction']['foreign_id']);
		if (!$record || $record[$transaction['Transaction']['model']]['user_id'] != $this->Session->read('Auth.User.id')) {
			die('Invalid Access');
		}

		if ($this->Common->isPosted()) {
			if ($this->request->data['Transaction']['confirm']) {
				$this->Transaction->id = $id;
				$this->Transaction->saveField('payment_status', Transaction::STATUS_PENDING);
				$this->Common->flashMessage('Sobald der Geldeingang bestätigt ist, wird die Transaktion abgeschlossen.', 'success');
				if ($transaction['Transaction']['model'] === 'PrepaidAccount') {
					$this->Common->postRedirect(array('controller' => 'prepaid_accounts', 'action' => 'view'));
				}
				$this->Common->postRedirect(array('action' => 'view', $id));
			}
		}

		$this->set(compact('transaction'));
	}

/*
	public function edit($id = null) {
		if (empty($id) || !($transaction = $this->Transaction->find('first', array('conditions'=>array('Transaction.id'=>$id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		if ($this->Common->isPosted()) {
			if ($this->Transaction->save($this->request->data)) {
				$var = $this->request->data['Transaction']['title'];
				$this->Common->flashMessage(__('record edit %s saved', h($var)), 'success');
				$this->Common->postRedirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors'), 'error');
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $transaction;
		}
	}

	public function delete($id = null) {
		if (!$this->Common->isPosted()) {
			throw new MethodNotAllowedException();
		}
		if (empty($id) || !($transaction = $this->Transaction->find('first', array('conditions'=>array('Transaction.id'=>$id), 'fields'=>array('id', 'title'))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action'=>'index'));
		}
		$var = $transaction['Transaction']['title'];

		if ($this->Transaction->delete($id)) {
			$this->Common->flashMessage(__('record del %s done', h($var)), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Common->flashMessage(__('record del %s not done exception', h($var)), 'error');
		$this->Common->autoRedirect(array('action' => 'index'));
	}
*/

	/**
	 * note: paymentMethod required
	 */
	public function notify($type = null) {
		//TODO

		//currently logging for manual debug
		$res = array();
		$res['params'] = $this->request->params;
		$res['data'] = $this->request->data;
		$res['get'] = $_GET;
		$res['post'] = $_POST;
		if (!file_exists(LOGS . 'payment')) {
			mkdir(LOGS . 'payment', 0755);
		}
		$name = strtolower($type) . '_' . date(FORMAT_DB_DATE) . '_' . date('H-i-s');
		while (file_exists(LOGS . 'payment' . DS . $name . (!empty($i) ? '_' . $i : '') . '.txt')) {
			$i = (int)$i + 1;
		}
		file_put_contents(LOGS . 'payment' . DS . $name . (!empty($i) ? '_' . $i : '') . '.txt', print_r($res, true));
		die('');
	}

/****************************************************************************************
 * ADMIN functions
 ****************************************************************************************/

	public function admin_index() {
		$this->Transaction->recursive = 0;
		$transactions = $this->paginate();
		$this->set(compact('transactions'));
	}

	public function admin_view($id = null) {
		$this->Transaction->recursive = 0;
		if (empty($id) || !($transaction = $this->Transaction->find('first', array('conditions' => array('Transaction.id' => $id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		$this->set(compact('transaction'));
	}

	public function admin_add() {
		if ($this->Common->isPosted()) {
			$this->Transaction->create();
			if ($this->Transaction->save($this->request->data)) {
				$var = $this->request->data['Transaction']['title'];
				$this->Common->flashMessage(__('record add %s saved', h($var)), 'success');
				$this->Common->postRedirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors'), 'error');
			}
		}
	}

	public function admin_edit($id = null) {
		if (empty($id) || !($transaction = $this->Transaction->find('first', array('conditions' => array('Transaction.id' => $id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		if ($this->Common->isPosted()) {
			if ($this->Transaction->save($this->request->data)) {
				$var = $this->request->data['Transaction']['title'];
				$this->Common->flashMessage(__('record edit %s saved', h($var)), 'success');
				$this->Common->postRedirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors'), 'error');
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $transaction;
		}
	}

	public function admin_delete($id = null) {
		if (!$this->Common->isPosted()) {
			throw new MethodNotAllowedException();
		}
		if (empty($id) || !($transaction = $this->Transaction->find('first', array('conditions' => array('Transaction.id' => $id), 'fields' => array('id', 'title'))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		$var = $transaction['Transaction']['title'];

		if ($this->Transaction->delete($id)) {
			$this->Common->flashMessage(__('record del %s done', h($var)), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Common->flashMessage(__('record del %s not done exception', h($var)), 'error');
		$this->Common->autoRedirect(array('action' => 'index'));
	}

/****************************************************************************************
 * protected/interal functions
 ****************************************************************************************/

/****************************************************************************************
 * deprecated/test functions
 ****************************************************************************************/

}
