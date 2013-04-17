<?php
/* generated on: 2011-11-20 20:32:19 : 1321817539 */
class PaymentSchema extends CakeSchema {

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $bitcoin_addresses = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary', 'collate' => NULL, 'comment' => ''),
		'account' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'address' => array('type' => 'string', 'null' => false, 'length' => 34, 'collate' => 'utf8_unicode_ci', 'comment' => 'bitcoin address', 'charset' => 'utf8'),
		'amount_received' => array('type' => 'float', 'null' => true, 'default' => NULL, 'length' => '10,4', 'collate' => NULL, 'comment' => ''),
		'amount_sent' => array('type' => 'float', 'null' => true, 'default' => NULL, 'length' => '10,4', 'collate' => NULL, 'comment' => ''),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'collate' => NULL, 'comment' => ''),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'collate' => NULL, 'comment' => ''),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);
	public $bitcoin_transactions = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary', 'collate' => NULL, 'comment' => ''),
		'address_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10, 'collate' => NULL, 'comment' => ''),
		'model' => array('type' => 'string', 'null' => false, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'foreign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'amount' => array('type' => 'float', 'null' => false, 'default' => '0.0000', 'length' => '10,4', 'collate' => NULL, 'comment' => ''),
		'amount_expected' => array('type' => 'float', 'null' => false, 'default' => '0.0000', 'length' => '10,4', 'collate' => NULL, 'comment' => 'expected amount in bitcoins'),
		'confirmations' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10, 'collate' => NULL, 'comment' => ''),
		'details' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_unicode_ci', 'comment' => 'can transport some information', 'charset' => 'utf8'),
		'payment_fee' => array('type' => 'float', 'null' => true, 'default' => NULL, 'length' => '10,4', 'collate' => NULL, 'comment' => ''),
		'status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => NULL, 'comment' => '0=pending,1=finished'),
		'refund_address' => array('type' => 'string', 'null' => false, 'length' => 34, 'collate' => 'utf8_unicode_ci', 'comment' => 'only necessary if refund has to be granted', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'collate' => NULL, 'comment' => ''),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'collate' => NULL, 'comment' => ''),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);
	public $discount_codes = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary', 'collate' => NULL, 'comment' => ''),
		'discount_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10, 'collate' => NULL, 'comment' => ''),
		'code' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 20, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'used' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'collate' => NULL, 'comment' => ''),
		'model' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'foreign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'collate' => NULL, 'comment' => ''),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'collate' => NULL, 'comment' => ''),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);
	public $discounts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary', 'collate' => NULL, 'comment' => ''),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'factor' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => NULL, 'comment' => 'percent'),
		'amount' => array('type' => 'float', 'null' => false, 'default' => '0.00', 'length' => '6,2', 'collate' => NULL, 'comment' => ''),
		'unlimited' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'collate' => NULL, 'comment' => ''),
		'min' => array('type' => 'integer', 'null' => false, 'default' => '0', 'collate' => NULL, 'comment' => ''),
		'valid_from' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'collate' => NULL, 'comment' => ''),
		'valid_until' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'collate' => NULL, 'comment' => ''),
		'model' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 20, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'foreign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'details' => array('type' => 'text', 'null' => false, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'comment' => 'json encoded!', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'collate' => NULL, 'comment' => ''),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'collate' => NULL, 'comment' => ''),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);
	public $payment_methods = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary', 'collate' => NULL, 'comment' => ''),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'collate' => NULL, 'comment' => ''),
		'set_rate' => array('type' => 'float', 'null' => false, 'default' => '0.00', 'length' => '6,2', 'collate' => NULL, 'comment' => 'fixed charge'),
		'rel_rate' => array('type' => 'float', 'null' => false, 'default' => '0.0000', 'length' => '5,4', 'collate' => NULL, 'comment' => 'relative to amount'),
		'sort' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10, 'collate' => NULL, 'comment' => ''),
		'description' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'hint' => array('type' => 'text', 'null' => false, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'details' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'duration' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 50, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'name' => array('type' => 'string', 'null' => false, 'length' => 250, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'alias' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'hook' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => 'what function is supposed to be triggered', 'charset' => 'utf8'),
		'url' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'comment' => 'homepage', 'charset' => 'utf8'),
		'votes' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10, 'collate' => NULL, 'comment' => ''),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'collate' => NULL, 'comment' => ''),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'collate' => NULL, 'comment' => ''),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);
	public $prepaid_accounts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary', 'collate' => NULL, 'comment' => ''),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'amount' => array('type' => 'float', 'null' => true, 'default' => '0.0000', 'length' => '9,4', 'collate' => NULL, 'comment' => ''),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'collate' => NULL, 'comment' => ''),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'collate' => NULL, 'comment' => ''),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);
	public $transactions = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary', 'collate' => NULL, 'comment' => ''),
		'title' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'token' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'foreign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'model' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'type' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => 'paypal, ...', 'charset' => 'utf8'),
		'transaction_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'transaction_type' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'note' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'amount' => array('type' => 'float', 'null' => false, 'default' => '0.00', 'length' => '9,2', 'collate' => NULL, 'comment' => ''),
		'fee_amount' => array('type' => 'float', 'null' => false, 'default' => '0.00', 'length' => '9,2', 'collate' => NULL, 'comment' => ''),
		'tax_amount' => array('type' => 'float', 'null' => false, 'default' => '0.00', 'length' => '9,2', 'collate' => NULL, 'comment' => ''),
		'currency_code' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 3, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'payment_type' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'payment_status' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'pending_reason' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'reason_code' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'charset' => 'utf8'),
		'order_time' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'collate' => NULL, 'comment' => ''),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'collate' => NULL, 'comment' => ''),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);
}
