<?php
/**
 * class for refund/rueckbuchung
 *
 * Copyright (c) 2011 Payment Network AG
 *
 * $Date: 2011-11-30 10:40:45 +0100 (Wed, 30 Nov 2011) $
 * @version SofortLib 1.3.0  $Id: sofortLib_refund.inc.php 2418 2011-11-30 09:40:45Z dehn $
 * @author Payment Network AG http://www.payment-network.com (integration@sofort.com)
 *
 */
class SofortLib_Refund extends SofortLib_Abstract
{
	var $response = array(array());

	function SofortLib_Refund($apikey='') {
		list($userid, $projectId, $apikey) = explode(':', $apikey);
		$apiUrl = (getenv('refundApiUrl') != '') ? getenv('refundApiUrl') : 'https://www.sofortueberweisung.de/payment/refunds';
		$this->SofortLib($userid, $apikey, $apiUrl);
	}


	/**
	 * send this message and get response
	 *
	 * @return array transactionid=>status
	 */
	function sendRequest() {
		parent::sendRequest();

		return $this->getStatusArray();
	}

	/**
	 * generate XML message
	 * @return string
	 */
	function toXml() {
		$msg = '<?xml version="1.0" encoding="UTF-8"?>';
		$msg .= $this->_arrayToXml($this->parameters, 'refunds');
		return $msg;
	}


	/**
	 * add a new refund to this message
	 *
	 * @param string $transaction transaction id of transfer you want to refund
	 * @param float $amount amount of money to refund, less or equal to amount of original transfer
	 * @param string $comment comment that will be displayed in  admin-menu later
	 * @return SofortLib_Refund $this
	 */
	function addRefund($transaction, $amount, $comment = '') {
		$this->parameters[] = array('transaction' => $transaction, 'amount' => $amount, 'comment' => $comment);
		return $this;
	}


	/**
	 * set data of account
	 *
	 * @param string $bank_code bank code of bank
	 * @param string $account_number account number
	 * @param string $holder Name/Holder of this account
	 * @return SofortLib_Multipay $this
	 */
	function setSenderAccount($bank_code, $account_number, $holder='') {
		$this->parameters['sender'] = array('holder' => $holder, 'account_number' => $account_number, 'bank_code' => $bank_code);
		return $this;
	}


	function setTitle($arg) {
		$this->parameters['title'] = $arg;
		return $this;
	}


	/**
	 * Parser for response from server
	 * this callback will be called for every closing xml-tag
	 * @private
	 */
	function onParseTag($data, $tag){
		switch($tag) {
			case 'transaction':
			case 'amount':
			case 'comment':
			case 'status':
				if($this->_getParentTag() == 'refund') {
					$i = count($this->response)-1;
					$this->response[$i][$tag] = $data;
				}
				break;
			case 'code':
			case 'message':
				if($this->_getParentTag() == 'error') {
					$i = count($this->response)-1;
					$this->response[$i][$this->_getParentTag()][$tag] = $data;
				}
				break;
			case 'refund':
				if($this->_getParentTag() == 'refunds') {
					array_push($this->response, array());
				}
				break;
			case 'refunds':
				array_pop($this->response);
				break;
			default:
				break;
		}
	}


	function getTransactionId($i = 0) {
		return $this->response[$i]['transaction'];
	}


	function getAmount($i = 0) {
		return $this->response[$i]['amount'];
	}


	/**
	 * @deprecated - use getRefundError() instead
	 * @return ALWAYS false
	 */
	function getError($paymentMethod = 'all', $message = '') {
		return false;
	}


	function getRefundError($i = 0) {
		return parent::getError('all', $this->response[$i]);
	}


	/**
	 * @deprecated - use isRefundError() instead
	 * @return ALWAYS false
	 */
	function isError($paymentMethod = 'all', $message = '') {
		return false;
	}


	function isRefundError($i = 0) {
		return $this->response[$i]['status'] == 'error';
	}


	/* function doesnt exist anymore
	function getErrorCode($i = 0) {
		return parent::getErrorCode($this->response[$i]);
	}
	*/


	function getAsArray() {
		return $this->response;
	}


	function getStatusArray() {
		$ret = array();
		foreach($this->response as $transaction) {
			if($transaction['status'] == 'ok') {
				$ret[$transaction['transaction']] = 'ok';
			} else {
				$ret[$transaction['transaction']] = parent::getError($transaction);
			}
		}

		return $ret;
	}
}