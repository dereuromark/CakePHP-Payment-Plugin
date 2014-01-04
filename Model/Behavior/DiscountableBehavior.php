<?php
App::uses('ModelBehavior', 'Model');
/**
 * Copyright 2011, dereuromark (http://www.dereuromark.de)
 *
 * @cakephp 2.0
 * @link    http://github.com/dereuromark/
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class DiscountableBehavior extends ModelBehavior {

	public $Discount;

	public function setup(Model $Model, $settings = array()) {
		$default = array('message' => __('Please confirm the checkbox'), 'field' => 'confirm', 'model' => null, 'before' => 'validate');

		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = $default;
		}

		$this->settings[$Model->alias] = array_merge($this->settings[$Model->alias], is_array($settings) ? $settings : array());

		$this->Discount = ClassRegistry::init('Discount');
	}

	public function beforeValidate(Model $Model, $options = array()) {
		$return = parent::beforeValidate($Model, $options);

		if ($this->settings[$Model->alias]['before'] === 'validate') {
			# we dont want to return the value, because other fields might then not be validated
			# (save will not continue with errors, anyway)
			$return = $this->confirm($Model, $return);
		}

		return $return;
	}

	public function beforeSave(Model $Model, $options = array()) {
		$return = parent::beforeSave($Model, $options);

		if ($this->settings[$Model->alias]['before'] === 'save') {
			return $this->confirm($Model, $return);
		}
		//pr($Model->data);
		return $return;
	}

	/**
	 * redeem the code
	 */
	public function afterSave(Model $Model, $created, $options = array()) {
		parent::afterSave($Model, $created, $options);

		$discountCode = (array)$Model->Session->read('DiscountCode');
		if (empty($discountCode)) {
			return;
		}
		//pr($Model->data); die();
	}

	/**
	 * Run before a model is saved, used...
	 *
	 * @param object $Model Model about to be saved.
	 * @return boolean true if save should proceed, false otherwise
	 */
	public function confirm(Model $Model, $return = true) {
		$discountCode = (array)$Model->Session->read('DiscountCode');
		if (empty($discountCode)) {
			return true;
		}
		$discount = $this->Discount->get($discountCode['discount_id']);

		$orderId = $Model->Session->read('Order.id');
		if ($Model->alias === 'Order') {
			$this->Order = $Model;
		} else {
			$this->Order = ClassRegistry::init('Order');
		}
		$cartItems = $this->Order->getCartItems($orderId);

		$discount = array_merge(array('DiscountCode' => $discountCode), $discount);

		$value = Order::calcTotal($cartItems);

		$res = $this->Discount->DiscountCode->isValid($discount, $value);
		if ($res === true) {
			return true;
		}

		$Model->invalidate('code', $res);
		return $return;
	}

}
