<?php

App::import('Helper', 'Payment.Bitcoin');
App::import('Lib', 'Tools.MyCakeTestCase');

/**
 * Bitcoin Test Case
 */
class BitcoinTest extends MyCakeTestCase {

	var $Bitcoin;

	/**
	 * setUp method
	 *
	 * @access public
	 * @return void
	 */
	function startCase() {
		$this->Bitcoin = new BitcoinHelper();
		$this->Bitcoin->initHelpers();
	}

	/**
	 * test image
	 *
	 * 2011-07-20 ms
	 */
	function testImage() {
		$res = $this->Bitcoin->image(null, array('title' => 'XYZ'));
		pr($res);

		$res = $this->Bitcoin->image(24);
		pr($res);

		$res = $this->Bitcoin->image(32);
		pr($res);

		$res = $this->Bitcoin->image(48, array('onclick'=>'alert(\'HI\')', 'title' => 'XYZ'));
		pr($res);

		$res = $this->Bitcoin->image(64, array('title' => 'XYZ'));
		pr($res);
	}


	function testBox() {
		$res = $this->Bitcoin->paymentBox(3.123456, '4578345734895734895734df34873847283478');
		pr($res);

		$res = $this->Bitcoin->paymentBox(4, '');
		pr($res);

		$res = $this->Bitcoin->donationBox('4578345734895734895734df34873847283478');
		pr($res);
	}




	/**
	 * tearDown method
	 *
	 * @access public
	 * @return void
	 */
	function tearDown() {
		//unset($this->Bitcoin);
	}

}
