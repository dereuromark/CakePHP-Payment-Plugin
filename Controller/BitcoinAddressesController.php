<?php
App::uses('PaymentAppController', 'Payment.Controller');

class BitcoinAddressesController extends PaymentAppController {

	public $paginate = array();

	public function beforeFilter() {
		parent::beforeFilter();
	}

	public function admin_index() {
		$this->BitcoinAddress->recursive = 0;
		$bitcoinAddresses = $this->paginate();
		$this->set(compact('bitcoinAddresses'));
	}

	public function admin_view($id = null) {
		if (empty($id) || !($bitcoinAddress = $this->BitcoinAddress->find('first', array('conditions' => array('BitcoinAddress.id' => $id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		$this->set(compact('bitcoinAddress'));
	}

	public function admin_add() {
		if ($this->Common->isPosted()) {
			$this->BitcoinAddress->create();
			if ($this->BitcoinAddress->save($this->request->data)) {
				$var = $this->request->data['BitcoinAddress']['address'];
				$this->Common->flashMessage(__('record add %s saved', h($var)), 'success');
				$this->Common->postRedirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors'), 'error');
			}
		}
	}

	public function admin_edit($id = null) {
		if (empty($id) || !($bitcoinAddress = $this->BitcoinAddress->find('first', array('conditions' => array('BitcoinAddress.id' => $id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		if ($this->Common->isPosted()) {
			if ($this->BitcoinAddress->save($this->request->data)) {
				$var = $this->request->data['BitcoinAddress']['address'];
				$this->Common->flashMessage(__('record edit %s saved', h($var)), 'success');
				$this->Common->postRedirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors'), 'error');
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $bitcoinAddress;
		}
	}

	public function admin_delete($id = null) {
		if (!$this->Common->isPosted()) {
			throw new MethodNotAllowedException();
		}
		if (empty($id) || !($bitcoinAddress = $this->BitcoinAddress->find('first', array('conditions' => array('BitcoinAddress.id' => $id), 'fields' => array('id', 'address'))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		$var = $bitcoinAddress['BitcoinAddress']['address'];

		if ($this->BitcoinAddress->delete($id)) {
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
