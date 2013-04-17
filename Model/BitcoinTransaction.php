<?php
App::uses('PaymentAppModel', 'Payment.Model');

class BitcoinTransaction extends PaymentAppModel {

	public $displayField = 'amount';

	public $order = array('BitcoinTransaction.created'=>'DESC');

	public $validate = array(
		'address_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'valErrMandatoryField',
			),
		),
		'model' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'valErrMandatoryField',
			),
		),
		'foreign_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'valErrMandatoryField',
			),
		),
		'confirmations' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'valErrMandatoryField',
			),
		),
		'amount' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'valErrMandatoryField',
			),
		),
		'amount_expected' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'valErrMandatoryField',
			),
		),
		'payment_fee' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'valErrMandatoryField',
			),
		),
		'details' => array(
		),
		'status' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'valErrMandatoryField',
			),
		),
		'refund_address' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'valErrMandatoryField',
				'last' => true
			),
			'validateBitcoinAddress' => array(
				'rule' => array('validateBitcoinAddress'),
				'message' => 'Invalid Bitcoin Address',
			),
		),
		# for valiadtion only:
		'pwd' => array(
			'notempty' => array(
				'rule' => array('validatePwd'),
				'message' => 'valErrMandatoryField',
			),
		),
		'to_address' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'valErrMandatoryField',
				'last' => true
			),
			'validateBitcoinAddress' => array(
				'rule' => array('validateBitcoinAddress'),
				'message' => 'Invalid Bitcoin Address',
			),
		),
		'from_address' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'valErrMandatoryField',
				'last' => true
			),
			'validateBitcoinAddress' => array(
				'rule' => array('validateBitcoinAddress'),
				'message' => 'Invalid Bitcoin Address',
			),
			'validateFromAddress' => array(
				'rule' => array('validateFromAddress'),
				'message' => 'Invalid Address',
			),
		),
		'to_account' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'valErrMandatoryField',
				'last' => true
			),
			'validateToAccount' => array(
				'rule' => array('validateToAccount'),
				'message' => 'Invalid Bitcoin Account',
			),
		),
		'from_account' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'valErrMandatoryField',
				'last' => true
			),
			'validateFromAccount' => array(
				'rule' => array('validateFromAccount'),
				'message' => 'Insufficient Funds',
			),
		),
		'confirmations' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'valErrMandatoryField',
			),
		),
		'address' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'valErrMandatoryField',
				'last' => true
			),
			'validateBitcoinAddress' => array(
				'rule' => array('validateBitcoinAddress'),
				'message' => 'Invalid Bitcoin Address',
			),
		),
	);


	public $belongsTo = array(
		/*
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '', //array('id', 'username'),
			'order' => ''
		)*/
		'BitcoinAddress' => array(
			'className' => 'Payment.BitcoinAddress',
			'foreignKey' => 'address_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);


	public function validatePwd($data) {
		$pwd = array_shift($data);
		if (!($key = Configure::read('Bitcoin.key'))) {
			return true;
		}
		return $pwd == $key;
	}

	public function validateToAccount($account) {
		$own = $this->ownAccount();
		$account = array_shift($account);
		return $own != $account;
	}

	public function validateFromAccount($account) {
		$account = array_shift($account);
		if (!isset($this->data[$this->alias]['amount'])) {
			return true;
		}
		if ($this->Bitcoin->getBalance($account) < $this->data[$this->alias]['amount']) {
			return false;
		}
		return true;
	}

	public function validateFromAddress($address) {
		$accountAddresses = $this->Bitcoin->getAddressesByAccount();
		$address = array_shift($address);
		if (!in_array($address, $accountAddresses)) {
			return false;
		}
		return true;
	}

	public function validateBitcoinAddress($address) {
		if (is_array($address)) {
			$address = array_shift($address);
		}
		return $this->Bitcoin->validateAddress($address);
	}



	public function __construct($id = false, $table = false, $ds = null) {
		App::uses('BitcoinLib', 'Payment.Lib');
		$this->Bitcoin = new BitcoinLib();
		parent::__construct($id, $table, $ds);
	}


	public function beforeDelete($cascade = true) {
		parent::beforeDelete();

		$this->record = $this->get($this->id);
	}

	public function afterDelete() {
		parent::afterDelete();
		$id = $this->record[$this->alias]['address_id'];
		# clear address if nothing has been received yet by it
		$this->BitcoinAddress->resetIfUnused($id);
	}

	/**
	 * via cronjob every few minutes
	 * updates all transactions
	 * 2011-07-16 ms
	 */
	public function checkTransactions() {
		$queue = $this->find('all', array(
			'conditions'=>array('confirmations <'=>6, 'user_id !='=>'', 'modified >'=>date(FORMAT_DB_DATE, time()-7*DAY)),
			'order'=>array('modified'=>'ASC'), //'limit' => 20,
		));
		foreach ($queue as $address) {
			//$this->updateAddress($address);
			//$paid = $this->Bitcoin->getReceivedByAddress($address[$this->alias]['address']);
			/*
			if ($paid >= $ordersTotal) {

			}
			*/
		}
	}

	/**
	 * @param array $data
	 * -
	 * @return array $address
	 */
	public function send($data) {
		if (!isset($data[$this->alias]['from_account'])) {
			$data[$this->alias]['from_account'] = $this->ownAccount();
		}
		/*
		if (!isset($data[$this->alias]['from_address'])) {
			$data[$this->alias]['from_address'] = $this->ownAddress();
		}
		*/
		$this->set($data);
		if (!$this->validates()) {
			return false;
		}
		$adr = $this->data[$this->alias];
		//return $this->Bitcoin->sendToAddress($adr['to_address'], $adr['amount'], $adr['comment'], $adr['comment_to']); # buggy ? sends from account with empty string
		return $this->Bitcoin->sendFrom($adr['from_account'], $adr['to_address'], $adr['amount'], 1, $adr['comment'], $adr['comment_to']);
	}

	/**
	 * @param array $data
	 * -
	 * @return bool $success
	 */
	public function move($data) {
		if (!isset($data[$this->alias]['from_account'])) {
			$data[$this->alias]['from_account'] = $this->ownAccount();
		}
		$this->set($data);
		if (!$this->validates()) {
			return false;
		}
		return $this->Bitcoin->move($this->data[$this->alias]['from_account'], $this->data[$this->alias]['to_account'], $this->data[$this->alias]['amount'], 1, $this->data[$this->alias]['comment']);
	}

	/**
	 * @return string $account
	 * 2011-07-20 ms
	 */
	public function ownAccount() {
		if ($account = CakeSession::read('Bitcoin.account')) {
			return $account;
		}
		return Configure::read('Bitcoin.account');
	}

	/**
	 * @return string $address or FALSE on failure
	 * 2011-07-20 ms
	 */
	public function ownAddress($addresses = array()) {
		if ($address = CakeSession::read('Bitcoin.address')) {
			return $address;
		} elseif ($address = Configure::read('Bitcoin.address')) {
			return $address;
		} elseif (!empty($addresses)) {
			return $addresses[0];
		}
		return false;
	}

	/**
	 * for selects
	 * similar to find('list')
	 */
	public function addressList($addresses = null) {
		if ($addresses === null) {
			$addresses = $this->Bitcoin->getAddressesByAccount($this->ownAccount());
		}
		$ownAddresses = empty($addresses) ? array() : array_combine(array_values($addresses), array_values($addresses));
		return $ownAddresses;
	}

	/**
	 * for selects
	 * similar to find('list')
	 */
	public function accountList($accounts = null) {
		if ($accounts === null) {
			$accounts = $this->Bitcoin->listAccounts();
		}
		$ownAccounts = array();
		foreach ($accounts as $ownAccount => $amount) {
			$ownAccounts[$ownAccount] = $ownAccount . ' ('.$amount.' BTC)';
		}
		return $ownAccounts;
	}

	const STATUS_PENDING = 0;
	const STATUS_CLOSED = 1;

}
