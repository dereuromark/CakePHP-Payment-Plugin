<?php
/**
 * Instance of this class handles the callback of Payment Network to notify about a status change
 *
 * In rare cases notifications might be doubled or even wrong alltogether (if
 * send by a malicious user). So don't use this to change your status but instead
 * use the transaction id to query the webservice for detailed data (SofortLib_TransactionData)
 *
 * eg: $notificationObj = new SofortLib_Notification();
 *
 * $transactionId = $notificationObj->getNotification();
 *
 * Copyright (c) 2011 Payment Network AG
 *
 * $Date: 2011-11-30 10:40:45 +0100 (Wed, 30 Nov 2011) $
 * @version SofortLib 1.3.0  $Id: sofortLib_notification.inc.php 2418 2011-11-30 09:40:45Z dehn $
 * @author Payment Network AG http://www.payment-network.com (integration@sofort.com)
 *
 */
class SofortLib_Notification extends SofortLib_Abstract {
	var $transactionId = '';
	var $time;
	var $parameters = array();


	/**
	 * creates a new notification object for receiving notifications
	 */
	function SofortLib_Notification() {
		$this->SofortLib('', '', '');
	}


	/**
	 * reads the input and tries to read the transaction id
	 *
	 * @return array transactionid=>status
	 */
	function getNotification($source = 'php://input') {
		$data = file_get_contents($source);

		//we don't really need a huge parser, simply extract the transaction-id
		if(!preg_match('#<transaction>([0-9a-z-]+)</transaction>#i', $data, $matches)) {
			$this->log(__CLASS__ . ' <- '. $data);
			$this->errors['error']['message'] = 'could not parse message';
			return false;
		}
		$this->transactionId = $matches[1];

		$this->log(__CLASS__ . ' <- '. $this->formatXmlString($data));


		preg_match('#<time>(.+)</time>#i', $data, $matches);
		if(isset($matches[1]))
			$this->time = $matches[1];

//		$this->_initParser();
//		$this->_parse($data);

		return $this->transactionId;
	}


	////not in use!!////
	/**
	 * Parser for response from server
	 * this callback will be called for every closing xml-tag
	 * @private
	 *//*
	function onParseTag($data, $tag){
		switch($tag) {
			case 'transaction':
				$this->transactionId = $data;
				break;
			case 'time':
				$this->time = $data;
				break;
			break;
		}
	}*/


	function sendRequest() {
		trigger_error('sendRequest() not possible in this case', E_USER_NOTICE);
	}


	function getTime() {
		return $this->time;
	}


	function getTransactionId() {
		return $this->transactionId;
	}
}