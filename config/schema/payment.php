<?php 
/* payment schema generated on: 2011-07-30 03:23:08 : 1311988988*/
class paymentSchema extends CakeSchema {
	var $name = 'payment';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $bitcoin_addresses = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'account' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'address' => array('type' => 'string', 'null' => false, 'length' => 34, 'collate' => 'utf8_unicode_ci', 'comment' => 'bitcoin address', 'charset' => 'utf8'),
		'amount_received' => array('type' => 'float', 'null' => true, 'default' => NULL, 'length' => '10,4'),
		'amount_sent' => array('type' => 'float', 'null' => true, 'default' => NULL, 'length' => '10,4'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);
	var $bitcoin_transactions = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'address_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10),
		'model' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'foreign_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10),
		'amount' => array('type' => 'float', 'null' => false, 'default' => '0.0000', 'length' => '10,4'),
		'amount_expected' => array('type' => 'float', 'null' => false, 'default' => '0.0000', 'length' => '10,4', 'comment' => 'expected amount in bitcoins'),
		'confirmations' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10),
		'details' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'comment' => 'can transport some information', 'charset' => 'utf8'),
		'payment_fee' => array('type' => 'float', 'null' => true, 'default' => NULL, 'length' => '10,4'),
		'status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2, 'comment' => '0=pending,1=finished'),
		'refund_address' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 34, 'collate' => 'utf8_unicode_ci', 'comment' => 'only necessary if refund has to be granted', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);
	var $prepaid_accounts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'amount' => array('type' => 'float', 'null' => true, 'default' => '0.0000', 'length' => '9,4'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);
}
?>