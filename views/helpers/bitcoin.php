<?php

/** Bitcoin Helper
 *
 * @author Mark Scherer
 * @link http://www.dereuromark.de
 * @license MIT
 */
class BitcoinHelper extends AppHelper {

	var $helpers = array('Html');

	var $settings = array();
	var $_defaults = array(
		'maxDecimals' => 2,
		'dec' => '.',
		'sep' => ','
	);

	/**
	 *  Setup the config based on Config settings
	 */
	function __construct() {
		$this->settings = $this->_defaults;
		if ($x = Configure::read('Localization.decimalPoint')) {
			$this->settings['dec'] = $x;
		}
		if ($x = Configure::read('Localization.thousandsSeparator')) {
			$this->settings['sep'] = $x;
		}
		$this->settings = am($this->settings, (array)Configure::read('Bitcoin'));

		parent::__construct();
	}

	/**
	 * display payment box
	 * 2011-07-20 ms
	 */
	function paymentBox($amount, $address) {
		if ($address === null) {
			$address = Configure::read('Bitcoin.address');
		}
		$string = '<div class="bitcoinBox">';
		$string .= '<div class="amount">'.__('Value', true).': '.$this->value($amount).'</div>';
  	$string .= '<code class="address">'.h($address).'</code>';
  	$string .= '</div>';
  	return $string;
	}


	/**
	 * 2011-07-20 ms
	 */
	function donationBox($address = null) {
		if ($address === null) {
			$address = Configure::read('Bitcoin.address');
		}
		$string = '<div class="bitcoinBox">';
  	$string .= '<code class="address">'.h($address).'</code>';
  	$string .= '</div>';
  	return $string;
	}

	function image($size = null, $options = array()) {
		if ($size == 16) {
			$size = null;
		}
		$path = '/payment/img/bitcoin%s.png';
		$path = sprintf($path, (String)$size);
		return $this->Html->image($path, $options);
	}


	function value($amount, $maxDecimals = null) {
		if ($maxDecimals === null) {
			$maxDecimals = $this->settings['maxDecimals'];
		}
  	return number_format($amount, $maxDecimals, $this->settings['dec'], $this->settings['sep']);
	}

}

?>
