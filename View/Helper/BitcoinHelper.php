<?php
App::uses('AppHelper', 'View/Helper');

/** Bitcoin Helper
 *
 * @author Mark Scherer
 * @link http://www.dereuromark.de
 * @license MIT
 */
class BitcoinHelper extends AppHelper {

	public $helpers = array('Html');

	public $settings = array();
	public $_defaults = array(
		'places' => 2,
		'dec' => '.',
		'sep' => ','
	);

	/**
	 *  Setup the config based on Config settings
	 */
	public function __construct(View $View, $settings = array()) {
		$this->settings = $this->_defaults;
		if ($x = Configure::read('Localization.decimals')) {
			$this->settings['dec'] = $x;
		}
		if ($x = Configure::read('Localization.thousands')) {
			$this->settings['sep'] = $x;
		}
		$this->settings = array_merge($this->settings, (array)Configure::read('Bitcoin'));

		parent::__construct($View, $settings);
	}

	/**
	 * display payment box
	 * 2011-07-20 ms
	 */
	public function paymentBox($amount, $address) {
		if ($address === null) {
			$address = Configure::read('Bitcoin.address');
		}
		$string = '<div class="bitcoinBox">';
		$string .= '<div class="amount">'.__('Value').': '.$this->value($amount).'</div>';
		$string .= '<code class="address">'.h($address).'</code>';
		$string .= '</div>';
		return $string;
	}


	/**
	 * 2011-07-20 ms
	 */
	public function donationBox($address = null) {
		if ($address === null) {
			$address = Configure::read('Bitcoin.address');
		}
		$string = '<div class="bitcoinBox">';
		$string .= '<code class="address">'.h($address).'</code>';
		$string .= '</div>';
		return $string;
	}

	public function image($size = null, $options = array()) {
		if ($size == 16) {
			$size = null;
		}
		$path = '/payment/img/bitcoin%s.png';
		$path = sprintf($path, (String)$size);
		return $this->Html->image($path, $options);
	}


	public function value($amount, $maxDecimals = null) {
		if ($maxDecimals === null) {
			$maxDecimals = $this->settings['places'];
		}
		return number_format($amount, $maxDecimals, $this->settings['dec'], $this->settings['sep']);
	}

}
