<?php
class PrepaidAccountsController extends PaymentAppController {

	var $name = 'PrepaidAccounts';
	var $helpers = array('Tools.Numeric');
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
		$this->PrepaidAccount->recursive = 0;
		$prepaidAccounts = $this->paginate();
		$this->set(compact('prepaidAccounts'));
	}

	function admin_view($id = null) {
		if (empty($id) || !($prepaidAccount = $this->PrepaidAccount->find('first', array('contain'=>array('User'), 'conditions'=>array('PrepaidAccount.id'=>$id))))) {
			$this->Common->flashMessage(__('invalid record', true), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		$logEntries = $this->PrepaidAccount->findLog();
		$this->set(compact('prepaidAccount', 'logEntries'));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->PrepaidAccount->create();
			if ($this->PrepaidAccount->save($this->data)) {
				$var = $this->data['PrepaidAccount']['amount'];
				if (Configure::read('PrepaidAccount.callback')) {
					$this->PrepaidAcount->User->afterPrepaidChange($this->PrepaidAccount->id, null, $var);
				}
				
				$this->Common->flashMessage(sprintf(__('record add %s saved', true), h($var)), 'success');
				$this->Common->postRedirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors', true), 'error');
			}
		}
		$users = $this->PrepaidAccount->User->find('list');
		$this->set(compact('users'));
	}

	function admin_edit($id = null) {
		if (empty($id) || !($prepaidAccount = $this->PrepaidAccount->find('first', array('conditions'=>array('PrepaidAccount.id'=>$id))))) {
			$this->Common->flashMessage(__('invalid record', true), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->PrepaidAccount->save($this->data)) {
				$var = $this->data['PrepaidAccount']['amount'];
				if (Configure::read('PrepaidAccount.callback')) {
					$this->PrepaidAcount->User->afterPrepaidChange($this->PrepaidAccount->id, $prepaidAccount['PrepaidAccount']['amount'], $var);
				}
				
				$this->Common->flashMessage(sprintf(__('record edit %s saved', true), h($var)), 'success');
				$this->Common->postRedirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors', true), 'error');
			}
		}
		if (empty($this->data)) {
			$this->data = $prepaidAccount;
		}
		$users = $this->PrepaidAccount->User->find('list');
		$this->set(compact('users'));
	}

	function admin_delete($id = null) {
		if (empty($id) || !($prepaidAccount = $this->PrepaidAccount->find('first', array('conditions'=>array('PrepaidAccount.id'=>$id), 'fields'=>array('id', 'amount'))))) {
			$this->Common->flashMessage(__('invalid record', true), 'error');
			$this->Common->autoRedirect(array('action'=>'index'));
		}
		$var = $prepaidAccount['PrepaidAccount']['amount'];
		
		if ($this->PrepaidAccount->delete($id)) {
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
