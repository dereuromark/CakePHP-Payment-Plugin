<?php
/* Transactions Test cases generated on: 2011-09-23 12:44:16 : 1316774656*/
App::uses('TransactionsController', 'Payment.Controller');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class TestTransactionsController extends TransactionsController {

	public $autoRender = false;

	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class TransactionsControllerTest extends MyCakeTestCase {

	public function setUp() {
		$this->Transactions = new TestTransactionsController();
		$this->Transactions->constructClasses();
	}

	public function tearDown() {
		unset($this->Transactions);
		ClassRegistry::flush();
	}

}
