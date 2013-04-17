<?php
App::uses('PaymentAppController', 'Payment.Controller');

class PrepaidAccountsController extends PaymentAppController {

	public $helpers = array('Tools.Numeric');
	public $paginate = array();

	public $presetVars = true;

	public function beforeFilter() {
		parent::beforeFilter();

	}



/****************************************************************************************
 * USER functions
 ****************************************************************************************/

	public function view() {
		$uid = $this->Session->read('Auth.User.id');
		$this->PrepaidAccount->Behaviors->load('Payment.Loadable');
		$account = $this->PrepaidAccount->account($uid);
		$amount = $account['PrepaidAccount']['amount'];

		$amounts = $this->PrepaidAccount->loadableAmountsText($amount);
		$this->PaymentMethod = ClassRegistry::init('Payment.PaymentMethod');
		$paymentMethods = $this->PaymentMethod->find('all', array('conditions'=>array('active'=>1, 'alias !='=>'prepaid')));// array('1'=>'Paypal');

		$this->Transaction = ClassRegistry::init('Payment.Transaction');

		if ($this->Common->isPosted()) {
			$this->PrepaidAccount->Behaviors->load('Tools.Confirmable');
			if (empty($data['PrepaidAccount']['charge_amount'])) {
				$data['PrepaidAccount']['charge_amount'] = 0;
			}
			$this->PrepaidAccount->set($this->request->data);
			if ($this->PrepaidAccount->validates()) {
				$this->_initPayment($account, $this->PrepaidAccount->data);

			} else {
				$this->Common->flashMessage(__('formContainsErrors'), 'error');
			}

		} else {
			$this->request->data['PrepaidAccount']['charge_amount'] = $this->PrepaidAccount->defaultAmount();
			$this->request->data['PrepaidAccount']['payment_type_id'] = 1;
		}

		$cond = array(
			'Transaction.model'=>'PrepaidAccount',
			'Transaction.foreign_id'=>$account['PrepaidAccount']['id'],
			'Transaction.status' => array(Transaction::STATUS_NEW, Transaction::STATUS_PENDING),
			'Transaction.type !=' => 'prepaid',
		);
		$pendingTransaction = $this->Transaction->find('first', array('conditions'=>$cond));

		$transactions = $this->Transaction->getOwn('PrepaidAccount', $account['PrepaidAccount']['id'], 50, 'all', 'prepaid');
		$transactionTotal = $this->Transaction->getOwn('PrepaidAccount', $account['PrepaidAccount']['id'], null, 'count', 'prepaid');
		$this->set(compact('amount', 'amounts', 'paymentMethods', 'transactions', 'transactionTotal', 'pendingTransaction'));
		$this->Common->loadHelper(array('Payment.PrepaidAccount'));
	}

