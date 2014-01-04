<?php
/* PrepaidAccount Fixture generated on: 2011-07-30 00:07:18 : 1311978438 */
class PrepaidAccountFixture extends CakeTestFixture {

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'user_id' => array('type' => 'string', 'null' => false, 'length' => 36, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'amount' => array('type' => 'float', 'null' => true, 'default' => '0.0000', 'length' => '9,4'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array()
	);

	public $records = array(
		array(
			'id' => 1,
			'user_id' => 'Lorem ipsum dolor sit amet',
			'amount' => 1,
			'created' => '2011-07-30 00:27:18',
			'modified' => '2011-07-30 00:27:18'
		),
	);
}
