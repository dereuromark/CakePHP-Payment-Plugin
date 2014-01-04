<?php
/* Discount Fixture generated on: 2011-05-26 02:05:01 : 1306371121 */
class DiscountFixture extends CakeTestFixture {

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 60, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'factor' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2, 'comment' => 'percent'),
		'amount' => array('type' => 'float', 'null' => false, 'default' => '0.00', 'length' => '6,2'),
		'unlimited' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'min' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 3),
		'valid_from' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'valid_until' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'model' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'foreign_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);

	public $records = array(
		array(
			'id' => 1,
			'name' => 'Lorem ipsum dolor sit amet',
			'factor' => 1,
			'amount' => 1,
			'unlimited' => 1,
			'min' => 1,
			'valid_from' => '2011-05-26 02:52:01',
			'valid_until' => '2011-05-26 02:52:01',
			'model' => 'Lorem ipsum dolor ',
			'foreign_id' => 1,
			'created' => '2011-05-26 02:52:01',
			'modified' => '2011-05-26 02:52:01'
		),
	);
}
