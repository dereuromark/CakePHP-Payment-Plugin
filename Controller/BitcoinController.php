<?php
App::uses('PaymentAppController', 'Payment.Controller');

class BitcoinController extends PaymentAppController {

	public $helpers = array('Tools.Numeric');

	public $uses = array('Payment.BitcoinTransaction');

	public function beforeFilter() {
		parent::beforeFilter();

		# temporary
		if (isset($this->Auth)) {
			//$this->Auth->allow();
		}
	}

/****************************************************************************************
 * ADMIN functions
 ****************************************************************************************/

	/**
	 * bitcoin admincenter (main overview)
	 */
	public function admin_index() {
		$details = $infos = array();
		try {
			if (Configure::read('Bitcoin.username') && Configure::read('Bitcoin.password')) {
				$details = array(
					'accounts' => $this->BitcoinTransaction->Bitcoin->listAccounts(),
					//'account' => $this->BitcoinTransaction->Bitcoin->getAccountAddress(),
					'active' => Configure::read('Bitcoin.account'),
					'addresses' => $this->BitcoinTransaction->Bitcoin->getAddressesByAccount(),
				);
				$infos = $this->BitcoinTransaction->Bitcoin->getInfo();
			} else {
				$this->Common->flashMessage('Zugangsdaten fehlen. Kann keine Verbindung aufbauen.', 'warning');
			}
		} catch (BitcoinClientException $e) {
			$this->Common->flashMessage($e->getMessage(), 'error');
		}

		if ($this->Common->isPosted()) {
			try {
				$this->BitcoinTransaction->set($this->request->data);
				if ($this->BitcoinTransaction->validates()) {
					$addressDetails = array();
					if (Configure::read('Bitcoin.username') && Configure::read('Bitcoin.password')) {
						$addressDetails['firstSeen'] = $this->BitcoinTransaction->Bitcoin->addressFirstSeen($this->request->data['BitcoinAddress']['address']);

					}
					$this->Common->flashMessage('Valid', 'success');
				} else {
					$this->Common->flashMessage('Invalid', 'error');
				}
			} catch (BitcoinClientException $e) {
				$this->Common->flashMessage($e->getMessage(), 'error');
			}
		}

		$this->set(compact('infos', 'details'));
	}

	public function admin_address_details($address = null) {
		if (empty($address) || !($this->BitcoinTransaction->set(array('address' => $address)) && $this->BitcoinTransaction->validates())) {
			$this->Common->autoRedirect(array('action' => 'index'));
		}

		//TODO
	}

