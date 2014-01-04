<?php

App::uses('BitcoinLib', 'Payment.Lib');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class BitcoinLibTest extends MyCakeTestCase {

	public $Bitcoin;

	public function setUp() {
		$this->Bitcoin = new BitcoinLib();
	}

	public function testObject() {
		$this->assertTrue(is_object($this->Bitcoin));
		$this->assertInstanceOf('BitcoinLib', $this->Bitcoin);
	}

	public function tearDown() {
		unset($this->Bitcoin);
	}

	/**
	 * @todo Implement testHash160ToAddress().
	 */
	public function testHash160ToAddress() {
	// Remove the following lines when you implement this test.
	//$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testAddressToHash160().
	 */
	public function testAddressToHash160() {
	// Remove the following lines when you implement this test.
	//$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test Bitcoin::checkAddress() with various good and bad addresses.
	 */
	public function testCheckAddress() {
	$this->assertTrue(Bitcoin::checkAddress("1pA14Ga5dtzA1fbeFRS74Ri32dQjkdKe5"));
	$this->assertTrue(Bitcoin::checkAddress("1MU97wyf7msCVdaapneW2dW1uXP7oEQsFA"));
	$this->assertTrue(Bitcoin::checkAddress("1F417eczAAbh41V4oLGNf3DqXLY72hsM73"));
	$this->assertTrue(Bitcoin::checkAddress("1ASgNrpNNejRJVfqK2jHmfJ3ZQnMSUJkwJ"));
	$this->assertFalse(Bitcoin::checkAddress("1ASgNrpNNejRJVfqK2jHmfJ3ZQnMSUJ"));
	$this->assertFalse(Bitcoin::checkAddress("1111111fnord"));
	}

	/**
	 * @todo Implement testPubKeyToAddress().
	 */
	public function testPubKeyToAddress() {
	// Remove the following lines when you implement this test.
	//$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test the Bitcoin::remove0x() function.
	 */
	public function testRemove0x() {
		$this->assertEquals(Bitcoin::remove0x("abcdefg"), "abcdefg");
		$this->assertEquals(Bitcoin::remove0x("0xabcdefg"), "abcdefg");
		$this->assertEquals(Bitcoin::remove0x("0Xabcdefg"), "abcdefg");
	}

	public function testGetTotalBitcoins() {
		$res = $this->Bitcoin->getTotalBitcoins();
		pr($res);
		$this->assertTrue(!empty($res) && $res >= 6823150);
	}

	public function testGetDifficulty() {
		$res = $this->Bitcoin->getDifficulty();
		pr($res);
		$this->assertTrue(!empty($res) && $res >= 1563027);

		$resOffline = $this->Bitcoin->_query('getdifficulty');
		pr($resOffline);
		$this->assertTrue(!empty($resOffline) && $resOffline >= 1563027);

		$this->assertWithinMargin($res, $resOffline, 100);
	}

	public function testGetBlockCount() {
		$res = $this->Bitcoin->getBlockCount();
		pr($res);
		$this->assertTrue(!empty($res) && $res >= 137078);

		$resOffline = $this->Bitcoin->_query('getblockcount');
		pr($resOffline);
		$this->assertTrue(!empty($resOffline) && $resOffline >= 137078);

		$this->assertEquals($res, $resOffline);
	}

	public function testAddressFirstSeen() {
		$res = $this->Bitcoin->addressFirstSeen('161AcnPykE42e4ErQNR9B73Bb78Jy81AN6');
		pr($res);
		$this->assertTrue(!empty($res) && $res = 'Never seen');
		/*
		$res = $this->Bitcoin->addressFirstSeen('161AcnPykE42e4ErQNR9B73Bb78Jy81AN62');
		pr($res);
		$this->assertTrue(empty($res));
		*/

		$res = $this->Bitcoin->addressFirstSeen('1PJ3Jy1T36BzxuikZDXY5YV7YjTmfcvQNc');
		pr($res);
		$this->assertTrue(!empty($res) && substr($res, 0, 4) === '2011');
	}

	public function testMyTransactions() {
		$res = $this->Bitcoin->myTransactions('1PJ3Jy1T36BzxuikZDXY5YV7YjTmfcvQNc');
		pr($res);
		$this->assertTrue(!empty($res));
	}

	public function testGetTransaction() {
		$res = $this->Bitcoin->getTransaction('e5b0f6297fa6743e0c2126fe5bda7b894a95bae7aae37d2695756b68468e4732');
		pr($res);
		if (!$this->Bitcoin->settings['daemon']) {
			$this->assertTrue(empty($res));
		} else {
			$this->assertTrue(!empty($res));
		}
	}

	public function testGetBalance() {
		$res = $this->Bitcoin->getBalance();
		pr($res);
		if (!$this->Bitcoin->settings['daemon']) {
			$this->assertFalse($res);
		} else {
			$this->assertTrue(is_numeric($res)); # can be < > or =
		}
	}

	public function testGetReceivedByAddress() {
		$res = $this->Bitcoin->getReceivedByAddress('1PJ3Jy1T36BzxuikZDXY5YV7YjTmfcvQNc');
		pr($res);
		$this->assertTrue(is_numeric($res) && $res > 0);

		$res = $this->Bitcoin->getReceivedByAddress('17pbR4ExxFvx6WePiMVg3a9CC4fPmxsVMJ');
		pr($res);
		$this->assertTrue(is_numeric($res) && $res > 0);

		$res = $this->Bitcoin->getReceivedByAddress('1MPj4jENy5Lcwe9ADXeXeSJwEm5r7NowkZ');
		pr($res);
		$this->assertTrue(is_numeric($res) && $res == 0);
	}

	public function testGetTotalSentByAddress() {
		$res = $this->Bitcoin->getTotalSentByAddress('1PJ3Jy1T36BzxuikZDXY5YV7YjTmfcvQNc');
		pr($res);
		$this->assertTrue(is_numeric($res) && $res > 0);

		$res = $this->Bitcoin->getTotalSentByAddress('17pbR4ExxFvx6WePiMVg3a9CC4fPmxsVMJ');
		pr($res);
		$this->assertTrue(is_numeric($res) && $res > 0);
	}

	public function testValidateTransaction() {
		$res = $this->Bitcoin->validateTransaction('e5b0f6297fa6743e0c2126fe5bda7b894a95bae7aae37d2695756b68468e4732');
		$this->assertTrue($res);

		$res = $this->Bitcoin->validateAddress('eVb0f6297fa6743e0c2126fe5bda7b894a95bae7aae37d2695756b68468e4732');
		$this->assertFalse($res);
	}

	public function testValidateAddress() {
		$res = $this->Bitcoin->validateAddress('1PJ3Jy1T36BzxuikZDXY5YV7YjTmfcvQNc');
		$this->assertTrue($res);

		$res = $this->Bitcoin->validateAddress('2C5GSxS1ozWFB9sVX7CkFNHptY1kYaBBM5');
		$this->assertFalse($res);
	}

}
