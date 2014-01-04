<?php
/* Transaction Fixture generated on: 2011-09-23 12:09:45 : 1316773365 */
class TransactionFixture extends CakeTestFixture {

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'foreign_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'model' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'type' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => 'paypal, ...', 'charset' => 'utf8'),
		'transaction_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'transaction_type' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'note' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'amount' => array('type' => 'float', 'null' => false, 'default' => '0.00', 'length' => '9,2'),
		'fee_amount' => array('type' => 'float', 'null' => false, 'default' => '0.00', 'length' => '9,2'),
		'tax_amount' => array('type' => 'float', 'null' => false, 'default' => '0.00', 'length' => '9,2'),
		'currency_code' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 3, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'payment_type' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'payment_status' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'pending_reason' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'reason_code' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'order_time' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array()
	);

	public $records = array(
		array(
			'id' => 1,
			'foreign_id' => 'Lorem ipsum dolor sit amet',
			'model' => 'Lorem ipsum dolor sit amet',
			'type' => 'Lorem ipsum dolor sit amet',
			'transaction_id' => 'Lorem ipsum dolor sit amet',
			'transaction_type' => 'Lorem ipsum dolor sit amet',
			'note' => 'Lorem ipsum dolor sit amet',
			'amount' => 1,
			'fee_amount' => 1,
			'tax_amount' => 1,
			'currency_code' => 'L',
			'payment_type' => 'Lorem ipsum dolor sit amet',
			'payment_status' => 'Lorem ipsum dolor sit amet',
			'pending_reason' => 'Lorem ipsum dolor sit amet',
			'reason_code' => 'Lorem ipsum dolor sit amet',
			'order_time' => '2011-09-23 12:22:45',
			'created' => '2011-09-23 12:22:45'
		),
	);
}
