<?php

App::uses('BitcoinHelper', 'Payment.View/Helper');
App::uses('MyCakeTestCase', 'Tools.TestSuite');
App::uses('View', 'View');
App::uses('Controller', 'Controller');

/**
 * Bitcoin Test Case
 */
class BitcoinTest extends MyCakeTestCase {

	public $Bitcoin;

	/**
	 * SetUp method
	 *
	 * @access public
	 * @return void
	 */
	public function setUp() {
		$this->Bitcoin = new BitcoinHelper(new View(new Controller(new CakeRequest(null, false), null)));
	}

	/**
	 * Test image
	 *
	 * 2011-07-20 ms
	 */
	public function testImage() {
		$res = $this->Bitcoin->image(null, array('title' => 'XYZ'));
		pr($res);

		$res = $this->Bitcoin->image(24);
		pr($res);

		$res = $this->Bitcoin->image(32);
		pr($res);

		$res = $this->Bitcoin->image(48, array('onclick' => 'alert(\'HI\')', 'title' => 'XYZ'));
		pr($res);

		$res = $this->Bitcoin->image(64, array('title' => 'XYZ'));
		pr($res);
	}


	public function testBox() {
		$res = $this->Bitcoin->paymentBox(3.123456, '4578345734895734895734df34873847283478');
		pr($res);

		$res = $this->Bitcoin->paymentBox(4, '');
		pr($res);

		$res = $this->Bitcoin->donationBox('4578345734895734895734df34873847283478');
		pr($res);
	}




	/**
	 * TearDown method
	 *
	 * @access public
	 * @return void
	 */
	public function tearDown() {
		//unset($this->Bitcoin);
	}

}
