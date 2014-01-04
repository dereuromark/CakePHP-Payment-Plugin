<?php
/* BitcoinAddress Test cases generated on: 2011-07-16 02:50:12 : 1310777412*/
App::uses('BitcoinAddress', 'Payment.Model');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class BitcoinAddressTest extends MyCakeTestCase {

	public function setUp() {
		$this->BitcoinAddress = ClassRegistry::init('BitcoinAddress');
	}

	public function tearDown() {
		unset($this->BitcoinAddress);
		ClassRegistry::flush();
	}

}
