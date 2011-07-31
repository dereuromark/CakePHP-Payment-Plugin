<?php
class BitcoinTransactionsController extends AppController {

	var $name = 'BitcoinTransactions';
	var $paginate = array();

	function beforeFilter() {
		parent::beforeFilter();
	}



/****************************************************************************************
 * USER functions
 ****************************************************************************************/


/****************************************************************************************
 * ADMIN functions
 ****************************************************************************************/

	function admin_index() {
		$this->BitcoinTransaction->recursive = 0;
		$bitcoinTransactions = $this->paginate();
		$this->set(compact('bitcoinTransactions'));
	}

	function admin_view($id = null) {
		if (empty($id) || !($bitcoinTransaction = $this->BitcoinTransaction->find('first', array('conditions'=>array('BitcoinTransaction.id'=>$id))))) {
			$this->Common->flashMessage(__('invalid record', true), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		$this->set(compact('bitcoinTransaction'));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->BitcoinTransaction->create();
			if ($this->BitcoinTransaction->save($this->data)) {
				$var = $this->data['BitcoinTransaction']['amount'];
				$this->Common->flashMessage(sprintf(__('record add %s saved', true), h($var)), 'success');
				$this->Common->postRedirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors', true), 'error');
			}
		}
		$addresses = $this->BitcoinTransaction->Address->find('list');
		$this->set(compact('addresses'));
	}

	function admin_edit($id = null) {
		if (empty($id) || !($bitcoinTransaction = $this->BitcoinTransaction->find('first', array('conditions'=>array('BitcoinTransaction.id'=>$id))))) {
			$this->Common->flashMessage(__('invalid record', true), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->BitcoinTransaction->save($this->data)) {
				$var = $this->data['BitcoinTransaction']['amount'];
				$this->Common->flashMessage(sprintf(__('record edit %s saved', true), h($var)), 'success');
				$this->Common->postRedirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors', true), 'error');
			}
		}
		if (empty($this->data)) {
			$this->data = $bitcoinTransaction;
		}
		$addresses = $this->BitcoinTransaction->Address->find('list');
		$this->set(compact('addresses'));
	}

	function admin_delete($id = null) {
		if (empty($id) || !($bitcoinTransaction = $this->BitcoinTransaction->find('first', array('conditions'=>array('BitcoinTransaction.id'=>$id), 'fields'=>array('id', 'amount'))))) {
			$this->Common->flashMessage(__('invalid record', true), 'error');
			$this->Common->autoRedirect(array('action'=>'index'));
		}
		$var = $bitcoinTransaction['BitcoinTransaction']['amount'];
		
		if ($this->BitcoinTransaction->delete($id)) {
			$this->Common->flashMessage(sprintf(__('record del %s done', true), h($var)), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Common->flashMessage(sprintf(__('record del %s not done exception', true), h($var)), 'error');
		$this->Common->autoRedirect(array('action' => 'index'));
	}



/****************************************************************************************
 * protected/interal functions
 ****************************************************************************************/


/****************************************************************************************
 * deprecated/test functions
 ****************************************************************************************/

}
