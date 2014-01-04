<?php
App::uses('AppHelper', 'View/Helper');

/** PrepaidAccount Helper
 *
 * @author Mark Scherer
 * @link http://www.dereuromark.de
 * @license MIT
 */
class PrepaidAccountHelper extends AppHelper {

	public $helpers = array('Html');

	/**
	 *
	 */
	public function __construct(View $View, $settings = array()) {

		parent::__construct($View, $settings);
	}

	public function radioButtons($paymentMethods) {
		$paymentTypes = array();
		foreach ($paymentMethods as $paymentMethod) {
			$icon = $this->Html->image('/payment/img/' . $paymentMethod['PaymentMethod']['alias'] . '.png') . ' ';

			$text = $icon . h($paymentMethod['PaymentMethod']['name']);
			if (!empty($paymentMethod['PaymentMethod']['duration'])) {
				$text .= '<br /><small>Dauer: ' . h($paymentMethod['PaymentMethod']['duration']) . '</small>';
			}
			$paymentTypes[$paymentMethod['PaymentMethod']['alias']] = $text;
		}
		return $paymentTypes;
	}

}
