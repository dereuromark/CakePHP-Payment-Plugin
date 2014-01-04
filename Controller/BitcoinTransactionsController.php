<?php
//App::uses('AppController', 'Controller');
App::uses('PaymentAppController', 'Payment.Controller');

class BitcoinTransactionsController extends PaymentAppController {

	public $paginate = array();

	public function beforeFilter() {
		parent::beforeFilter();
	}

/****************************************************************************************
 * USER functions
 ****************************************************************************************/

/****************************************************************************************
 * ADMIN functions
 ****************************************************************************************/

	public function admin_index() {
		$this->BitcoinTransaction->recursive = 0;
		$bitcoinTransactions = $this->paginate();
		$this->set(compact('bitcoinTransactions'));
	}

	public function admin_view($id = null) {
		if (empty($id) || !($bitcoinTransaction = $this->BitcoinTransaction->find('first', array('conditions' => array('BitcoinTransaction.id' => $id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		$this->set(compact('bitcoinTransaction'));
	}

	public function admin_add() {
		if ($this->Common->isPosted()) {
			$this->BitcoinTransaction->create();
			if ($this->BitcoinTransaction->save($this->request->data)) {
				$var = $this->request->data['BitcoinTransaction']['amount'];
				$this->Common->flashMessage(__('record add %s saved', h($var)), 'success');
				$this->Common->postRedirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors'), 'error');
			}
		}
		$addresses = $this->BitcoinTransaction->Address->find('list');
		$this->set(compact('addresses'));
	}

	public function admin_edit($id = null) {
		if (empty($id) || !($bitcoinTransaction = $this->BitcoinTransaction->find('first', array('conditions' => array('BitcoinTransaction.id' => $id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		if ($this->Common->isPosted()) {
			if ($this->BitcoinTransaction->save($this->request->data)) {
				$var = $this->request->data['BitcoinTransaction']['amount'];
				$this->Common->flashMessage(__('record edit %s saved', h($var)), 'success');
				$this->Common->postRedirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors'), 'error');
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $bitcoinTransaction;
		}
		$addresses = $this->BitcoinTransaction->Address->find('list');
		$this->set(compact('addresses'));
	}

	public function admin_delete($id = null) {
		if (!$this->Common->isPosted()) {
			throw new MethodNotAllowedException();
		}
		if (empty($id) || !($bitcoinTransaction = $this->BitcoinTransaction->find('first', array('conditions' => array('BitcoinTransaction.id' => $id), 'fields' => array('id', 'amount'))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		$var = $bitcoinTransaction['BitcoinTransaction']['amount'];

		if ($this->BitcoinTransaction->delete($id)) {
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
