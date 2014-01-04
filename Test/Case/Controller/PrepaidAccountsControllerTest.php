<?php
/* PrepaidAccounts Test cases generated on: 2011-07-30 00:27:48 : 1311978468*/
App::uses('PrepaidAccountsController', 'Payment.Controller');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class TestPrepaidAccountsController extends PrepaidAccountsController {

	public $autoRender = false;

	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class PrepaidAccountsControllerTest extends MyCakeTestCase {

	public function setUp() {
		$this->PrepaidAccounts = new TestPrepaidAccountsController();
		$this->PrepaidAccounts->constructClasses();
	}

	public function tearDown() {
		unset($this->PrepaidAccounts);
		ClassRegistry::flush();
	}

}
