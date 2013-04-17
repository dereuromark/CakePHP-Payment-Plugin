<?php
/**
 * This class encapsulates retrieval of listed banks of the Netherlands
 *
 * Copyright (c) 2011 Payment Network AG
 *
 * $Date$
 * @version SofortLib 1.3.0  $Id$
 * @author Payment Network AG http://www.payment-network.com (integration@sofort.com)
 *
 */
class SofortLib_iDeal_Banks extends SofortLib_Abstract {
	
	var $banks = '';
	var $count = 0;
	
	function SofortLib_iDeal_Banks($configurationKey, $apiUrl) {
		list($userid, $projectId, $apikey) = explode(':', $configurationKey);
		$this->SofortLib($userid, $apikey, $apiUrl.'/banks');
	}
	
	function toXml(){}
	
	function onParseTag($data, $tag) {
		switch($tag) {
			case 'code':
			case 'name':
					$this->banks[$this->count][$tag] = $data;
					break;
			case 'bank':
					$this->count++;
				break;
			default:
			break;
		}
			
	}
	
	function getBanks() {
		return $this->banks;
	}
	
}