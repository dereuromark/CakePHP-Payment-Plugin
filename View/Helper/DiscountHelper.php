<?php
App::uses('AppHelper', 'View/Helper');

class DiscountHelper extends AppHelper {

	public $helpers = array('Html', 'Tools.QrCode', 'Tools.Numeric');

	/**
	 * @return string html
	 */
	public function image($code, $options = array()) {
		$url = $this->Html->url(array('controller' => 'discounts', 'action' => 'code', $code), true);
		$string = $this->QrCode->formatText($url, 'url');
		return $this->QrCode->image($string);
	}

	/**
	 * @param array $Discount (flat)
	 * @return string text;
	 */
	public function publicDetails($discount) {
		$res = array();
		if ($discount['amount']) {
			$res[] = $this->Numeric->money($discount['amount']);
		}
		if ($discount['factor']) {
			$res[] = $discount['factor'] . '%';
		}
		$res = 'Rabatt: ' . implode(' + ', $res);
		return $res;
	}

}
