<?php
/**
 * Instance of this class handles the callback of Payment Network to notify about a status change, the classic way to do so
 *
 * Copyright (c) 2011 Payment Network AG
 *
 * $Date: 2011-11-30 10:40:45 +0100 (Wed, 30 Nov 2011) $
 * @version SofortLib 1.2.0  $Id: sofortLib_classic_notification.inc.php 2418 2011-11-30 09:40:45Z dehn $
 * @author Payment Network AG http://www.payment-network.com (integration@sofort.com)
 *
 */
class SofortLib_ClassicNotification
{
	var $params = array();
	var $password;
	var $userid;
	var $projectid;
	var $hashfunction;

	var $hashCheck = false;

	function SofortLib_ClassicNotification($userid, $projectid, $password, $hashfunction='sha1') {
		$this->password = $password;
		$this->userid = $userid;
		$this->projectid = $projectid;
		$this->hashfunction = strtolower($hashfunction);
	}


	function getNotification($request='') {
		if($request == '')
			$request = $_POST;

		if(array_key_exists('international_transaction', $request)) {
		//standard notification
			$fields = array(
				'transaction', 'user_id', 'project_id',
				'sender_holder', 'sender_account_number', 'sender_bank_code', 'sender_bank_name', 'sender_bank_bic', 'sender_iban', 'sender_country_id',
				'recipient_holder',	'recipient_account_number', 'recipient_bank_code', 'recipient_bank_name', 'recipient_bank_bic',	'recipient_iban', 'recipient_country_id',
				'international_transaction', 'amount', 'currency_id', 'reason_1', 'reason_2', 'security_criteria',
				'user_variable_0',	'user_variable_1', 'user_variable_2', 'user_variable_3', 'user_variable_4',	'user_variable_5',
				'created'
			);
		} else {
			//ideal
			$fields = array(
				'transaction', 'user_id', 'project_id',
				'sender_holder', 'sender_account_number', 'sender_bank_name', 'sender_bank_bic', 'sender_iban', 'sender_country_id',
				'recipient_holder',	'recipient_account_number', 'recipient_bank_code', 'recipient_bank_name', 'recipient_bank_bic',	'recipient_iban', 'recipient_country_id',
				'amount', 'currency_id', 'reason_1', 'reason_2',
				'user_variable_0',	'user_variable_1', 'user_variable_2', 'user_variable_3', 'user_variable_4',	'user_variable_5',
				'created'
			);
		}
		//http-notification with status
		if(array_key_exists('status', $request) && !empty($request['status'])) {
			array_push($fields, 'status', 'status_modified');
		}

		$this->params = array();
		foreach($fields as $key) {
			# bugfix to prevent undefined index notices to occurr - 2011-12-29 ms
			$this->params[$key] = isset($request[$key]) ? $request[$key] : null;
		}

		$this->params['project_password'] = $this->password;

		$validationhash = $this->getHashHexValue(implode('|', $this->params), $this->hashfunction);
		# bugfix to prevent undefined index notices to occurr - 2011-12-29 ms
		$messagehash = isset($request['hash']) ? $request['hash'] : null;

		$this->hashCheck = ($validationhash === $messagehash);

		return $this;
	}


	/**
	 * @param string $data string to be hashed
	 * @return string the hash
	 */
	function getHashHexValue($data, $hashfunction='sha1') {
		if($hashfunction == 'sha1')
			return sha1($data);
		if($hashfunction == 'md5')
			return md5($data);
		//mcrypt installed?
		if(function_exists('hash') && in_array($hashfunction, hash_algos()))
			return hash($hashfunction, $data);

		return false;
	}


	function isError() {
		if(!$this->hashCheck)
			return true;

		return false;
	}


	function getError() {
		if(!$this->hashCheck) {
			return 'hash-check failed';
		}
		return false;
	}


	function getTransaction() {
		return $this->params['transaction'];
	}


	function getAmount() {
		return $this->params['amount'];
	}


	function getUserVariable($i=0) {
		return $this->params['user_variable_'.$i];
	}


	function getCurrency() {
		return $this->params['currency_id'];
	}


	function getTime() {
		return $this->params['created'];
	}

	function getStatus() {
		return $this->params['status'];
	}

	function getStatusReason() {
		switch ($this->getStatus()) {
			case 'received':
				return 'credited';
			case 'pending':
				return 'not_credited_yet';
			case 'loss':
				return 'loss';
		}

		return false;
	}
}