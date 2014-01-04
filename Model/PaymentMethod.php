<?php
App::uses('PaymentAppModel', 'Payment.Model');

class PaymentMethod extends PaymentAppModel {

	public $order = array('PaymentMethod.sort' => 'DESC', 'PaymentMethod.name' => 'ASC');

	public $actsAs = array('Tools.DecimalInput');

	public $validate = array(
		'sort' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'valErrMandatoryField'
			),
		),
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'valErrMandatoryField',
				'last' => true
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'valErrRecordNameAlreadyExists',
			),
		),
		'set_rate' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'valErrMandatoryField',
				'last' => true
			),
			'range' => array(
				'rule' => array('validateRange', -4.99, 4.99),
				'message' => 'Please enter a number between -4.99 and 4.99 (€)'
			)
		),
		'rel_rate' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'valErrMandatoryField',
				'last' => true
			),
			'range' => array(
				'rule' => array('validateRange', -0.99, 0.99),
				'message' => 'Please enter a number between -0.99 (-99%) and 0.99 (99%)'
			)
		),
		'url' => array(
			'rule' => array('validateUrl'),
			'message' => 'Dies scheint keine gültige Url zu sein',
			'allowEmpty' => true
		),
		'active' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'valErrMandatoryField'
			),
		),
		'hook' => array(
			'numeric' => array(
				'rule' => array('validateHook'),
				'message' => 'valErrMandatoryField',
				'allowEmpty' => true
			),
		),
		'details' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 255),
				'message' => array('valErrMaxCharacters %s', 255),
				'allowEmpty' => true
			),
		),
	);

	public function validateRange($data, $min, $max) {
		$val = array_shift($data);
		return $val >= $min && $val <= $max;
	}

	public function validateHook($data) {
		$val = array_shift($data);
		if (!in_array($val, self::hooks())) {
			return false;
		}
		return true;
	}

	public function beforeValidate($options = array()) {
		parent::beforeValidate($options);
		if (isset($this->data[$this->alias]['name'])) {
			$this->data[$this->alias]['name'] = ucfirst($this->data[$this->alias]['name']);
		}

		if (isset($this->data[$this->alias]['set_rate']) && empty($this->data[$this->alias]['set_rate'])) {
			$this->data[$this->alias]['set_rate'] = (float)$this->data[$this->alias]['set_rate'];
		}
		if (isset($this->data[$this->alias]['rel_rate']) && empty($this->data[$this->alias]['rel_rate'])) {
			$this->data[$this->alias]['rel_rate'] = (float)$this->data[$this->alias]['rel_rate'];
		}

		return true;
	}

	public function beforeSave($options = array()) {
		$ret = parent::beforeSave($options);

		# replace name with placeholder
		self::unprep($this->data[$this->alias]);

		return $ret;
	}

	/**
	 * Get price (rel + set) for payment method
	 *
	 * @param $id int PaymentMethod id
	 * @return array: array(rel_rate=>x, set_rate=>y) or false on failure
	 */
	public function getQuotes($id) {
		$data = $this->get($id, array('rel_rate', 'set_rate'));
		if (empty($data)) {
			return false;
		}
		return $data[$this->alias];
	}

	public function calculate($amount, $paymentMethod) {
		if (!is_array($paymentMethod)) {
			$paymentMethod = $this->get($paymentMethod);
			$paymentMethod = $paymentMethod[$this->alias];
		}
		$res = $amount + $paymentMethod['set_rate'] + $amount * $paymentMethod['rel_rate'];
		return $res;
	}

	/**
	 * @return false or array $record with updated data
	 */
	public function vote($id) {
		$record = $this->get($id);
		if (empty($record)) {
			return false;
		}
		$this->id = $id;
		if (!$this->saveField('votes', $record[$this->alias]['votes'] + 1)) {
			return false;
		}
		return $record;
	}

/** Static **/

	/**
	 * Replace {} with name etc
	 *
	 * @static
	 */
	public function prep(&$record) {
		if (isset($record['hint']) && isset($record['name'])) {
			$record['hint'] = String::insert($record['hint'], array('name' => $record['name']), array('before' => '{', 'after' => '}', 'clean' => true));
		}
	}

	public function unprep(&$record) {
		if (isset($record['hint']) && isset($record['name'])) {
			$record['hint'] = str_ireplace($record['name'], '{name}', $record['hint']);
		}
	}

	/**
	 * Maybe call it "processor"?
	 * Static Model::method()
	 *
	 * @static
	 */
	public function hooks($value = null) {
		$options = (array)Configure::read('Payment');
		return $options;
		//return parent::enum($value, $options);
	}

}
