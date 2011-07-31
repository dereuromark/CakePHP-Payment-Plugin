<?php
/* BitcoinAddresses Test cases generated on: 2011-07-16 02:50:23 : 1310777423*/
App::import('Controller', 'Payment.BitcoinAddresses');
App::import('Vendor', 'MyCakeTestCase');

class TestBitcoinAddressesController extends BitcoinAddressesController {
	var $autoRender = false;

	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class BitcoinAddressesControllerTestCase extends MyCakeTestCase {
	function startTest() {
		$this->BitcoinAddresses = new TestBitcoinAddressesController();
		$this->BitcoinAddresses->constructClasses();
	}

	function endTest() {
		unset($this->BitcoinAddresses);
		ClassRegistry::flush();
	}

}