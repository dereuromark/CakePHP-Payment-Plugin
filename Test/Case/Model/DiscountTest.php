<?php
/* Discount Test cases generated on: 2011-05-26 02:52:02 : 1306371122*/
App::uses('Discount', 'Payment.Model');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class DiscountTest extends MyCakeTestCase {

	public $fixtures = array('app.discount', 'app.discount_code');

	public function setUp() {
		$this->Discount = ClassRegistry::init('Payment.Discount');
	}

	public function tearDown() {
		unset($this->Discount);
		ClassRegistry::flush();
	}

	public function testCalculate() {
		$discount = array('amount' => 0, 'factor' => 0);
		$was = 2;
		$res = $this->Discount->calculate($was, $discount);
		$this->assertEquals($was, $res);

		$discount = array('amount' => 0.1, 'factor' => 0);
		$was = 2.2;
		$res = $this->Discount->calculate($was, $discount);
		$this->assertEquals($was - 0.1, $res);

		$discount = array('amount' => 0, 'factor' => 10);
		$was = 2.2;
		$res = $this->Discount->calculate($was, $discount);
		$this->assertEquals($was - 0.22, $res);

		$discount = array('amount' => 1, 'factor' => 10);
		$was = 2.2;
		$res = $this->Discount->calculate($was, $discount);
		$this->assertEquals($was - 1.12, $res);

		$discount = array('amount' => 10, 'factor' => 0);
		$was = 2.2;
		$res = $this->Discount->calculate($was, $discount);
		$this->assertEquals(0, $res);
	}

	public function testCalculateRedeemedAmount() {
		$discount = array('amount' => 0, 'factor' => 0);
		$was = 2;
		$res = $this->Discount->calculateRedeemedAmount($was, $discount);
		$this->assertEquals(0, $res);

		$discount = array('amount' => 0.1, 'factor' => 0);
		$was = 2.2;
		$res = $this->Discount->calculateRedeemedAmount($was, $discount);
		$this->assertEquals(0.1, $res);

		$discount = array('amount' => 0, 'factor' => 10);
		$was = 2.2;
		$res = $this->Discount->calculateRedeemedAmount($was, $discount);
		$this->assertEquals(0.22, $res);

		$discount = array('amount' => 1, 'factor' => 10);
		$was = 2.2;
		$res = $this->Discount->calculateRedeemedAmount($was, $discount);
		$this->assertEquals(1.12, $res);

		$discount = array('amount' => 10, 'factor' => 0);
		$was = 2.2;
		$res = $this->Discount->calculateRedeemedAmount($was, $discount);
		$this->assertEquals(2.2, $res);

		$discount = array('amount' => 2, 'factor' => 50);
		$was = 2.4;
		$res = $this->Discount->calculateRedeemedAmount($was, $discount);
		$this->assertEquals(2.2, $res);
	}

}
