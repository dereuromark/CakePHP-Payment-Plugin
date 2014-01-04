<?php
/* DiscountCodes Test cases generated on: 2011-05-26 02:52:41 : 1306371161*/
App::uses('DiscountCodesController', 'Payment.Controller');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class TestDiscountCodesController extends DiscountCodesController {

	public $autoRender = false;

	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class DiscountCodesControllerTest extends MyCakeTestCase {

	public $fixtures = array('app.discount_code', 'app.discount');

	public function setUp() {
		$this->DiscountCodes = new TestDiscountCodesController();
		$this->DiscountCodes->constructClasses();
	}

	public function tearDown() {
		unset($this->DiscountCodes);
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
