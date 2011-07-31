<?php
/* BitcoinAddress Test cases generated on: 2011-07-16 02:50:12 : 1310777412*/
App::import('Model', 'Payment.BitcoinAddress');
App::import('Vendor', 'MyCakeTestCase');

class BitcoinAddressTestCase extends MyCakeTestCase {
	function startTest() {
		$this->BitcoinAddress = ClassRegistry::init('BitcoinAddress');
	}

	function endTest() {
		unset($this->BitcoinAddress);
		ClassRegistry::flush();
	}

}