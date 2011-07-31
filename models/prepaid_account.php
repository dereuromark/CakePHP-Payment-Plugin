<?php

if (!defined('CLASS_USER')) {
	define('CLASS_USER', 'User');
}

/**
 * PrepaidAccount for users
 * callbacks for user model:
 * - afterPrepaidChange($prepaidAccountId, $before, $after)
 * features
 * - complete transaction log
 * 
 * 2011-07-30 ms
 */
class PrepaidAccount extends PaymentAppModel {
	var $name = 'PrepaidAccount';
	var $displayField = 'amount';

	var $actsAs = array('Tools.Logable'=>array('change'=>'full'));
	var $order = array();

	var $validate = array(
		'user_id' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'valErrMandatoryField',
				'last' => true
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'valErrRecordNameExists',
			),
		),
		'amount' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'valErrMandatoryField',
			),
		),
	);


	var $belongsTo = array(
		'User' => array(
			'className' => CLASS_USER,
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => array('id', 'username'),
			'order' => ''
		)
	);
	
	
	function availableMoney($uid) {
		$res = $this->find('first', array('conditions'=>array('user_id'=>$uid)));
		if (!$res) {
			return 0;
		}
		return $res[$this->alias]['amount'];
	}
	
	/**
	 * //TODO: atomic query for more security?
	 * use the money in the prepaid account
	 * @return the amount the user has paid with (0 if not possible)
	 * 2011-07-30 ms
	 */
	function pay($uid, $amount = null) {
		$res = $this->find('first', array('conditions'=>array('user_id'=>$uid)));
		if (!$res || $res[$this->alias]['amount'] <= 0) {
			return 0;
		}
		$money = $res[$this->alias]['amount'];
		
		if ($amount === null || $amount > $money) {
			$amount = $money;
		}
		
		$this->id = $res[$this->alias]['id'];
		$this->enableLog(false);
		$this->saveField('amount', $money - $amount);
		$this->enableLog(true);
		$this->customLog('paid ('.number_format($amount, 2).')', $this->id);
		return (float)$amount;
	}
	
	/**
	 * put money into the prepaid account
	 * @return boolean $success
	 * 2011-07-30 ms
	 */
	function deposit($uid, $amount) {
		$account = $this->account($uid);
		if (!$this->updateAll(array('amount' => $amount), array('user_id'=>$uid))) {
			return false;
		}
		$this->customLog('deposited ('.number_format($amount, 2).')', $account[$this->alias]['id']);
		return true;
	}
	
	/**
	 * get the current account
	 * if it does not exist it creates one!
	 * @return array $account
	 * 2011-07-30 ms
	 */
	function account($uid) {
		if ($account = $this->find('first', array('conditions'=>array('user_id'=>$uid)))) {
			return $account;
		}
		# create
		$data = array(
			'user_id' => $uid,
		);
		$this->create();
		$this->enableLog(false);
		$res = $this->save($data, false);
		$this->enableLog(true);
		if ($res) {
			$res[$this->alias]['id'] = $this->id;
		}
		return $res; 
	}
	
}
