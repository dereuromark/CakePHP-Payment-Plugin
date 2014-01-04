<?php
/* Discounts Test cases generated on: 2011-05-26 02:52:12 : 1306371132*/
App::uses('DiscountsController', 'Payment.Controller');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class TestDiscountsController extends DiscountsController {

	public $autoRender = false;

	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class DiscountsControllerTest extends MyCakeTestCase {

	public $fixtures = array('app.discount', 'app.discount_code');

	public function setUp() {
		$this->Discounts = new TestDiscountsController();
		$this->Discounts->constructClasses();
	}

	public function tearDown() {
		unset($this->Discounts);
		ClassRegistry::flush();
	}

	public function testIndex() {
	}

	public function testView() {
	}

	public function testAdd() {
	}

	public function testEdit() {
	}

	public function testDelete() {
	}

	public function testAdminIndex() {
	}

	public function testAdminView() {
	}

	public function testAdminAdd() {
	}

	public function testAdminEdit() {
	}

	public function testAdminDelete() {
	}

}
