<?php
/* PrepaidAccounts Test cases generated on: 2011-07-30 00:27:48 : 1311978468*/
App::import('Controller', 'payment.PrepaidAccounts');
App::import('Vendor', 'MyCakeTestCase');

class TestPrepaidAccountsController extends PrepaidAccountsController {
	var $autoRender = false;

	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class PrepaidAccountsControllerTestCase extends MyCakeTestCase {
	function startTest() {
		$this->PrepaidAccounts = new TestPrepaidAccountsController();
		$this->PrepaidAccounts->constructClasses();
	}

	function endTest() {
		unset($this->PrepaidAccounts);
		ClassRegistry::flush();
	}

}