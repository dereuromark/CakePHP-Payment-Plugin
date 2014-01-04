<?php
App::uses('AppHelper', 'View/Helper');

class PaymentHelper extends AppHelper {

	public $helpers = array('Html', 'Session', 'Tools.Numeric', 'Payment.PrepaidAccount');

	public function radioButtons($paymentMethods, $options = array()) {
		$paymentTypes = array();

		if (isset($options['amount']) && $options['amount'] <= 0) {
			return array('prepaid' => __('(No payment method to choose)'));
		}

		foreach ($paymentMethods as $paymentMethod) {
			$icon = ' ' . $this->Html->image('/payment/img/' . $paymentMethod['PaymentMethod']['alias'] . '.png');

			$text = h($paymentMethod['PaymentMethod']['name']) . $icon;
			if ($paymentMethod['PaymentMethod']['alias'] === 'prepaid' && Configure::read('Project.code') === 'OF') {
				$text = __('Prepaid Account');
			}
			if ($paymentMethod['PaymentMethod']['alias'] === 'prepaid' && isset($options['prepaid'])) {
				if ($this->Session->read('Auth.User.id')) {
					$availableMoney = $this->Numeric->price($options['prepaid']);
					$text .= ' (' . __('available') . ': ' . $availableMoney . ')';
				} else {
					$text .= ' ' . __('loginNoticeForCheckout');
				}

			}

			if (!empty($paymentMethod['PaymentMethod']['duration'])) {
				$text .= '<br /><small>Dauer: ' . h($paymentMethod['PaymentMethod']['duration']) . '</small>';
			}
			$paymentTypes[$paymentMethod['PaymentMethod']['alias']] = $text;
		}
		return $paymentTypes;
	}

	public function form($type, $options = array()) {
		if (!method_exists($this, $type)) {
			trigger_error('Invalid Payment type');
			return '';
		}
		return $this->{$type}($options);
	}

	/**
	 * shouldnt be invoked - usually done right away on checkout
	 */
	public function prepaid($options) {
	}

	public function paypal($options) {
	}

	public function moneybookers($options) {
	}

	public function bitcoin($options) {
	}

	public function google($options) {
	}

	protected function _form($data) {
	}

}
