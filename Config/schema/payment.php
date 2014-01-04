<?php
/* generated on: 2011-11-20 20:32:19 : 1321817539 */
class PaymentSchema extends CakeSchema {

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $bitcoin_addresses = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary', 'collate' => null, 'comment' => ''),
		'account' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'address' => array('type' => 'string', 'null' => false, 'length' => 34, 'collate' => 'utf8_unicode_ci', 'comment' => 'bitcoin address', 'charset' => 'utf8'),
		'amount_received' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '10,4', 'collate' => null, 'comment' => ''),
		'amount_sent' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '10,4', 'collate' => null, 'comment' => ''),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null, 'collate' => null, 'comment' => ''),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null, 'collate' => null, 'comment' => ''),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);

	public $bitcoin_transactions = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary', 'collate' => null, 'comment' => ''),
		'address_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10, 'collate' => null, 'comment' => ''),
		'model' => array('type' => 'string', 'null' => false, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'foreign_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'amount' => array('type' => 'float', 'null' => false, 'default' => '0.0000', 'length' => '10,4', 'collate' => null, 'comment' => ''),
		'amount_expected' => array('type' => 'float', 'null' => false, 'default' => '0.0000', 'length' => '10,4', 'collate' => null, 'comment' => 'expected amount in bitcoins'),
		'confirmations' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10, 'collate' => null, 'comment' => ''),
		'details' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_unicode_ci', 'comment' => 'can transport some information', 'charset' => 'utf8'),
		'payment_fee' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '10,4', 'collate' => null, 'comment' => ''),
		'status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => null, 'comment' => '0=pending,1=finished'),
		'refund_address' => array('type' => 'string', 'null' => false, 'length' => 34, 'collate' => 'utf8_unicode_ci', 'comment' => 'only necessary if refund has to be granted', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null, 'collate' => null, 'comment' => ''),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null, 'collate' => null, 'comment' => ''),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);

	public $discount_codes = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary', 'collate' => null, 'comment' => ''),
		'discount_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10, 'collate' => null, 'comment' => ''),
		'code' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'used' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'collate' => null, 'comment' => ''),
		'model' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'foreign_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null, 'collate' => null, 'comment' => ''),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null, 'collate' => null, 'comment' => ''),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);

	public $discounts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary', 'collate' => null, 'comment' => ''),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'factor' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => null, 'comment' => 'percent'),
		'amount' => array('type' => 'float', 'null' => false, 'default' => '0.00', 'length' => '6,2', 'collate' => null, 'comment' => ''),
		'unlimited' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'collate' => null, 'comment' => ''),
		'min' => array('type' => 'integer', 'null' => false, 'default' => '0', 'collate' => null, 'comment' => ''),
		'valid_from' => array('type' => 'datetime', 'null' => true, 'default' => null, 'collate' => null, 'comment' => ''),
		'valid_until' => array('type' => 'datetime', 'null' => true, 'default' => null, 'collate' => null, 'comment' => ''),
		'model' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'foreign_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'details' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'comment' => 'json encoded!', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null, 'collate' => null, 'comment' => ''),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null, 'collate' => null, 'comment' => ''),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);

	public $payment_methods = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary', 'collate' => null, 'comment' => ''),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'collate' => null, 'comment' => ''),
		'set_rate' => array('type' => 'float', 'null' => false, 'default' => '0.00', 'length' => '6,2', 'collate' => null, 'comment' => 'fixed charge'),
		'rel_rate' => array('type' => 'float', 'null' => false, 'default' => '0.0000', 'length' => '5,4', 'collate' => null, 'comment' => 'relative to amount'),
		'sort' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10, 'collate' => null, 'comment' => ''),
		'description' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'hint' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'details' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'duration' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'name' => array('type' => 'string', 'null' => false, 'length' => 250, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'alias' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'hook' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => 'what function is supposed to be triggered', 'charset' => 'utf8'),
		'url' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'comment' => 'homepage', 'charset' => 'utf8'),
		'votes' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10, 'collate' => null, 'comment' => ''),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'collate' => null, 'comment' => ''),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'collate' => null, 'comment' => ''),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);

	public $prepaid_accounts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary', 'collate' => null, 'comment' => ''),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'amount' => array('type' => 'float', 'null' => true, 'default' => '0.0000', 'length' => '9,4', 'collate' => null, 'comment' => ''),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null, 'collate' => null, 'comment' => ''),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null, 'collate' => null, 'comment' => ''),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);

	public $transactions = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary', 'collate' => null, 'comment' => ''),
		'title' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'token' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'foreign_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'model' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'type' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => 'paypal, ...', 'charset' => 'utf8'),
		'transaction_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'transaction_type' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'note' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'amount' => array('type' => 'float', 'null' => false, 'default' => '0.00', 'length' => '9,2', 'collate' => null, 'comment' => ''),
		'fee_amount' => array('type' => 'float', 'null' => false, 'default' => '0.00', 'length' => '9,2', 'collate' => null, 'comment' => ''),
		'tax_amount' => array('type' => 'float', 'null' => false, 'default' => '0.00', 'length' => '9,2', 'collate' => null, 'comment' => ''),
		'currency_code' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 3, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'payment_type' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'payment_status' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'pending_reason' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'reason_code' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'order_time' => array('type' => 'datetime', 'null' => false, 'default' => null, 'collate' => null, 'comment' => ''),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null, 'collate' => null, 'comment' => ''),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);
}
