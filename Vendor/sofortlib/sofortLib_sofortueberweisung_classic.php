<?php
require_once 'sofortLib_classic_notification.inc.php';
/**
 * Setup a sofortueberweisung.de session using the classic api
 * after the configuration of the configuration you will receive
 * an url and a transaction id, your customer should be redirected to this url
 *
 *
 * Called by the sofortLib.php/sofortLib_ideal_classic.php etc.
 * $sofort->new SofortLib_SofortueberweisungClassic( $userid, $projectid, $password [, $hashfunction='sha1'] );
 * $sofort->set...(); //set params for Hashcalculation
 * $sofort->set...(); //set more params for Hashcalculation
 * $sofort->getPaymentUrl();
 * Notice: sometimes getPaymentUrl() must be overwritten by calling class because of changed hash-params
 *
 *
 * Copyright (c) 2011 Payment Network AG
 *
 * $Date: 2011-11-30 10:40:45 +0100 (Wed, 30 Nov 2011) $
 * @version SofortLib 1.2.0  $Id: sofortLib_sofortueberweisung_classic.php 2418 2011-11-30 09:40:45Z dehn $
 * @author Payment Network AG http://www.payment-network.com (integration@sofort.com)
 *
 */
class SofortLib_SofortueberweisungClassic
{
	var $params = array();
	var $password;
	var $userid;
	var $projectid;
	var $hashfunction;
	
	var $paymentUrl = 'https://www.sofortueberweisung.de/payment/start';


	function SofortLib_SofortueberweisungClassic($userid, $projectid, $password, $hashfunction='sha1', $paymentUrl = 'https://www.sofortueberweisung.de/payment/start') {
		$this->password = $password;
		$this->userid = $userid;
		$this->projectid = $projectid;
		$this->hashfunction = strtolower($hashfunction);
		$this->params['encoding'] = 'UTF-8';

		$this->params['user_id'] = $this->userid;
		$this->params['project_id'] = $this->projectid;
		
		$this->paymentUrl = $paymentUrl;
	}


	function setAmount($arg, $currency = 'EUR') {
		$this->params['amount'] = $arg;
		$this->params['currency_id'] = $currency;
	}


	function setSenderHolder($senderHolder) {
		$this->params['sender_holder'] = $senderHolder;
	}


	function setSenderAccountNumber($senderAccountNumber) {
		$this->params['sender_account_number'] = $senderAccountNumber;
	}

	/**
	 *
	 * Set the reason (Verwendungszweck) for sending money
	 * @param string $arg
	 * @param string $arg2
	 */
	function setReason($arg, $arg2='') {
		$arg = preg_replace('#[^a-zA-Z0-9+-\.,]#', ' ', $arg);
		$arg2 = preg_replace('#[^a-zA-Z0-9+-\.,]#', ' ', $arg2);

		$this->params['reason_1'] = $arg;
		$this->params['reason_2'] = $arg2;

		return $this;
	}


	function addUserVariable($arg) {
		$i = 0;
		while ($i < 6) {
			if(array_key_exists('user_variable_'.$i, $this->params))
				$i++;
			else
				break;
		}
		$this->params['user_variable_'.$i] = $arg;

		return $this;
	}
	
	/**
	 * the customer will be redirected to this url after a successful
	 * transaction, this should be a page where a short confirmation is
	 * displayed
	 *
	 * @param string $arg the url after a successful transaction
	 * @return SofortLib_Multipay
	 */
	function setSuccessUrl($arg) {
		$this->params['user_variable_3'] = $arg;
		return $this;
	}


	/**
	 * the customer will be redirected to this url if he uses the
	 * abort link on the payment form, should redirect him back to
	 * his cart or to the payment selection page
	 *
	 * @param string $arg url for aborting the transaction
	 * @return SofortLib_Multipay
	 */
	function setAbortUrl($arg) {
		$this->params['user_variable_4'] = $arg;
		return $this;
	}	
	
	/**
	 * set the url where you want notification about status changes
	 * being sent to. Use SofortLib_Notification and SofortLib_TransactionData
	 * to further process that notification
	 *
	 * @param string $arg url
	 * @return SofortLib_Multipay
	 */
	function setNotificationUrl($arg) {
		$this->params['user_variable_5'] = $arg;
		return $this;
	}
	
	
	function setVersion($arg) {
		$this->params['interface_version'] = $arg;
		return $this;
	}


	function getPaymentUrl() {
		//fields required for hash
		$hashfields = array(
						'user_id',
						'project_id',
						'sender_holder',
						'sender_account_number',
						'sender_bank_code',
						'sender_country_id',
						'amount','currency_id',
						'reason_1','reason_2',
						'user_variable_0',
						'user_variable_1',
						'user_variable_2',
						'user_variable_3',
						'user_variable_4',
						'user_variable_5',
		);

		//build parameter-string for hashing
		$hashstring = '';
		foreach ($hashfields as $value) {
			if(array_key_exists($value, $this->params)) {
				$hashstring.= $this->params[$value];
			}
			$hashstring .= '|';
		}

		$hashstring .= $this->password;

		//calculate hash
		$hash = $this->getHashHexValue($hashstring, $this->hashfunction);
		$this->params['hash'] = $hash;

		//create parameter string
		$paramString = '';
		foreach ($this->params as $key => $value) {
			$paramString .= $key.'='.urlencode($value).'&';
		}
		$paramString = substr($paramString, 0, -1); //remove last &
		CakeLog::write('debug', $this->paymentUrl.'?'.$paramString);
		return $this->paymentUrl.'?'.$paramString;
	}


	function isError() {
		return false;
	}


	function getError() {
		return false;
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


	/**
	 * @param int [optional] $length length of return value, default 24
	 * @return string
	 */
	function generatePassword($length = 24) {
    	$password = '';

    	//we generate about 5-34 random characters [A-Za-z0-9] in every loop
    	do {
    		$randomBytes = '';
    		$strong = false;
    		if(function_exists('openssl_random_pseudo_bytes')) { //php >= 5.3
				$randomBytes = openssl_random_pseudo_bytes(32, $strong);//get 256bit
    		}
			if(!$strong) { //fallback
				$randomBytes = pack('I*', mt_rand()); //get 32bit (pseudo-random) 
			}

			//convert bytes to base64 and remove special chars
			$password .= preg_replace('#[^A-Za-z0-9]#', '', base64_encode($randomBytes));
    	} while (strlen($password) < $length);
    	
    	return substr($password, 0, $length);
    }
}