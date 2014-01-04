<?php
App::uses('AppModel', 'Model');
class Transaction extends AppModel {

	public $actsAs = array();

	public $order = array('Transaction.created' => 'DESC');

	public $skipFields = array('foreign_id', 'model', 'transaction_id', 'note', 'pending_reason', 'reason_code');

	public $validate = array(
		'foreign_id' => array(
			'uuid' => array(
				'rule' => array('uuid'),
				'message' => 'valErrMandatoryField',
			),
		),
		'model' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'valErrMandatoryField',
			),
		),
		'type' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'valErrMandatoryField',
			),
		),
		'transaction_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'valErrMandatoryField',
			),
		),
		'transaction_type' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'valErrMandatoryField',
			),
		),
		'note' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'valErrMandatoryField',
			),
		),
		'currency_code' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'valErrMandatoryField',
			),
		),
		'payment_type' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'valErrMandatoryField',
			),
		),
		'payment_status' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'valErrMandatoryField',
			),
		),
		'pending_reason' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'valErrMandatoryField',
			),
		),
		'reason_code' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'valErrMandatoryField',
			),
		),
	);

	public function initCustom($type, $data = array()) {
		$this->create();
		$data['type'] = $type;
		if (empty($data['type'])) {
			$data['type'] = 'prepaid';
		}
		if (!isset($data['title'])) {
			$title = $data['type'];
		}
		$this->set($data);
		return $this->save(null, false);
	}

	/**
	 * @param array $customData
	 * - model, foreign_id, currency_code, amount, token (required)
	 * - title (optional)
	 * @return mixed success
	 */
	public function initPaypal($data) {
		$this->create();
		$data['type'] = 'paypal';
		if (!isset($data['title'])) {
			$title = $data['type'] . ' payment';
		}
		$this->set($data);
		return $this->save(null, false);
	}

	/**
	 * @param array $officialPaypalReturnArray
	 * @param array $customData (model, foreign_id, title)
	 * @param boolean $treatPendingAsCompleted if one sells digital goods (PDF file) and possesses no risk if the payment is delayed
	 * @return mixed result
	 */
	public function updatePaypal($id, $array = array(), $treatPendingAsCompleted = false) {
		$data = array();
		$match = array(
			'REASONCODE' => 'reason_code',
			'PENDINGREASON' => 'pending_reason',
			'PAYMENTSTATUS' => 'payment_status',
			'CURRENCYCODE' => 'currency_code',
			'TAXAMT' => 'tax_amount',
			'FEEAMT' => 'fee_amount',
			'AMT' => 'amount',
			'PAYMENTTYPE' => 'payment_type',
			'ORDERTIME' => 'order_time',
			'TRANSACTIONTYPE' => 'transaction_type',
			'TRANSACTIONID' => 'transaction_id',
			'NOTE' => 'note',
		);
		foreach ($match as $from => $to) {
			if (!isset($array[$from])) {
				continue;
			}
			$data[$to] = $array[$from];
		}

		if ($data['payment_status'] === 'Completed' || $treatPendingAsCompleted && $data['payment_status'] === 'Pending') {
			$data['status'] = self::STATUS_COMPLETED;
		} elseif ($data['payment_status'] === 'Pending') {
			$data['status'] = self::STATUS_PENDING;
		}

		$this->id = $id;
		return $this->save($data, false);
	}

	public function initSofortbanking($data) {
		$this->create();
		$data['type'] = 'sofortbanking';
		$data['payment_type'] = 'instant';
		if (!isset($data['title'])) {
			$title = $data['type'] . ' payment';
		}
		$this->set($data);
		return $this->save(null, false);
	}

	public function updateSofortbanking($id, $array = array()) {
		$data = array();
		$data = array_merge($data, $array);
		$this->id = $id;
		$this->log(returns($data), 'sofortbanking');
		return $this->save($data, false);
	}

	public function initBankTransfer($data) {
		$this->create();
		$data['type'] = 'bank_transfer';
		if (!isset($data['title'])) {
			$title = $data['type'] . ' payment';
		}
		$this->set($data);
		return $this->save(null, false);
	}

	public function initSkrill($data) {
		$this->create();
		$data['type'] = 'skrill';
		if (!isset($data['title'])) {
			$title = $data['type'] . ' (moneybookers) payment';
		}
		$this->set($data);
		return $this->save(null, false);
	}

	public function getOwn($modelName, $foreignId, $limit = null, $type = 'all', $transactionType = null) {
		$options = array(
			'conditions' => array($this->alias . '.model' => $modelName),
			//'order' => array()
		);
		if ($foreignId !== null) {
			$options['conditions'][$this->alias . '.foreign_id'] = $foreignId;
		}
		if ($transactionType !== null) {
			$options['conditions'][$this->alias . '.type'] = $transactionType;
		}
		if ($limit !== null) {
			$options['limit'] = $limit;
		}
		return $this->find($type, $options);
	}

	public static function is($status, $transaction) {
		if (empty($transaction['payment_status'])) {
			if ($status == self::STATUS_TEXT_NEW) {
				return true;
			}
			return false;
		}
		if (in_array($status, array(self::STATUS_TEXT_NEW, self::STATUS_TEXT_PENDING, self::STATUS_TEXT_ABORTED, self::STATUS_TEXT_COMPLETED)) && $status == $transaction['payment_status']) {
			return true;
		}
		return false;
	}

	public static function statuses($value = null) {
		$array = array(
			self::STATUS_NEW => __('New'),
			self::STATUS_PENDING => __('Pending'),
			self::STATUS_ABORTED => __('Aborted'),
			self::STATUS_COMPLETED => __('Completed'),
		);
		if (empty($value)) {
			$value = self::STATUS_NEW;
		}
		return parent::enum($value, $array);
	}

	const STATUS_NEW = 0; //'New';
	const STATUS_PENDING = 1; //'Pending';
	const STATUS_ABORTED = 2; //'Aborted';
	const STATUS_COMPLETED = 3; //'Completed';

	const STATUS_TEXT_NEW = 'New';
	const STATUS_TEXT_PENDING = 'Pending';
	const STATUS_TEXT_ABORTED = 'Aborted';
	const STATUS_TEXT_COMPLETED = 'Completed';

}
