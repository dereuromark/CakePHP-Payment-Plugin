<?php

define('VERSION_CLASSIC','1.2.0');

require_once 'sofortLib_http.inc.php';
require_once 'sofortLib_sofortueberweisung_classic.php';
require_once 'sofortLib_Logger.inc.php';
require_once 'sofortLib_ideal_banks.inc.php';
/**
 * iDeal_Classic extends Sofortueberweisung_Classic, implementing payment via iDeal
 * Setup a session within iDeal using the classic api
 * You get the so called payment-url after successful configuration
 * Payment is enabled with this url being sent to iDeal
 *
 * eg:
 * $sofort = $sofortLib_iDealClassic = new SofortLib_iDealClassic ($configurationKey, $password, $hashfunction = 'sha1');
 * $sofort->getRelatedBanks(); //get all iDEAL-Banks
 * $sofort->getPaymentUrl(); //returns paymentUrl including (including ...&hash=1234567890&...)
 *
 * Copyright (c) 2011 Payment Network AG
 *
 * $Date: 2011-11-30 10:40:45 +0100 (Wed, 30 Nov 2011) $
 * @version SofortLib 1.3.0  $Id: sofortLib_ideal_classic.php 2418 2011-11-30 09:40:45Z dehn $
 * @author Payment Network AG http://www.payment-network.com (integration@sofort.com)
 *
 */
class SofortLib_iDealClassic extends SofortLib_SofortueberweisungClassic
{
	var $http = '';
	
	var $sofortLib = null;
	
	var $apiUrl = '';
	var $apiKey = '';
	var $relatedBanks = array();
	
	var $paymentUrl = '';
	
	/**
	 *
	 * Enter description here ...
	 * @param int $userid
	 * @param int $projectid
	 * @param string $apiKey
	 * @param string $password
	 * @param string $hashfunction (sha1, sha256, sha512) depending on PHP and OS and adjustment in project on pnag-server
	 */
	function SofortLib_iDealClassic($configurationKey, $password, $hashfunction = 'sha1') {
		
		$apiUrl = (getenv('idealApiUrl') != '') ? getenv('idealApiUrl') : 'https://www.sofort.com/payment/ideal';
		$this->apiUrl = $apiUrl;
		list($userid, $projectid, $apiKey) = explode(':', $configurationKey);
		$this->sofortLib = new SofortLib_iDeal_Banks($configurationKey, $this->apiUrl);
		parent::SofortLib_SofortueberweisungClassic($userid, $projectid, $password, $hashfunction);
		$this->apiKey = $apiKey;
	}


	/**
	 *
	 * Set sender's country id
	 * @param unknown_type $senderCountryId
	 * @return instance
	 */
	function setSenderCountryId($senderCountryId = 'NL') {
		$this->params['sender_country_id'] = $senderCountryId;
	}


	/**
	 *
	 * Set sender's bank code
	 * @param string $senderBankCode
	 * @return instance
	 */
	function setSenderBankCode ($senderBankCode) {
		$this->params['sender_bank_code'] = $senderBankCode;
		return $this;
	}


	/**
	 * No currency id in hash fields, in opposite to sofortueberweisung
	 * @override SofortLib_SofortueberweisungClassic::getPaymentUrl()
	 *
	 */
	function getPaymentUrl() {

		//fields required for hash, notice the missing currency id
		$hashfields = array('user_id', 'project_id','sender_holder','sender_account_number','sender_bank_code','sender_country_id','amount',
		'reason_1','reason_2','user_variable_0','user_variable_1','user_variable_2','user_variable_3','user_variable_4','user_variable_5');

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
		$hash = $this->getHashHexValue( trim( $hashstring ), $this->hashfunction );
		$this->params['hash'] = $hash;

		//create parameter string
		$paramString = '';
		foreach ($this->params as $key => $value) {
			$paramString .= $key.'='.urlencode($value).'&';
		}
		$paramString = substr($paramString, 0, -1); //remove last &
		$this->paymentUrl = $this->apiUrl.'?'.$paramString;
		
//		$this->log($this->paymentUrl);
		
		return $this->paymentUrl;
	}

	
	function getError(){
		return $this->error;
	}


	/**
	 * Get related banks of iDeal
	 * @return array
	 */
	function getRelatedBanks() {
		$this->relatedBanks = $this->sofortLib->sendRequest();
		return $this->relatedBanks->getBanks();
	}
}