	public function _initPayment($account, $data) {
		switch ($data['PrepaidAccount']['payment_type']) {
			case 'paypal':
				# Paypal
				$this->Common->loadComponent(array('Payment.Paypal'));
				$array = array(
					'amount' => $data['PrepaidAccount']['charge_amount'],
					'returnurl' => array('action'=>'deposit'),
					'cancelurl' => array('action'=>'view'),
					'successurl' => array('action'=>'deposit', 'ok'=>1),
					'desc'=> $this->PrepaidAccount->getPaymentDescription($data['PrepaidAccount']['charge_amount'])
				);
				$res = $this->Paypal->setExpressCheckout($array);

				if (empty($res['ACK']) || $res['ACK'] !== 'Success') {
					$message = null;
					if (!empty($res['Error']['Message'])) {
						$message = $res['Error']['Message'];
					}
					$this->log(returns($res), 'paypal');
					throw new InternalErrorException($message);
				}
				$data = array(
					'amount' => $data['PrepaidAccount']['charge_amount'],
					'model' => 'PrepaidAccount',
					'foreign_id' => $account['PrepaidAccount']['id'],
					'title' => $this->PrepaidAccount->getPaymentDescription($data['PrepaidAccount']['charge_amount'], true),
					'token' => $res['TOKEN'],
					'currency_code' => $this->Paypal->get('currency_code'),
				);
				$array = array_merge($array, $data);
				$this->Transaction->initPaypal($array);
				$this->Paypal->redirect($res['TOKEN']);

			case 'bank_transfer':
				# Bank Transfer
				$array = array(
					'amount' => $data['PrepaidAccount']['charge_amount'],
					'model' => 'PrepaidAccount',
					'foreign_id' => $account['PrepaidAccount']['id'],
					'title' => $this->PrepaidAccount->getPaymentDescription($data['PrepaidAccount']['charge_amount'], true),
					//'token' => $res['TOKEN'],
					'currency_code' => 'EUR',
				);
				$this->Transaction->initBankTransfer($array);
				$this->redirect(array('controller'=>'transactions', 'action'=>'view', $this->Transaction->id));

			case 'skrill':
				# MoneyBookers
				$this->Common->loadComponent('Payment.Skrill');
				$array = array(
					'amount' => $data['PrepaidAccount']['charge_amount'],
					'detail1_description' => __('Account Deposition'),
					'detail1_text' => __('Prepaid Account'),
				);
				$res =  $this->Skrill->setExpressCheckout($array);
				if (!$res) {
					$this->Common->flashMessage(__('Invalid Argument'));
					$this->redirect(array('action'=>'view'));
				}
				$array = array(
					'amount' => $data['PrepaidAccount']['charge_amount'],
					'model' => 'PrepaidAccount',
					'foreign_id' => $account['PrepaidAccount']['id'],
					'title' => __('Account Deposition from %s', $this->Session->read('Auth.User.email')),
					//'token' => $res['TOKEN'],
					'currency_code' => 'EUR',
				);
				$this->Transaction->initSkrill($array);
				$this->Skrill->redirect($res);

			case 'bitcoin':
				//TODO
				break;
			case 'sofortbanking':
				//TODO
				$array = array(
					'amount' => $data['PrepaidAccount']['charge_amount'],
					'description' => $this->PrepaidAccount->getPaymentDescription($data['PrepaidAccount']['charge_amount'], true),
					'reason' => __('Prepaid Account'),
					'model' => 'PrepaidAccount',
					'foreign_id' => $account['PrepaidAccount']['id'],
				);
				$this->Common->loadComponent('Payment.PaymentNetwork');
				$res = $this->PaymentNetwork->setClassicExpressCheckout($array);
				if (!$res) {
					$this->Common->flashMessage(__('Invalid Argument'));
					$this->redirect(array('action'=>'view'));
				}
				$data = array(
					'title' => __('Account Deposition from %s', $this->Session->read('Auth.User.email')),
					//'token' => $res['token'],
					'currency_code' => $this->PaymentNetwork->get('currency_code'),
				);
				$array = array_merge($array, $data);
				$this->Transaction->initSofortbanking($array);
				$this->PaymentNetwork->classicRedirect($res);
				break;
			default:
		}
		$this->Common->flashMessage(__('Invalid Argument'));
	}


