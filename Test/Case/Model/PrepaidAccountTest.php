<?php

App::uses('PrepaidAccount', 'Payment.Model');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class PrepaidAccountTest extends MyCakeTestCase {

	public $fixtures = array('plugin.payment.PrepaidAccount', 'plugin.tools.Log', 'user');

	public $PrepaidAccount = null;

	public function setUp() {
		$this->PrepaidAccount = ClassRegistry::init('Payment.PrepaidAccount');
	}

	public function tearDown() {
		unset($this->PrepaidAccount);
		ClassRegistry::flush();
	}

	public function testAccount() {
		$this->PrepaidAccount->truncate();
		//$this->PrepaidAccount->Log->truncate();

		$account = $this->PrepaidAccount->account('x');
		pr($account);
		$this->assertTrue(!empty($account) && is_array($account));

		$res = $this->PrepaidAccount->pay('x', 2);
		$this->assertSame(0, $res);

		$res = $this->PrepaidAccount->deposit('x', 2);
		$this->assertTrue($res);
		$current = $this->PrepaidAccount->availableMoney('x');
		$this->assertEquals(2, $current);

		$res = $this->PrepaidAccount->pay('x', 1.90);
		$this->assertSame(1.90, $res);
		$current = $this->PrepaidAccount->availableMoney('x');
		$this->assertEquals(0.10, $current);
	}

	public function testSendOverviewEmail() {
		$res = $this->PrepaidAccount->sendOverviewEmail();
		$this->assertTrue($res);
	}

}
