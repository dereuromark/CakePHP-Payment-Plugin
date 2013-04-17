<?php
/**
 * class for handling debit/lastschrift
 *
 * Copyright (c) 2011 Payment Network AG
 *
 * $Date: 2011-11-30 10:40:45 +0100 (Wed, 30 Nov 2011) $
 * @version SofortLib 1.3.0  $Id: sofortLib_debit.inc.php 2418 2011-11-30 09:40:45Z dehn $
 * @author Payment Network AG http://www.payment-network.com (integration@sofort.com)
 *
 */
class SofortLib_Debit extends SofortLib_Abstract
{
	var $response = array();

	var $parameters = array();


	function SofortLib_Debit($apikey='') {
		list($userid, $projectId, $apikey) = explode(':', $apikey);
		$apiUrl = (getenv('debitApiUrl') != '') ? getenv('debitApiUrl') : 'https://www.sofortlastschrift.de/payment/debitpay/xml';
		$this->SofortLib($userid, $apikey, $apiUrl);
		$this->setProjectId($projectId);
		$this->setDate(); //set date to today
	}


	/**
	 * send this debitpay and get response
	 * @return boolean true if transaction was accepted, false otherwise
	 */
	function sendRequest() {
		parent::sendRequest();

		return $this->isError() === false;
	}


	/**
	 * Project id
	 * Id of your Sofortlastschrift project
	 *
	 * @param int $id project id
	 * @return SofortLib_Debit $this
	 */
	function setProjectId($id) {
		$this->parameters['project_id'] = $id;

		return $this;
	}


	/**
	 * sets date of this debitpay
	 * automatically called in constructor and set to today
	 *
	 * @param String $date date in Format Y-m-d (eg: 2011-01-20), default: today
	 * @return SofortLib_Debit $this
	 */
	function setDate($date = '') {
		if(empty($date)) {
			$date = date('Y-m-d');
		}
		$this->parameters['date'] = $date;

		return $this;
	}


	/**
	 * set data of account
	 *
	 * @param String $bankCode bank code of bank
	 * @param String $accountNumber account number
	 * @param String $holder Name/Holder of this account
	 * @return SofortLib_Debit $this
	 */
	function setSenderAccount($bankCode, $accountNumber, $holder) {
		$this->parameters['sl']['sender'] = array('holder' => $holder, 'account_number' => $accountNumber, 'bank_code' => $bankCode);
		return $this;
	}


	/**
	 * set data of account
	 *
	 * @param String $accountNumber account number
	 * @return SofortLib_Debit $this
	 */
	function setSenderAccountNumber($accountNumber) {
		$this->parameters['sl']['sender']['account_number'] = $accountNumber;
		return $this;
	}


	/**
	 * set data of account
	 *
	 * @param String $bankCode bank code of bank
	 * @return SofortLib_Debit $this
	 */
	function setSenderBankCode($bankCode) {
		$this->parameters['sl']['sender']['bank_code'] = $bankCode;
		return $this;
	}


	/**
	 * set data of account
	 *
	 * @param String $name Name/Holder of this account
	 * @return SofortLib_Debit $this
	 */
	function setSenderHolder($name) {
		$this->parameters['sl']['sender']['holder'] = $name;
		return $this;
	}


	/**
	 * set amount of this transfer
	 * needs to be a decimal e.g. 2.24
	 *
	 * @param float $amount amount of this transfer
	 */
	function setAmount($amount) {
		$this->parameters['sl']['amount'] = $amount;
		return $this;
	}


	/**
	 * add another user-variable to this transfer
	 * this variable could be a customer-number or similar and will
	 * help you identify this transfer later
	 *
	 * @param String $userVariable max 255 characters
	 * @return SofortLib_Debit $this
	 */
	function addUserVariable($userVariable) {
		$this->parameters['sl']['user_variables'][] = $userVariable;
		return $this;
	}


	/**
	 * adds another reason (verwendungszweck)
	 * only first two can be used, 27 characters each
	 *
	 * @param $reason string
	 * @return SofortLib_Debit $this
	 */
	function addReason($reason) {
		$this->parameters['sl']['reasons'][] = $reason;
		return $this;
	}


	/**
	 * set reason (verwendugszweck) of this transfer
	 * two lines possible, 27 characters each
	 *
	 * @param $reason1
	 * @param $reason2
	 * @return SofortLib_Debit $this
	 */
	function setReason($reason1, $reason2 = '') {
		$this->parameters['sl']['reasons'][0] = $reason1;
		$this->parameters['sl']['reasons'][1] = $reason2;
		return $this;
	}


	/**
	 * get Transaction-Id of this Transfer
	 * @return String transaction-id
	 */
	function getTransactionId() {
		return $this->response['transaction'];
	}


	function getReason($i = 0) {
		return $this->response['reasons'][$i];
	}


	function getAmount() {
		return $this->response['amount'];
	}


	function getUserVariable($i = 0) {
		return $this->response['user_variables'][$i];
	}


	function getDate() {
		return $this->response['date'];
	}


	function isError ($paymentMethod = 'all', $message = ''){
		return parent::isError($paymentMethod, $message);
	}


	function getError ($paymentMethod = 'all', $message = '') {
		return parent::getError($paymentMethod, $message);
	}


	/**
	 * generate XML message
	 * @return string
	 */
	function toXml() {
		$msg = '<?xml version="1.0" encoding="UTF-8"?>';
		$msg .= $this->_arrayToXml($this->parameters, 'debitpay');

		return $msg;
	}


	/**
	 * Parser for response from server
	 * this callback will be called for every closing xml-tag
	 * @private
	 */
	function onParseTag($data, $tag){
		switch($tag) {
			case 'project_id':
			case 'date':
			case 'transaction':
			case 'amount':
				$this->response[$tag] = $data;
				break;
			case 'holder':
			case 'account_number':
			case 'bank_code':
			case 'bic':
			case 'iban':
			case 'country_code':
			case 'code':
			case 'message':
				if($this->_getParentTag() == 'sender' || $this->_getParentTag() == 'error') {
					$this->response[$this->_getParentTag()][$tag] = $data;
				}
				break;
			case 'user_variable':
			case 'reason':
				if($this->_getParentTag() == 'user_variables' || $this->_getParentTag() == 'reasons') {
					$this->response[$this->_getParentTag()][] = $data;
				}
				break;
			default:
				break;
		}
	}


	function getResponse() {
		return $this->response;
	}
}