	/**
	 * load money into prepaid account
	 * 2011-09-23 ms
	 */
	public function deposit($id = null) {
		$this->PrepaidAccount->Behaviors->load('Payment.Loadable');
		$this->Common->loadComponent(array('Payment.Paypal'));
		if (empty($this->request->query('token'))) {
			throw new MethodNotAllowedException(__('Invalid Access'));
		}
		$token = $this->request->query('token');
		$payerId = $this->request->query('PayerID');

		$this->Transaction = ClassRegistry::init('Payment.Transaction');
		$transaction = $this->Transaction->find('first', array('conditions'=>array('Transaction.model'=>'PrepaidAccount', 'Transaction.token'=>$token)));
		if (!$transaction) { //
			throw new MethodNotAllowedException(__('Invalid Access'));
			$this->Common->autoRedirect(array('action'=>'view'));
		}

		$res = $this->Paypal->getExpressCheckoutDetails($token);

		$array = array(
			'token' => $token,
			'payerid' => $payerId,
			'amount' => $transaction['Transaction']['amount'],
			//'notifyurl' => array('action'=>'pay_confirm', $id),
			//'custom' => 'prepaid_16',
		);
		$res = $this->Paypal->doExpressCheckoutPayment($array);

		if (empty($res['ACK']) || $res['ACK'] !== 'Success') {
			$message = null;
			if (!empty($res['Error']['Message'])) {
				$message = $res['Error']['Message'];
			}
			$this->log(returns($res), 'paypal');
			throw new InternalErrorException($message);
		}

		$this->Transaction->updatePaypal($transaction['Transaction']['id'], $res);
		if ($res['PAYMENTSTATUS'] === 'Completed') {
			$amount = $transaction['Transaction']['amount'];
			if ($finalAmount = $this->PrepaidAccount->finalAmount($amount)) {
				$amount = $finalAmount;
			}
			$title = $this->PrepaidAccount->getPaymentDescription($transaction['Transaction']['amount'], true);
			/*
			if ($transaction['Transaction']['amount'] != $amount) {
				$title .= ' ('.number_format((float)$amount-(float)$transaction['Transaction']['amount'], 2, ',', '.').' € + Bonus)';
			}
			*/
			$this->PrepaidAccount->deposit($this->Session->read('Auth.User.id'), $amount, array('title'=>$title));
			$this->Common->flashMessage('Einzahlung erfolgreich abgeschlossen', 'success');
		} else {
			$this->Common->flashMessage('Einzahlung ist erfolgt, konnte aber noch nicht gutgeschrieben werden.', 'warning');
		}

		$this->redirect(array('action'=>'view'));
	}


/****************************************************************************************
 * ADMIN functions
 ****************************************************************************************/

	/**
	 * validate that all transactions sum up to the current balance!
	 *
	 * 2012-04-07 ms
	 */
	public function admin_validate() {
		$this->PrepaidAccount = ClassRegistry::init('Payment.PrepaidAccount');
		//$prepaidAccount = $this->PrepaidAccount->availableMoney($this->Session->read('Auth.User.id'));
		$prepaidAccounts = $this->PrepaidAccount->find('all', array('contain'=>array('User'), 'conditions'=>array('amount >'=>0)));

		$transactions = array();
		$stats = array('error'=>0, 'ok'=>0);

		$this->Transaction = ClassRegistry::init('Payment.Transaction');
		foreach ($prepaidAccounts as $key => $prepaidAccount) {
			$id = $prepaidAccount['PrepaidAccount']['id'];
			$options = array(
				'conditions'=>array(
					'Transaction.model' => 'PrepaidAccount',
					'Transaction.foreign_id' => $id,
					'Transaction.status' => Transaction::STATUS_COMPLETED,
					'Transaction.type' => 'prepaid',
			));
			$transactions[$id] = $this->Transaction->find('all', $options);

			$this->PrepaidAccount->validateTransactions($prepaidAccount['PrepaidAccount'], $transactions[$id]);
			$prepaidAccounts[$key]['PrepaidAccount'] = $prepaidAccount['PrepaidAccount'];

			if (!$prepaidAccount['PrepaidAccount']['validates']) {
				$stats['error']++;
			}	else {
				$stats['ok']++;
			}
		}
		$this->set(compact('prepaidAccounts', 'transactions', 'stats'));
	}

