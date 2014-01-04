<?php
/* DiscountCode Fixture generated on: 2011-05-26 02:05:57 : 1306371057 */
class DiscountCodeFixture extends CakeTestFixture {

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'discount_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10),
		'code' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'used' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);

	public $records = array(
		array(
			'id' => 1,
			'discount_id' => 1,
			'code' => 'Lorem ipsum dolor ',
			'used' => 1,
			'created' => '2011-05-26 02:50:56',
			'modified' => '2011-05-26 02:50:56'
		),
	);
}
