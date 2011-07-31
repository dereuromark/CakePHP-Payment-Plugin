<?php
class BitcoinAddress extends PaymentAppModel {
	var $name = 'BitcoinAddress';
	var $displayField = 'address';

	var $actsAs = array();
	var $order = array('BitcoinAddress.created'=>'DESC');


	var $validate = array(
		'account' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'valErrMandatoryField',
			),
		),
		'address' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'valErrMandatoryField',
				'last' => true
			),
		),
		'amount_received' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'valErrMandatoryField',
			),
		),
		'amount_send' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'valErrMandatoryField',
			),
		),
	);



	var $hasMany = array(
		'BitcoinTransaction' => array(
			'className' => 'Payment.BitcoinTransaction',
			'foreignKey' => 'address_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
		),
	);
	

	/**
	 * update transaction count etc for a given address
	 * 2011-07-20 ms
	 */
	function updateAddress($address) {

	}


	/**
	 * from a shell cronjob it can make sure there are always unused address available
	 * neccessary for security reasons (backuped wallets cannot transfer money anymore otherwise)
	 * and for a quick response time (takes time to create on on the fly)
	 * TODO: also clear old unused ones for further use
	 * 2011-07-20 ms
	 */
	function guaranteeFreeAddresses($amount = 5) {

	}
	
	/**
	 * use this address in the future if transaction was cancelled and address is still unused
	 * 2011-07-26 ms
	 */
	function resetIfUnused($id) {
		$res = $this->get($id);
		if (!$res) {
			return false;
		}
		if ($res[$this->alias]['amount_received'] > 0 || $res[$this->alias]['amount_sent'] > 0) {
			return false;
		}
		# necessary? we could find unused addresses by "NOT IN" as well
		//return $this->save();
		return true;
	}

	/**
	 * complete update
	 * 2011-07-20 ms
	 */
	function update() {


		return true;
	}

}
