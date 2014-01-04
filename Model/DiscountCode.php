<?php
App::uses('PaymentAppModel', 'Payment.Model');

class DiscountCode extends PaymentAppModel {

	public $displayField = 'code';

	public $order = array('DiscountCode.created' => 'DESC');

	public $validate = array(
		'discount_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'valErrMandatoryField',
			),
		),
		'code' => array(
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
		'details' => array(
		),
		'used' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'message' => 'valErrMandatoryField',
			),
		),
		'quantity' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'valErrMandatoryField',
			),
		),
	);

	public $belongsTo = array(
		'Discount' => array(
			'className' => 'Payment.Discount',
			'foreignKey' => 'discount_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public function findByCode($code = null) {
		return $this->find('first', array('contain' => array('Discount'), 'conditions' => array($this->alias . '.code' => $code)));
	}

	/**
	 * @param array $code (DiscountCode + Discount)
	 * @param integer $value (necessary if min/max etc is supposed to be checked)
	 * @return boolean TRUE or error message
	 */
	public function isValid($code, $value = null) {
		if ((int)$code[$this->alias]['used']) {
			return __('discountCodeUsed');
		}
		if ((int)$code['Discount']['valid_from'] > 0 && $code['Discount']['valid_from'] > date(FORMAT_DB_DATETIME)) {
			return __('discountCodeNotValidAnymore');
		}
		if ((int)$code['Discount']['valid_until'] > 0 && $code['Discount']['valid_until'] < date(FORMAT_DB_DATETIME)) {
			return __('discountCodeNotYetValid');
		}
		if ($value !== null && $code['Discount']['min'] > 0 && $code['Discount']['min'] > $value) {
			return __('discountCodeMinAmountNotReached');
		}
		if (!$this->checkDate($code)) {
			return __('discountCodeDateInvalid');
		}
		return true;
	}

	/**
	 * DiscountCode::checkDate()
	 * Checks if today is between DiscountCode.created and DiscountCode.created+validityDays
	 *
	 * @param array $code array('DiscountCode'=>...)
	 */
	public function checkDate($code) {
		if (empty($code['Discount']['validity_days'])) {
			return true;
		}
		$validityDate = strtotime($code['DiscountCode']['created']) + $code['Discount']['validity_days'] * DAY;
		$now = time();
		if ($now <= $validityDate) {
			return true;
		}
		return false;
	}

	/**
	 * @param length (1...32) [a-z0-9]
	 */
	public function generateCode($length = null) {
		$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		if ($length === null) {
			if (!($length = Configure::read('DiscountCode.length'))) {
				$length = 8;
			}
		}
		return substr(md5($ip . microtime() . rand(1, 999999)), 0, $length);
	}

	/**
	 * DiscountCode::setFree()
	 * Sets a used Discountcode free, so that you can use it again
	 *
	 * @param integer $discountCodeId
	 */
	public function setFree($discountCodeId) {
		if ($discountCode = $this->find('first', array('conditions' => array('id' => $discountCodeId)))) {
			$discountCode['DiscountCode']['used'] = 0;
			$discountCode['DiscountCode']['model'] = '';
			$discountCode['DiscountCode']['redeemed_amount'] = 0.00;
			$this->create();
			$this->save($discountCode);
		}
	}

}
