<?php
/* BitcoinAddress Fixture generated on: 2011-07-16 02:07:28 : 1310777368 */
class BitcoinAddressFixture extends CakeTestFixture {

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10),
		'model' => array('type' => 'string', 'null' => false, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'foreign_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10),
		'address' => array('type' => 'string', 'null' => false, 'length' => 34, 'collate' => 'utf8_unicode_ci', 'comment' => 'own address', 'charset' => 'utf8'),
		'amount' => array('type' => 'float', 'null' => false, 'default' => '0.0000', 'length' => '10,4', 'comment' => 'expected amount in bitcoins'),
		'confirmations' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10),
		'details' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_unicode_ci', 'comment' => 'can transport some information', 'charset' => 'utf8'),
		'status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2, 'comment' => '0=freeUnusedPoolAddresses,1=inUse,2='),
		'refund_address' => array('type' => 'string', 'null' => false, 'length' => 34, 'collate' => 'utf8_unicode_ci', 'comment' => 'only necessary if refund has to be granted', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);

	public $records = array(
		array(
			'id' => 1,
			'user_id' => 1,
			'model' => 'Lorem ipsum dolor sit amet',
			'foreign_id' => 1,
			'address' => 'Lorem ipsum dolor sit amet',
			'amount' => 1,
			'confirmations' => 1,
			'details' => 'Lorem ipsum dolor sit amet',
			'status' => 1,
			'refund_address' => 'Lorem ipsum dolor sit amet',
			'created' => '2011-07-16 02:49:28',
			'modified' => '2011-07-16 02:49:28'
		),
	);
}
