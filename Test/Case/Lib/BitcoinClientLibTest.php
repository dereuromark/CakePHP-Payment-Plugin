<?php

App::uses('BitcoinLib', 'Payment.Lib');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class BitcoinClientLibTest extends MyCakeTestCase {

	public $c = null;

	public function setUp() {
		parent::setUp();

		$path = Configure::read('Bitcoin.config_path');
		if (!$path) {
			$path = APP . 'Config' . DS;
		}

		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

		if (true && !extension_loaded("curl")) {
			dl("php_curl.dll");//retardation on my PHP/Win7 install
		}
		if (!isset($this->c)) {
			$this->c = new BitcoinClient("http", "fnordbagger", "spambots", "localhost", 8332, null, 0);
		}
	}

	/**
	 * @expectedException BitcoinClientException
	 */
	public function testInvalidScheme() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$junk = new BitcoinClient("ftp", "bobo", "mypass", "kremvax.kremlin.su");
	}

	/**
	 * @expectedException BitcoinClientException
	 */
	public function testNoUsername() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$junk = new BitcoinClient("http", "", "mypass", "kremvax.kremlin.su");
	}

	/**
	 * @expectedException BitcoinClientException
	 */
	public function testNoPassword() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$junk = new BitcoinClient("https", "bobo", "", "kremvax.kremlin.su");
	}

	/**
	 * @expectedException BitcoinClientException
	 */
	public function testNoAddress() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$junk = new BitcoinClient("https", "bobo", "", "kremvax.kremlin.su");
	}

	/**
	 * @expectedException BitcoinClientException
	 */
	public function testInvalidPortString() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$junk = new BitcoinClient("http", "bobo", "mypass", "kremvax.kremlin.su", "yeehaw");
	}

	/**
	 * @expectedException BitcoinClientException
	 */
	public function testInvalidPortFloat() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$junk = new BitcoinClient("http", "bobo", "mypass", "kremvax@kremlin.su", 3.14159);
	}

	/**
	 * @expectedException BitcoinClientException
	 */
	public function testInvalidPortNegative() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$junk = new BitcoinClient("http", "bobo", "mypass", "kremvax@kremlin.su", -273);
	}

	/**
	 * @expectedException BitcoinClientException
	 */
	public function testInvalidPortPositive() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$junk = new BitcoinClient("http", "bobo", "mypass", "kremvax@kremlin.su", 65536);
	}

	/**
	 * @expectedException BitcoinClientException
	 */
	public function testUnreadableCertificate() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$junk = new BitcoinClient("http", "bobo", "mypass", "kremvax@kremlin.su", 8332, "/doesntexist.cert");
	}

	public function testgetaddress() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$this->assertTrue(Bitcoin::checkAddress($this->c->getnewaddress()));
	}

	public function testgetaddressWithLabel() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$address = $this->c->getnewaddress("test label");
	$this->assertTrue(Bitcoin::checkAddress($address));
	$this->assertEquals($this->c->getlabel($address), "test label");
	}

	public function testCan_connect() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$this->assertTrue($this->c->can_connect());
	}

	public function testQuery_arg_to_parameter() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$this->assertEquals($this->c->query_arg_to_parameter("string"), new jsonrpcval("string"));
	$this->assertEquals($this->c->query_arg_to_parameter("string with spaces"), new jsonrpcval("string with spaces"));
	$this->assertEquals($this->c->query_arg_to_parameter(3), new jsonrpcval(3, "int"));
	$this->assertEquals($this->c->query_arg_to_parameter(3.14159), new jsonrpcval(3.14159, "double"));
	$this->assertEquals($this->c->query_arg_to_parameter("3"), new jsonrpcval(3, "int"));
	$this->assertEquals($this->c->query_arg_to_parameter("3.14159"), new jsonrpcval(3.14159, "double"));
	$this->assertEquals($this->c->query_arg_to_parameter(true), new jsonrpcval(true, "boolean"));
	$this->assertEquals($this->c->query_arg_to_parameter(array("fnord")), new jsonrpcval(array("fnord"), "array"));
	}

	public function testBackupwallet() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	@unlink("G:/tmp/wallet.dat");
	$this->assertEquals($this->c->backupwallet("/tmp/wallet.dat"), '');
	@unlink("G:/tmp/wallet.dat");
	}

	/**
	 * @expectedException BitcoinClientException
	 */
	public function testBackupwalletBad1() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	touch("G:/tmp/wallet.dat");
	$this->c->backupwallet("/tmp/wallet.dat");
	@unlink("G:/tmp/wallet.dat");// NOTREACHED
	}

	/**
	 * @expectedException BitcoinClientException
	 */
	public function testBackupwalletBad2() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$this->c->backupwallet("/tmpqoxxx/wallet.dat");
	}

	/**
	 * @todo Add tests for getbalance(account, minconf)
	 */
	public function testGetbalance() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$this->assertInternalType("float", $this->c->getbalance());
	}

	public function testGetblockcount() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$this->assertInternalType("int", $this->c->getblockcount());
	}

	public function testGetblocknumber() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$this->assertInternalType("int", $this->c->getblocknumber());
	}

	public function testGetconnectioncount() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$this->assertInternalType("int", $this->c->getconnectioncount());
	}

	public function testGetdifficulty() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$this->assertInternalType("float", $this->c->getdifficulty());
	}

	public function testSetGetgenerate() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$this->assertEquals($this->c->setgenerate(true, 1), '');
	$this->assertTrue($this->c->getgenerate());
	$this->assertEquals($this->c->setgenerate(false, 0), '');
	$this->assertFalse($this->c->getgenerate());
	}

	public function testGetinfo() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$this->assertInternalType("array", $ret = $this->c->getinfo());
	$this->assertArrayHasKey("version", $ret);
	$this->assertInternalType("int", $ret["version"]);
	}

	/**
	 * @since 0.3.17
	 * @todo implement
	 */
	public function testGetaccount() {
	$this->markTestIncomplete();
	}

	/**
	 * @deprecated Since 0.3.17
	 */
	public function testLabel() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$stamp = md5(strval(time));
	$address = $this->c->getnewaddress();
	$this->assertEquals($this->c->setlabel($address, $stamp), '');
	$this->assertEquals($this->c->getlabel($address), $stamp);
	$this->assertEquals($this->c->getreceivedbylabel($stamp, 0), 0.00);
	$this->assertEquals($this->c->setlabel($address, ''), '');
	$this->assertEquals($this->c->getlabel($address), '');
	}

	/**
	 * @since 0.3.17
	 * @todo implement
	 */
	public function testSetaccount() {
	$this->markTestIncomplete();
	}

	public function testGetreceivedbyaddress() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	try {
		$this->assertEquals($this->c->getreceivedbyaddress("1Mnu2THcNAjd1diBJ79mhTXCxPeG3K6mLU"), 1.00);
	} catch (Exception $e) {
		do {
		printf("%s:%d %s (%d) [%s]\n", $e->getFile(), $e->getLine(), $e->getMessage(), $e->getCode(), get_class($e));
		} while ($e = $e->getPrevious());
	}
	$this->assertEquals($this->c->getreceivedbyaddress("1Mnu2THcNAjd1diBJ79mhTXCxPeG3K6mLU"), 1.00);
	$this->assertEquals($this->c->getreceivedbyaddress("1Kr7USMAgMo7fcPSWsQ7kGL12V3u4sNtjV"), 0.00);
	}

	/**
	 * @since 0.3.17
	 * @todo implement
	 */
	public function testGetreceivedbyaccount() {
	$this->markTestIncomplete();
	}

	public function testHelp($command = null) {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$this->assertInternalType("string", $ret = $this->c->help());
	}

	public function testListreceivedbyaddress() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$this->assertInternalType("array", $ret = $this->c->listreceivedbyaddress());
	}

	/*
	 * @since 0.3.17
	 * @todo implement
	 */

	public function testListreceivedbyaccount() {
	$this->markTestIncomplete();
	}

	/**
	 * @deprecated Since 0.3.17
	 */
	public function testListreceivedbylabel() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$this->assertInternalType("array", $ret = $this->c->listreceivedbylabel());
	}

	/**
	 * @todo Implement
	 */
	public function testSendtoaddress() {
	$this->markTestIncomplete();
	}

	/**
	 * @todo Implement?
	 */
	public function testStop() {
	$this->markTestIncomplete();
	}

	public function testValidateaddress() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

		$junk = $this->c->validateaddress('1Kr7USMAgMo7fcPSWsQ7kGL12V3u4sNtjV');
	$this->assertTrue($junk["isvalid"]);
	$junk = $this->c->validateaddress('1F417eczAAbh41V4oLGNf3DqXLY72hsM73');
	$this->assertTrue($junk["isvalid"]);
	$junk = $this->c->validateaddress('1F417eczAAbh41V4oLGNf3DqXLY72hsM7');
	$this->assertFalse($junk["isvalid"]);
	$junk = $this->c->validateaddress('fnordbarbaz');
	$this->assertFalse($junk["isvalid"]);
	}

	/**
	 * @todo Implement
	 * @since 0.3.18
	 */
	public function testGettransaction() {
	$this->markTestIncomplete();
	}

	/**
	 * @todo Implement
	 * @since 0.3.18
	 */
	public function testMove() {
	$this->markTestIncomplete();
	}

	/**
	 * @todo Implement
	 * @since 0.3.18
	 */
	public function testSendfrom() {
	$this->markTestIncomplete();
	}

	/**
	 * @todo This is a bit weak...
	 * @since 0.3.18
	 */
	public function testGetwork() {
		if ($this->skipIf(WINDOWS, '%s does not work on windows')) {
			return;
		}

	$this->junk = $this->c->getwork();
	$this->assertArrayHasKey("midstate", $this->junk);
	$this->assertArrayHasKey("data", $this->junk);
	$this->assertArrayHasKey("hash1", $this->junk);
	$this->assertArrayHasKey("target", $this->junk);
	}

	/**
	 * @todo Implement
	 * @since 0.3.18
	 */
	public function testGetaccountaddress() {
	$this->markTestIncomplete();
	}

	/**
	 * @todo Implement
	 */
	public function testGethashespersec() {
	$this->markTestIncomplete();
	}

	/**
	 * @todo Implement
	 * @since 0.3.18
	 */
	public function testGetaddressesbyaccount() {
	$this->markTestIncomplete();
	}

}
