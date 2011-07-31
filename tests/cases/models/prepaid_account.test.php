<?php

App::import('Model', 'Payment.PrepaidAccount');
App::import('Lib', 'Tools.MyCakeTestCase');

class PrepaidAccountTestCase extends MyCakeTestCase {
	var $fixtures = array('Payment.prepaid_account_fixture');

	var $PrepaidAccount = null;

	function startTest() {
		$this->PrepaidAccount = ClassRegistry::init('PrepaidAccount');
	}

	function endTest() {
		unset($this->PrepaidAccount);
		ClassRegistry::flush();
	}
	
	
	function testAccount() {
		$this->PrepaidAccount->truncate();
		//$this->PrepaidAccount->Log->truncate();
		
		$account = $this->PrepaidAccount->account('x');
		pr($account);
		$this->assertTrue(!empty($account) && is_array($account));
		
		
		$res = $this->PrepaidAccount->pay('x', 2);	
		$this->assertIdentical(0, $res);
		
		$res = $this->PrepaidAccount->deposit('x', 2);
		$this->assertTrue($res);
		$current = $this->PrepaidAccount->availableMoney('x');
		$this->assertEqual(2, $current);
		
		$res = $this->PrepaidAccount->pay('x', 1.90);
		$this->assertIdentical(1.90, $res);
		$current = $this->PrepaidAccount->availableMoney('x');
		$this->assertEqual(0.10, $current);
		
	}

}