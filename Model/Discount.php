<?php
App::uses('PaymentAppModel', 'Payment.Model');

class Discount extends PaymentAppModel {

	public $actsAs = array('Tools.Jsonable' => array('fields' => 'details'));

	public $order = array('Discount.created' => 'DESC');

	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'valErrMandatoryField',
				'last' => true
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'valErrRecordNameExists',
			),
		),
		'validity_days' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'valErrMandatoryField',
			),
		),
		'amount' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'valErrMandatoryField',
				'last' => true,
			),
			'amountOrFactor' => array(
				'rule' => array('validateAmountOrFactor'),
				'message' => 'Es wurde weder ein fester noch ein prozentualer Betrag festgelegt.',
				'last' => true,
			),
		),
		'factor' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'valErrMandatoryField',
			),
		),
		'unlimited' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'message' => 'valErrMandatoryField',
			),
		),
		'min' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'valErrMandatoryField',
			),
		),
		'model' => array(
		),
		'foreign_id' => array(
		),
		'valid_from' => array(
			'time' => array(
				'rule' => array('validateDatetime', array('allowEmpty' => true)),
				'message' => 'valErrMandatoryField',
				'last' => true,
			),
		),
		'valid_until' => array(
			'time' => array(
				'rule' => array('validateDatetime', array('allowEmpty' => true, 'after' => 'valid_from')),
				'message' => 'valErrMandatoryField',
				'last' => true,
			),
		),
	);

	/**
	 * Validate that either amount or factor is given
	 *
	 * @return boolean success
	 */
	public function validateAmountOrFactor($data) {
		$amount = array_shift($data);
		if (!isset($this->data[$this->alias]['factor'])) {
			return true;
		}
		$factor = $this->data[$this->alias]['factor'];
		return $amount > 0 || $factor > 0 && $factor < 100;
	}

	public $hasMany = array(
		'DiscountCode' => array(
			'className' => 'Payment.DiscountCode',
			'foreignKey' => 'discount_id',
			'dependent' => true, # !!!
			'conditions' => '',
			'fields' => '',
			'order' => '',
		)
	);

	/**
	 * @return string code or bool FALSE on failure
	 */
	public function createCode($discount) {
		$data = array(
			'code' => $this->DiscountCode->generateCode(),
			'discount_id' => $discount
		);
		if ($this->DiscountCode->save($data)) {
			return $data['code'];
		}
		return false;
	}

	/**
	 * @param code
	 * @return array code or bool FALSE on error
	 */
	public function check($code, $value = null) {
		$code = $this->DiscountCode->findByCode($code);
		if (empty($code)) {
			$this->error = __('discountCodeInvalid');
			return false;
		}
		if (($res = $this->DiscountCode->isValid($code, $value)) === true) {
			$this->error = '';
			return $code;
		}
		$this->error = $res;
		return false;
	}

	/**
	 * @param code (expects already checked code)
	 * @param foreignId
	 * @param model
	 * @param additionalDataFields Fields that get saved additionally
	 * @return boolean success or NULL if already redeemed
	 */
	public function redeem($code, $foreignId = null, $model = null, $additionalDataFields = array()) {
		$code = $this->DiscountCode->findByCode($code);
		if (empty($code)) {
			return false;
		}
		/*
		if ($code['DiscountCode']['used']) {
			return null;
		}
		*/
		$data = array(
			'id' => $code['DiscountCode']['id'],
			'used' => 1,
		);

		$data = array_merge($data, $additionalDataFields);

		if ($foreignId) {
			$data['foreign_id'] = $foreignId;
		}
		if ($model) {
			$data['model'] = $model;
		}
		if ($this->DiscountCode->save($data)) {
			return true;
		}
		return false;
	}

	/**
	 * Calculate()
	 *
	 * @param float $oldValue
	 * @param array $discount['Discount']
	 * @return float newValue
	 */
	public static function calculate($value, $discount) {
		if ($discount['amount'] > 0) {
			$value = max(0, $value - $discount['amount']);
		}
		if ($discount['factor'] > 0) {
			$factor = $discount['factor'] / 100;
			return $value - $factor * $value;
		}
		return $value;
	}

	/**
	 * CalculateRedeemedAmount()
	 *
	 * @param float $value
	 * @param array $discount['Discount']
	 * @return float redeemedAmount for DiscountCode
	 */
	public static function calculateRedeemedAmount($value, $discount) {
		return $value - self::calculate($value, $discount);
	}

}