	public function admin_repair($id = null) {
		if (empty($id) || !($prepaidAccount = $this->PrepaidAccount->find('first', array('contain'=>array(), 'conditions'=>array('PrepaidAccount.id'=>$id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		$id = $prepaidAccount['PrepaidAccount']['id'];
		$this->Transaction = ClassRegistry::init('Payment.Transaction');
		$options = array(
			'conditions'=>array(
				'Transaction.model'=>'PrepaidAccount',
				'Transaction.foreign_id'=>$id,
				'Transaction.status' => Transaction::STATUS_COMPLETED,
				'Transaction.type' => 'prepaid',
			)
		);
		$transactions = $this->Transaction->find('all', $options);

		$this->PrepaidAccount->validateTransactions($prepaidAccount['PrepaidAccount'], $transactions);
		$difference = $prepaidAccount['PrepaidAccount']['amount'] - $prepaidAccount['PrepaidAccount']['transaction_amount'];

		if (!$difference) {
			$this->Common->flashMessage(__('Nothing needs to be corrected here'), 'error');
			$this->Common->autoRedirect(array('action'=>'validate'));
		}

		$data = array(
			'title' => __('Manual correction of Prepaid Account'),
			'model' => 'PrepaidAccount',
			'foreign_id' => $id,
			'status' => Transaction::STATUS_COMPLETED,
			'amount' => $difference,
		);
		$this->Transaction->initCustom('prepaid', $data);
		$this->Common->flashMessage(__('Corrected'), 'success');
		$this->Common->autoRedirect(array('action'=>'validate'));
	}

	public function admin_index() {
		$this->PrepaidAccount->recursive = 0;
		$this->PrepaidAccount->Behaviors->load('Search.Searchable');
		$this->Common->loadComponent(array('Search.Prg'));

		$this->Prg->commonProcess();
		$this->paginate['conditions'] = $this->PrepaidAccount->parseCriteria($this->passedArgs);

		$prepaidAccounts = $this->paginate();

		$userDisplayField = $this->PrepaidAccount->User->displayField;

		$users = $this->PrepaidAccount->User->find('list');
		if (!empty($this->request->data['PrepaidAccount']['user_id'])) {
			if (!array_key_exists($this->request->data['PrepaidAccount']['user_id'], $users)) {
				$users['-'] = ' - '.__('invalidSearchValue').' - ';
				$this->request->data['PrepaidAccount']['user_id'] = '-';
			}
		}
		$this->set(compact('prepaidAccounts', 'userDisplayField', 'users'));
	}

	/**
	 * void the prepaid account or make a payout to this user (cash, transfer, ...)
	 *
	 * 2012-04-07 ms
	 */
	public function admin_payout($id = null) {
		if (empty($id) || !($prepaidAccount = $this->PrepaidAccount->find('first', array('contain'=>array('User'), 'conditions'=>array('PrepaidAccount.id'=>$id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		if ($prepaidAccount['PrepaidAccount']['amount'] <= 0) {
			$this->Common->flashMessage(__('no money left in account'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		if ($this->Common->isPosted()) {
			$this->PrepaidAccount->validate['amount']['range'] = array(
				'rule' => array('validatePayoutRange', $prepaidAccount['PrepaidAccount']['amount']),
				'message' => 'valErrInsufficientFunds',
			);
			$this->PrepaidAccount->set($this->request->data);
			if ($this->PrepaidAccount->validates()) {
				$amount = $this->request->data['PrepaidAccount']['amount'];
				$this->Transaction = ClassRegistry::init('Payment.Transaction');
				$this->PrepaidAccount->pay($prepaidAccount['PrepaidAccount']['user_id'], $amount, false);
				$data = array(
					'title' => $this->request->data['PrepaidAccount']['reason'],
					'transaction_type' => 'payout',
					'note' => $this->request->data['PrepaidAccount']['note'],
					'model' => $this->PrepaidAccount->alias,
					'foreign_id' => $id,
					'status' => Transaction::STATUS_COMPLETED,
					'amount' => -$amount,
				);
				$this->Transaction->initCustom('prepaid', $data);
				$this->Common->flashMessage(__('%s paid out', $amount), 'success');
				$this->Common->postRedirect(array('action' => 'index'));
			}
		} else {
			$this->request->data['PrepaidAccount']['reason'] = __('Payout');
			$this->request->data['PrepaidAccount']['note'] = __('Cash');
		}
		$this->set(compact('prepaidAccount', 'logEntries'));
	}

	public function admin_view($id = null) {
		if (empty($id) || !($prepaidAccount = $this->PrepaidAccount->find('first', array('contain'=>array('User'), 'conditions'=>array('PrepaidAccount.id'=>$id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		$logEntries = $this->PrepaidAccount->findLog(array('foreign_id'=>$id));

		$this->Transaction = ClassRegistry::init('Payment.Transaction');
		$options = array('conditions'=>array('Transaction.model'=>'PrepaidAccount', 'Transaction.foreign_id'=>$id));
		$transactions = $this->Transaction->find('all', $options);

		$this->set(compact('prepaidAccount', 'logEntries', 'transactions'));
	}

	public function admin_add($userId = null) {
		if ($this->Common->isPosted()) {
			if (Configure::read('MasterPassword.password')) {
				$this->PrepaidAccount->Behaviors->load('Tools.MasterPassword');
			}
			$this->PrepaidAccount->create();
			if ($this->PrepaidAccount->save($this->request->data)) {
				$var = $this->request->data['PrepaidAccount']['amount'];
				if (Configure::read('PrepaidAccount.callback')) {
					$this->PrepaidAccount->User->afterPrepaidChange($this->PrepaidAccount->id, null, $var);
				}
				if ($var != 0) {
					$this->Transaction = ClassRegistry::init('Payment.Transaction');
					$data = array(
						'title' => $this->request->data['PrepaidAccount']['reason'],
						'model' => $this->PrepaidAccount->alias,
						'foreign_id' => $this->PrepaidAccount->id,
						'status' => Transaction::STATUS_COMPLETED,
						'amount' => $var,
					);
					$this->Transaction->initCustom('prepaid', $data);
				}
				$this->Common->flashMessage(__('record add %s saved', h($var)), 'success');
				$this->Common->postRedirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors'), 'error');
			}
		} else {
			if (!empty($userId)) {
				$this->request->data['PrepaidAccount']['user_id'] = $userId;
			}
		}
		$users = $this->PrepaidAccount->User->find('list');
		$this->set(compact('users'));
	}

	public function admin_edit($id = null) {
		if (empty($id) || !($prepaidAccount = $this->PrepaidAccount->find('first', array('conditions'=>array('PrepaidAccount.id'=>$id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		if ($this->Common->isPosted()) {
			if (Configure::read('MasterPassword.password')) {
				$this->PrepaidAccount->Behaviors->load('Tools.MasterPassword');
			}
			if ($this->PrepaidAccount->save($this->request->data)) {
				$var = $this->request->data['PrepaidAccount']['amount'];
				if (Configure::read('PrepaidAccount.callback')) {
					$this->PrepaidAccount->User->afterPrepaidChange($this->PrepaidAccount->id, $prepaidAccount['PrepaidAccount']['amount'], $var);
				}
				if (($difference = $var - $prepaidAccount['PrepaidAccount']['amount']) != 0) {
					$this->Transaction = ClassRegistry::init('Payment.Transaction');
					$data = array(
						'title' => $this->request->data['PrepaidAccount']['reason'],
						'model' => $this->PrepaidAccount->alias,
						'foreign_id' => $id,
						'status' => Transaction::STATUS_COMPLETED,
						'amount' => $difference,
					);
					$this->Transaction->initCustom('prepaid', $data);
				}
				$this->Common->flashMessage(__('record edit %s saved', h($var)), 'success');
				$this->Common->postRedirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors'), 'error');
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $prepaidAccount;
		}
		$users = $this->PrepaidAccount->User->find('list');
		$this->set(compact('users'));
	}

	public function admin_delete($id = null) {
		$useMasterPassword = (bool)Configure::read('MasterPassword.password');

		if (!$this->Common->isPosted() && !$useMasterPassword) {
			throw new MethodNotAllowedException();
		}
		if ($useMasterPassword) {
			$this->PrepaidAccount->Behaviors->load('Tools.MasterPassword');
		} else {
			$continue = true;
		}

		$continue = false;
		if (!$continue && $this->Common->isPosted()) {
			$this->PrepaidAccount->set($this->request->data);
			$continue = $this->PrepaidAccount->validates();
		}
		if ($continue) {
			if (empty($id) || !($prepaidAccount = $this->PrepaidAccount->find('first', array('conditions'=>array('PrepaidAccount.id'=>$id), 'fields'=>array('id', 'amount'))))) {
				$this->Common->flashMessage(__('invalid record'), 'error');
				$this->Common->autoRedirect(array('action'=>'index'));
			}
			$var = $prepaidAccount['PrepaidAccount']['amount'];

			if ($this->PrepaidAccount->delete($id)) {
				$this->Common->flashMessage(__('record del %s done', h($var)), 'success');
				$this->redirect(array('action' => 'index'));
			}
			$this->Common->flashMessage(__('record del %s not done exception', h($var)), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
	}



/****************************************************************************************
 * protected/interal functions
 ****************************************************************************************/


/****************************************************************************************
 * deprecated/test functions
 ****************************************************************************************/

}
