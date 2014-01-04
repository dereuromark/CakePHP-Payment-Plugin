<?php

App::uses('PaymentAppModel', 'Payment.Model');

if (!defined('CLASS_USER')) {
	define('CLASS_USER', 'User');
}

/**
 * PrepaidAccount for users
 * callbacks for user model:
 * - afterPrepaidChange($prepaidAccountId, $before, $after)
 * features
 * - complete transaction log
 *
 */
class PrepaidAccount extends PaymentAppModel {

	public $displayField = 'amount';

	public $actsAs = array('Tools.Logable' => array('change' => 'full'));

	public $order = array();

	public $filterArgs = array(
		'user_id' => array('type' => 'value'),
	);

	public $validate = array(
		'user_id' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'valErrMandatoryField',
				'last' => true
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'valErrRecordNameExists',
				'last' => true,
			),
		),
		'amount' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'valErrMandatoryField',
				'last' => true,
			),
		),
		# for loading
		'payment_type' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'valErrMandatoryField',
				'last' => true,
			),
		),
		# for add/edit
		'reason' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'valErrMandatoryField',
				'last' => true,
			),
		),
	);

	public $belongsTo = array(
		'User' => array(
			'className' => CLASS_USER,
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => array('id', 'username', 'email'),
			'order' => ''
		)
	);

	public function validatePayoutRange($data, $money) {
		$amount = array_shift($data);
		return $amount > 0 && $money >= $amount;
	}

	public function availableMoney($uid) {
		$res = $this->find('first', array('conditions' => array('user_id' => $uid)));
		if (!$res) {
			return 0;
		}
		return $res[$this->alias]['amount'];
	}

	/**
	 * //TODO: atomic query for more security?
	 * use the money in the prepaid account
	 * @return the amount the user has paid with (0 if not possible)
	 */
	public function pay($uid, $amount = null, $transaction = true) {
		if ($amount === null) {
			return false;
		}

		$account = $this->account($uid);
		if ($amount > 0 && (!$account || $account[$this->alias]['amount'] < $amount)) {
			return false;
		}
		$money = $account[$this->alias]['amount'];

		$this->id = $account[$this->alias]['id'];
		$this->enableLog(false);
		$this->saveField('amount', $money - $amount);
		$this->enableLog(true);

		$title = __('prepaidAccountPayTitle');
		$this->customLog(__('paid') . ' (' . number_format($amount, 2) . ')', $this->id, array('title' => $title));

		if ($transaction) {
			$this->Transaction = ClassRegistry::init('Payment.Transaction');
			$data = array(
				'title' => $title,
				'model' => $this->alias,
				'foreign_id' => $this->id,
				'status' => Transaction::STATUS_COMPLETED,
				'amount' => -$amount,
				//'order_time' => date(FORMAT_DB_DATETIME),
			);
			if (is_array($transaction)) {
				$data = array_merge($data, $transaction);
			}
			$this->Transaction->initCustom('prepaid', $data);
		}
		return (float)$amount;
	}

	/**
	 * Put money into the prepaid account
	 * @param $uid Id of user owning the account
	 * @param $amount Amount of money at the beginning
	 * @return boolean If $amount was successfully deposited
	 * @author gh 2011-09-13
	 */
	public function deposit($uid, $amount, $transaction = true) {
		if ($this->Behaviors->loaded('Loadable') && $finalAmount = $this->finalAmount($amount)) {
			$amount = $finalAmount;
		}
		$account = $this->account($uid);
		$account[$this->alias]['amount'] += $amount;
		$this->set($account);
		$this->id = $account[$this->alias]['id'];
		$this->enableLog(false);
		$res = $this->save();
		$this->enableLog(true);
		if (!$res) {
			die(returns($this->validationErrors));
			return false;
		}

		$title = __('prepaidAccountDepositTitle');
		$this->customLog(__('deposited') . ' (' . number_format($amount, 2) . ')', $this->id, array('title' => $title));

		if ($transaction) {
			$this->Transaction = ClassRegistry::init('Payment.Transaction');
			$data = array(
				'title' => $title,
				'model' => $this->alias,
				'foreign_id' => $this->id,
				'status' => Transaction::STATUS_COMPLETED,
				'amount' => $amount,
				//'order_time' => date(FORMAT_DB_DATETIME),
			);
			if (is_array($transaction)) {
				$data = array_merge($data, $transaction);
			}
			$this->Transaction->initCustom('prepaid', $data);
		}
		return true;
	}

	/**
	 * get the current account
	 * if it does not exist it creates one!
	 * @return array account
	 */
	public function account($uid) {
		if ($account = $this->find('first', array('conditions' => array('user_id' => $uid)))) {
			return $account;
		}
		# create
		$data = array(
			'user_id' => $uid,
		);
		$this->create();
		$this->enableLog(false);
		$res = $this->save($data, false);
		$this->enableLog(true);
		if ($res) {
			$res[$this->alias]['id'] = $this->id;
		}
		return $res;
	}

	/**
	 * PrepaidAccount::getPaymentDescription()
	 * For PayPal or other external payment providers.
	 * @param integer $paymentAmount
	 * @return void
	 * 2012-01-12
	 */
	public function getPaymentDescription($paymentAmount, $short = false) {
		App::uses('NumberLib', 'Tools.Utility');

		$amount = $paymentAmount;
		if ($this->Behaviors->loaded('Loadable') && ($finalAmount = $this->finalAmount($paymentAmount)) && $finalAmount != $paymentAmount) {
			$amount = $finalAmount;
		}
		$res = __('Account Deposition');
		if (!$short) {
			$res .= ': ' . NumberLib::money($amount);
		}
		if ($finalAmount != $paymentAmount) {
			$res .= ' (' . NumberLib::money($paymentAmount) . ' + ' . NumberLib::money((float)$finalAmount - (float)$paymentAmount) . ' Bonus)';
		}
		return $res;
	}

	/**
	 */
	public static function validateTransactions(&$prepaidAccount, &$transactions) {
		if ($prepaidAccount['amount'] == 0 && empty($transactions)) {
			$prepaidAccount['validates'] = true;
			$prepaidAccount['transaction_amount'] = 0;
			return true;
		}
		$total = 0;
		foreach ($transactions as $transaction) {
			$total += $transaction['Transaction']['amount'];
		}
		App::uses('NumberLib', 'Tools.Utility');
		$validates = NumberLib::isFloatEqual($total, $prepaidAccount['amount']);
		$prepaidAccount['validates'] = $validates;
		$prepaidAccount['transaction_amount'] = $total;
		return $validates;
	}

	/**
	 * send an email with all accounts and current money to admin email
	 * for security reasons (if DB crashes etc) and transparency
	 * @return boolean success
	 */
	public function sendOverviewEmail() {
		$accounts = $this->find('all', array('contain' => array('User.email'), 'conditions' => array($this->alias . '.amount >' => 0)));
		App::uses('NumericHelper', 'Tools.View/Helper');
		$Numeric = new NumericHelper(new View(null));

		$message = '';
		$total = 0;
		foreach ($accounts as $key => $account) {
			$message .= '#' . str_pad($key + 1, 3, '0', STR_PAD_LEFT) . ':' . TB . str_pad($Numeric->money($account[$this->alias]['amount']), 10, ' ', STR_PAD_LEFT) . ' - ' . $account['User']['email'] . PHP_EOL;
			$total += $account[$this->alias]['amount'];
		}
		$message = __('Total') . ': ' . $Numeric->money($total) . PHP_EOL . PHP_EOL . __('Details') . ':' . PHP_EOL . $message;

		App::uses('EmailLib', 'Tools.Lib');
		$this->Email = new EmailLib();
		$this->Email->to(Configure::read('Config.admin_email'), Configure::read('Config.admin_emailname'));
		$this->Email->subject(__('Prepaid Accounts') . ' - ' . __('Overview'));
		$this->Email->template('default', 'internal');
		$this->Email->viewVars(compact('message', 'subject', 'emailFrom', 'nameFrom'));
		if ($this->Email->send($message)) {
			return true;
		}
		return false;
	}

}
