<?php
/* DiscountCode Test cases generated on: 2011-05-26 02:50:57 : 1306371057*/
App::uses('DiscountCode', 'Payment.Model');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class DiscountCodeTest extends MyCakeTestCase {

	public $fixtures = array('app.discount_code', 'app.discount');

	public function setUp() {
		$this->DiscountCode = ClassRegistry::init('Payment.DiscountCode');
	}

	public function tearDown() {
		unset($this->DiscountCode);
		ClassRegistry::flush();
	}

	public function testGenerate() {
		$is = $this->DiscountCode->generateCode(12);
		$this->assertTrue(strlen($is) === 12);
	}

	public function testIsValid() {
		# errors
		$code = array(
			'DiscountCode' => array(
				'used' => 1,
			),
		);
		$is = $this->DiscountCode->isValid($code);
		pr($is);
		$this->assertTrue($is !== true);

		$code = array(
			'DiscountCode' => array(
				'used' => 0,
			),
			'Discount' => array(
				'valid_from' => '2013-01-01',
				'valid_until' => '2014-12-12',
			),
		);
		$is = $this->DiscountCode->isValid($code);
		pr($is);
		$this->assertTrue($is !== true);

		$code = array(
			'DiscountCode' => array(
				'used' => 0,
			),
			'Discount' => array(
				'valid_from' => '2009-01-01',
				'valid_until' => '2010-12-12',
			),
		);
		$is = $this->DiscountCode->isValid($code);
		pr($is);
		$this->assertTrue($is !== true);

		# ok
		$code = array(
			'DiscountCode' => array(
				'used' => 0,
			),
			'Discount' => array(
				'valid_from' => '2011-01-01',
				'valid_until' => '2012-12-12',
			),
		);
		$is = $this->DiscountCode->isValid($code);
		$this->assertTrue($is);

		$code = array(
			'DiscountCode' => array(
				'used' => 0,
			),
			'Discount' => array(
				'valid_from' => '2009-01-01',
				'valid_until' => '2019-12-12',
				'min' => 3
			),
		);
		$is = $this->DiscountCode->isValid($code, 2);
		pr($is);
		$this->assertTrue($is !== true);

		$code = array(
			'DiscountCode' => array(
				'used' => 0,
			),
			'Discount' => array(
				'valid_from' => '2009-01-01',
				'valid_until' => '2019-12-12',
				'min' => 1
			),
		);
		$is = $this->DiscountCode->isValid($code, 2);
		$this->assertTrue($is);
	}

	public function testIsValidWithValidateDays() {
		$code = array(
			'DiscountCode' => array(
				'used' => 0,
				'created' => date(FORMAT_DB_DATETIME, time() - DAY - HOUR)
			),
			'Discount' => array(
				'validity_days' => '1',
				'valid_from' => '2011-01-01',
				'valid_until' => '2014-12-12',
			),
		);
		$is = $this->DiscountCode->isValid($code);
		pr($is);
		$this->assertTrue($is !== true);

		# ok
		$code = array(
			'DiscountCode' => array(
				'used' => 0,
				'created' => date(FORMAT_DB_DATETIME, time() - HOUR)
			),
			'Discount' => array(
				'validity_days' => '1',
				'valid_from' => '2011-01-01',
				'valid_until' => '2014-12-12',
			),
		);
		$is = $this->DiscountCode->isValid($code);
		pr($is);
		$this->assertTrue($is);
	}

}
