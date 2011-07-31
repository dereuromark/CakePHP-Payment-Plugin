<?php
class BitcoinAddressesController extends PaymentAppController {

	var $name = 'BitcoinAddresses';
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
		$this->BitcoinAddress->recursive = 0;
		$bitcoinAddresses = $this->paginate();
		$this->set(compact('bitcoinAddresses'));
	}

	function admin_view($id = null) {
		if (empty($id) || !($bitcoinAddress = $this->BitcoinAddress->find('first', array('conditions'=>array('BitcoinAddress.id'=>$id))))) {
			$this->Common->flashMessage(__('invalid record', true), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		$this->set(compact('bitcoinAddress'));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->BitcoinAddress->create();
			if ($this->BitcoinAddress->save($this->data)) {
				$var = $this->data['BitcoinAddress']['address'];
				$this->Common->flashMessage(sprintf(__('record add %s saved', true), h($var)), 'success');
				$this->Common->postRedirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors', true), 'error');
			}
		}
		$users = $this->BitcoinAddress->User->find('list');
		$this->set(compact('users'));
	}

	function admin_edit($id = null) {
		if (empty($id) || !($bitcoinAddress = $this->BitcoinAddress->find('first', array('conditions'=>array('BitcoinAddress.id'=>$id))))) {
			$this->Common->flashMessage(__('invalid record', true), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->BitcoinAddress->save($this->data)) {
				$var = $this->data['BitcoinAddress']['address'];
				$this->Common->flashMessage(sprintf(__('record edit %s saved', true), h($var)), 'success');
				$this->Common->postRedirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors', true), 'error');
			}
		}
		if (empty($this->data)) {
			$this->data = $bitcoinAddress;
		}
		$users = $this->BitcoinAddress->User->find('list');
		$this->set(compact('users'));
	}

	function admin_delete($id = null) {
		if (empty($id) || !($bitcoinAddress = $this->BitcoinAddress->find('first', array('conditions'=>array('BitcoinAddress.id'=>$id), 'fields'=>array('id', 'address'))))) {
			$this->Common->flashMessage(__('invalid record', true), 'error');
			$this->Common->autoRedirect(array('action'=>'index'));
		}
		$var = $bitcoinAddress['BitcoinAddress']['address'];
		
		if ($this->BitcoinAddress->delete($id)) {
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
