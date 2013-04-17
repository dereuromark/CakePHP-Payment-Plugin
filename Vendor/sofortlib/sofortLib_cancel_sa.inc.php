<?php

/**
 * The base class for cancelling SofortDauerauftrag
 *
 * Copyright (c) 2011 Payment Network AG
 *
 * $Date: 2011-11-30 10:40:45 +0100 (Wed, 30 Nov 2011) $
 * @version SofortLib 1.3.0  $Id: sofortLib_cancel_sa.inc.php 2418 2011-11-30 09:40:45Z dehn $
 * @author Payment Network AG http://www.payment-network.com (integration@sofort.com)
 *
 */

class SofortLib_CancelSa extends SofortLib_Abstract {
	
	var $parameters;
	var $file;
	
	var $cancelUrl = '';


	/**
	 * create new cancel object
	 *
	 * @param String $apikey your API-key
	 */
	function SofortLib_CancelSa($apikey='') {
		list($userid, $projectId, $apikey) = explode(':', $apikey);
		$apiUrl = (getenv('sofortApiUrl') != '') ? getenv('sofortApiUrl') : 'https://api.sofort.com/api/xml';
		$this->SofortLib($userid, $apikey, $apiUrl);
	}


	/**
	 * generate XML message
	 * @return string
	 */
	function toXml() {
		$msg = '<?xml version="1.0" encoding="UTF-8"?>';
		$msg .= $this->_arrayToXml($this->parameters, 'cancel_sa');

		return $msg;
	}
	
	/**
	 * 
	 * remove SofortDauerauftrag
	 * @param String $transaction Transaction ID
	 * @return SofortLib_CancelSa
	 */
	function removeSofortDauerauftrag($transaction) {
		if(empty($transaction) && array_key_exists('transaction', $this->parameters)) {
			$transaction = $this->parameters['transaction'];
		}

		if(!empty($transaction)) {
			$this->parameters = NULL;
			$this->parameters['transaction'] = $transaction;
		}

		return $this;
	}
	
	
	/**
	 * Set the transaction you want to confirm/change
	 * @param String $arg Transaction Id
	 * @return SofortLib_CancelSa
	 */
	function setTransaction($arg) {
		$this->parameters['transaction'] = $arg;
		return $this;
	}
	
	
	function getCancelUrl() {
		return $this->cancelUrl;
	}
	
	/**
	 * Parser for response from server
	 * this callback will be called for every closing xml-tag
	 * @private
	 */
	function onParseTag($data, $tag){
			switch($tag) {
			case 'cancel_url':
				$this->cancelUrl = $data;
				break;
			default:
			break;
		}
	}
	
}