	public function admin_transfer() {
		$accounts = $this->BitcoinTransaction->Bitcoin->listAccounts();
		$addresses = $this->BitcoinTransaction->Bitcoin->getAddressesByAccount($this->BitcoinTransaction->ownAccount());
		$ownAddresses = $this->BitcoinTransaction->addressList($addresses);
		$ownAccounts = $this->BitcoinTransaction->accountList($accounts);

		if (!empty($this->request->data) && isset($this->request->data['Bitcoin']['own_account_id'])) {
			$this->request->data['BitcoinTransaction']['from_account'] = $this->request->data['Bitcoin']['own_account_id'];
			$this->BitcoinTransaction->set($this->request->data);
			if ($this->BitcoinTransaction->validates()) {
				$this->Session->write('Bitcoin.account', $this->request->data['Bitcoin']['own_account_id']);
				$this->Common->flashMessage('Changed', 'success');
				$this->redirect(array('action' => 'transfer'));
			} else {
				$this->BitcoinTransaction->validationErrors = array();
				$this->Common->flashMessage('formContainsErrors', 'error');
			}

		} elseif (!empty($this->request->data) && isset($this->request->data['BitcoinTransaction']['request'])) {
			# request
			$this->BitcoinTransaction->set($this->request->data);
			if ($this->BitcoinTransaction->validates()) {
				$this->Common->flashMessage('Displayed', 'success');
			} else {
				$this->Common->flashMessage('formContainsErrors', 'error');
			}

		} elseif (!empty($this->request->data) && isset($this->request->data['BitcoinTransaction']['move'])) {
			# move
			if ($this->BitcoinTransaction->move($this->request->data)) {
				$this->Common->flashMessage('Transfer complete', 'success');
				$this->redirect(array('action' => 'transfer'));
			} else {
				$this->Common->flashMessage('formContainsErrors', 'error');
			}

		} elseif (!empty($this->request->data) && isset($this->request->data['BitcoinTransaction']['send'])) {
			# send
			try {
				if ($this->BitcoinTransaction->send($this->request->data)) {
					$this->Common->flashMessage('Transfer complete', 'success');
					$this->redirect(array('action' => 'transfer'));
				} else {
					$this->Common->flashMessage('formContainsErrors', 'error');
				}
			} catch (BitcoinClientException $e) {
			$this->Common->flashMessage($e->getMessage(), 'error');
		}
		}

		if (empty($this->request->data)) {
			$this->request->data['Bitcoin']['own_account_id'] = $this->BitcoinTransaction->ownAccount();
			if ($address = $this->BitcoinTransaction->ownAddress($addresses)) {
				$this->request->data['BitcoinTransaction']['address'] = $address;
			} elseif ($address = $this->BitcoinTransaction->Bitcoin->getNewAddress()) {
				$this->Common->flashMessage('New Bitcoin Address generated', 'info');
				$this->request->data['BitcoinTransaction']['address'] = $address;
			}
		}

		$infos = $this->BitcoinTransaction->Bitcoin->getInfo();
		$this->Common->loadHelper(array('Tools.QrCode'));
		$this->set(compact('ownAccounts', 'ownAddresses', 'infos'));
	}

	/**
	 * transaction details
	 */
	public function admin_tx($txid = null) {
		if (empty($txid) || !$this->BitcoinTransaction->Bitcoin->validateTransaction($txid)) {
			$this->Common->flashMessage('Invalid Transaction', 'error');
			$this->redirect(array('action' => 'transfer'));
		}
		$transaction = $this->BitcoinTransaction->Bitcoin->getTransaction($txid);
		//e5b0f6297fa6743e0c2126fe5bda7b894a95bae7aae37d2695756b68468e4732
		$this->set(compact('txid', 'transaction'));
	}

	/**
	 * address details
	 */
	public function admin_address($address = null) {
		if (empty($address) || !$this->BitcoinTransaction->Bitcoin->validateAddress($address)) {
			$this->Common->flashMessage('Invalid Address', 'error');
			$this->redirect(array('action' => 'transfer'));
		}
	}

	public function admin_transactions($account = null) {
		if (!empty($this->request->params['named']['account'])) {
			$account = $this->request->params['named']['account'];
		}

		$transactions =	$this->BitcoinTransaction->Bitcoin->listTransactions($account);
		$accounts = $this->BitcoinTransaction->accountList();
		$this->set(compact('accounts', 'transactions'));
	}

	public function admin_fee() {
		if ($this->Common->isPosted()) {
			$this->BitcoinTransaction->set($this->request->data);
			if ($this->BitcoinTransaction->validates() && ($amount = $this->BitcoinTransaction->data['BitcoinAddress']['amount']) >= 0 && $this->BitcoinTransaction->Bitcoin->setFee($amount)) {
				$this->Common->flashMessage('Changed', 'success');
			} else {
				$this->Common->flashMessage('formContainsErrors', 'error');
			}
		}

		$infos = $this->BitcoinTransaction->Bitcoin->getInfo();
		$this->set(compact('infos'));
	}

	/**
	 * manually trigger the cronjobbed tasks
	 */
	public function admin_run() {
		if ($this->BitcoinTransaction->update()) {
			$this->log('Tasks manually triggered and successfully completed', 'bitcoin');
			$this->Common->flashMessage('Tasks manually triggered and successfully completed', 'success');
		} else {
			$this->log('Tasks manually triggered but aborted', 'bitcoin');
		}
		$this->Common->autoRedirect(array('action' => 'index'));
	}

/****************************************************************************************
 * protected/internal functions
 ****************************************************************************************/

/****************************************************************************************
 * deprecated/test functions
 ****************************************************************************************/

}
