<?php
/* BitcoinAddresses Test cases generated on: 2011-07-16 02:50:23 : 1310777423*/
App::uses('BitcoinAddressesController', 'Payment.Controller');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class TestBitcoinAddressesController extends BitcoinAddressesController {

	public $autoRender = false;

	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class BitcoinAddressesControllerTest extends MyCakeTestCase {

	public function setUp() {
		$this->BitcoinAddresses = new TestBitcoinAddressesController();
		$this->BitcoinAddresses->constructClasses();
	}

	public function tearDown() {
		unset($this->BitcoinAddresses);
		ClassRegistry::flush();
	}

}
