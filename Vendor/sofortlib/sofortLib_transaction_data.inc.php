<?php
/**
 * This class is  for retrieving information about transactions,
 * you can search by transaction-id or by date
 *
 * eg: $transactionDataObj = new SofortLib_TransactionData('yourapikey');
 *
 * $transactionDataObj->setTransaction('1234-456-789654-31321')->sendRequest();
 *
 * echo $transactionDataObj->getStatus();
 *
 * Copyright (c) 2011 Payment Network AG
 *
 * $Date: 2011-11-30 10:40:45 +0100 (Wed, 30 Nov 2011) $
 * @version SofortLib 1.3.0  $Id: sofortLib_transaction_data.inc.php 2418 2011-11-30 09:40:45Z dehn $
 * @author Payment Network AG http://www.payment-network.com (integration@sofort.com)
 *
 */
class SofortLib_TransactionData extends SofortLib_Abstract
{
	var $transaction = array();
	var $time=array();
	var $response = array();
	var $count = 0;

	function SofortLib_TransactionData($apikey='') {
		list($userid, $projectId, $apikey) = explode(':', $apikey);
		$apiUrl = (getenv('sofortApiUrl') != '') ? getenv('sofortApiUrl') : 'https://api.sofort.com/api/xml';
		$this->SofortLib($userid, $apikey, $apiUrl);
		return $this;
	}


	/**
	 * use this function if you want to request
	 * detailed information about a single transaction
	 *
	 * @param String $arg
	 * @return SofortLib_TransactionData $this
	 */
	function setTransaction($arg) {
		$this->response = array();
		$this->count = 0;
		if(is_array($arg)) {
			foreach($arg as $element) {
				$this->transaction[] = $element;
			}
		} else {
			$this->transaction[] = $arg;
		}
		return $this;
	}


	/**
	 * use this function if you want to request
	 * detailed information about several transactions
	 * at once
	 *
	 * @param String $arg
	 * @return SofortLib_TransactionData $this
	 */
	function addTransaction($arg) {
		if(is_array($arg)) {
			foreach($arg as $element) {
				$this->transaction[] = $element;
			}
		} else {
			$this->transaction[] = $arg;
		}
		return $this;
	}


	/**
	 * you can request all transactions of a certain time
	 * period
	 *
	 * use setNumber() to limit the results
	 *
	 * @param string $from date possible formats: 2011-01-25 or 2011-01-25T19:01:02+02:00
	 * @param string $to date possible formats: 2011-01-25 or 2011-01-25T19:01:02+02:00
	 * @return SofortLib_TransactionData $this
	 * @see setNumber()
	 */
	function setTime($from, $to) {
		$this->time['from_time'] = $from;
		$this->time['to_time'] = $to;
		return $this;
	}


	/**
	 * you can limit the number of results
	 *
	 * @param int $number number of results [0-100]
	 * @param int $page result page
	 * @return SofortLib_TransactionData $this
	 * @see setTime()
	 */
	function setNumber($number, $page='1') {
		$this->time['number'] = $number;
		$this->time['page'] = $page;
		return $this;
	}


	function onTagOpen($tag) {
		switch($tag) {
			case 'transaction_details':
				if($this->_getParentTag() == 'transactions') {
					array_push($this->response, array());
					$this->count += 1;
				}
				break;
			case 'credit':
			case 'payment':
			case 'item':
				if($this->_getParentTag() == 'credits' || $this->_getParentTag() == 'items' 
				|| $this->_getParentTag() == 'payments') {
					$this->response[$this->count-1][$tag][] = array();
				}				
			default:
				break;
		}
	}


	/**
	 * Parser for response from server
	 * this callback will be called for every closing xml-tag
	 * @private
	 */
	function onParseTag($data, $tag){
		switch($tag) {
			//plain data
			case 'status':
			case 'status_reason':
			case 'payment_method':
			case 'amount':
			case 'amount_refunded':
			case 'currency_code':
			case 'transaction':
			case 'time':
			case 'project_id':
			case 'test':
			case 'start_date':
			case 'minimum_payments':
			case 'payments_received':
			case 'total_payments':
			case 'interval':				
			case 'invoice_status':
			case 'invoice_objection':
			case 'invoice_url':
			case 'invoice_type':
			case 'invoice_date':
			case 'invoice_number':
			case 'due_date':
			case 'reason_1':
			case 'reason_2':
			case 'logo_url':
			case 'debitor_text':
			case 'customer_number':
				if($this->_getParentTag() == 'transaction_details' || $this->_getParentTag() == 'sr' 
				|| $this->_getParentTag() == 'sa') {
					$this->response[$this->count-1][$tag] = $data;
					break;
				} 
			//lists
			case 'reason':			//reasons
			case 'user_variable':	//user_variables
				if($this->_getParentTag() == 'user_variables' || $this->_getParentTag() == 'reasons') {
					$this->response[$this->count-1][$tag][] = $data;
					break;
				}
			//containers
			case 'currency_code':	//costs
			case 'fees':			//costs
			case 'exchange_rate':	//costs
			case 'salutation':		//incoive_address + shipping_address
			case 'firstname':		//incoive_address + shipping_address
			case 'lastname':		//incoive_address + shipping_address
			case 'street':			//incoive_address + shipping_address
			case 'street_number':	//incoive_address + shipping_address
			case 'zipcode':			//incoive_address + shipping_address
			case 'city':			//incoive_address + shipping_address
			case 'country_code':	//incoive_address + shipping_address
			case 'holder':			//sender + reciepient + recipient_bank_account
			case 'iban':			//sender + reciepient
			case 'bic':				//sender + reciepient
			case 'bank_code':		//sender + reciepient + recipient_bank_account
			case 'account_number':	//sender + reciepient + recipient_bank_account
			case 'bank_name':		//sender + reciepient + recipient_bank_account
				if($this->_getParentTag() == 'costs' || $this->_getParentTag() == 'invoice_address' 
				|| $this->_getParentTag() == 'shipping_address' || $this->_getParentTag() == 'sender' 
				|| $this->_getParentTag() == 'recipient' || $this->_getParentTag() == 'recipient_bank_account') {

					$this->response[$this->count-1][$this->_getParentTag()][$tag] = $data;
					break;
				}
			//containerlists	
			case 'credit_number':	//sr->credits
			case 'amount':			//sr->credits
			case 'tax_amount':		//sr->credits
			case 'currency_code':	//sr->credits
			case 'comment':			//sr->credits
			case 'serial':			//sa->payments
			case 'status':			//sa->payments
			case 'expected':		//sa->payments
			case 'received':		//sa->payments
			case 'item':			//sr->items
			case 'item_id':			//sr->items
			case 'product_number':	//sr->items
			case 'product_type':	//sr->items
			case 'number_type':		//sr->items
			case 'title':			//sr->items
			case 'description':		//sr->items
			case 'quantity':		//sr->items
			case 'unit_price':		//sr->items
			case 'tax':				//sr->items
				if($this->_getParentTag() == 'credit' || $this->_getParentTag() == 'payment'
				|| $this->_getParentTag() == 'item') {
					$c = count($this->response[$this->count-1][$this->_getParentTag()])-1;
					$this->response[$this->count-1][$this->_getParentTag()][$c][$tag] = $data;
					break;						
				}
			default:
				break;
		}
	}


	/**
	 * generate XML message
	 * @return string
	 */
	function toXml() {
		$msg = '<?xml version="1.0" encoding="UTF-8"?>';
		if(count($this->transaction) > 0) {
			$msg .= '<transaction_request>';
			$msg .= $this->_indexedArrayToXmlList($this->transaction, 'transaction');
			$msg .= '</transaction_request>';
		}
		else
			$msg .= $this->_arrayToXml($this->time, 'transaction_request');

		return $msg;
	}


	/**
	 * returns the InvoiceAddress
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return array
	 */
	function getInvoiceAddress($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['invoice_address'];
	}


	/**
	 * returns the ShippingAddress
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return array
	 */
	function getShippingAddress($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['shipping_address'];
	}


	/**
	 * returns the status of a transaction
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string pending|received|loss|refunded
	 */
	function getStatus($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['status'];
	}


	/**
	 * returns the detailed status description of a transaction
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string message
	 */
	function getStatusReason($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['status_reason'];
	}


	/**
	 * returns the total amount of a transaction
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return double amount
	 */
	function getAmount($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['amount'];
	}

	/**
	 * refund, if a transaction was refundend. amount = amountRefunded if everything was refunded
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return double amount
	 */
	function getAmountRefunded($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['amount_refunded'];
	}	

	/**
	 * returns the currency of a transaction
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string EUR|USD|GBP....
	 */
	function getCurrency($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['currency_code'];
	}


	/**
	 * returns the payment method of a transaction
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string su|sr|sl|sv|ls
	 */
	function getPaymentMethod($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

			return $this->response[$i]['payment_method'];
	}


	/**
	 * returns the payments array for an abo payment
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return array
	 */
	function getSofortdauerauftragPayments($i = 0) {
		if($i < 0 || $i >= $this->count || $this->response[$i]['payment_method'] != 'sa')
			return false;
		
		return $this->response[$i]['payment'];
	}


	/**
	 * returns the start date for an abo payment
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return array
	 */
	function getSofortdauerauftragStartDate($i = 0) {
		if($i < 0 || $i >= $this->count || $this->response[$i]['payment_method'] != 'sa')
			return false;
			
		return $this->response[$i]['start_date'];
	}


	/**
	 * returns the interval for an abo payment
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return int
	 */
	function getSofortdauerauftragInterval($i = 0) {
		if($i < 0 || $i >= $this->count || $this->response[$i]['payment_method'] != 'sa')
			return false;
			
		return $this->response[$i]['interval'];
	}


	/**
	 * returns total of payments of an abo payment
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return int
	 */
	function getSofortdauerauftragTotalPayments($i = 0) {
		if($i < 0 || $i >= $this->count || $this->response[$i]['payment_method'] != 'sa')
			return false;
			
		return $this->response[$i]['total_payments'];
	}


	/**
	 * returns total sum of received payments of an abo payment
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return int
	 */
	function getSofortdauerauftragPaymentsSum($i = 0) {
		if($i < 0 || $i >= $this->count || $this->response[$i]['payment_method'] != 'sa')
			return false;
		$sum = 0;
		foreach($this->response[$i]['payment'] as $payment) {
			if(array_key_exists('received', $payment)) {
				$sum += $this->getAmount($i);
			}
		}
		return $sum;	
	}


	/**
	 * returns the number of received payments of an abo payment
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return int
	 */
	function getSofortdauerauftragNumberOfPaymentsPending($i = 0) {
		if($i < 0 || $i >= $this->count || $this->response[$i]['payment_method'] != 'sa')
			return false;
		$sum = 0;
		foreach($this->response[$i]['payment'] as $payment) {
			if(empty($payment['received'])) {
				$sum++;
			}
		}
		return $sum;	
	}


	/**
	 * returns total sum of received payments of an abo payment
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return int
	 */
	function getSofortdauerauftragNumberOfPaymentsReceived($i = 0) {
		if($i < 0 || $i >= $this->count || $this->response[$i]['payment_method'] != 'sa')
			return false;
		$numberOfPayments = 0;
		foreach($this->response[$i]['payment'] as $payment) {
			if(!empty($payment['received'])) {
				$numberOfPayments++;
			}
		}
		return $numberOfPayments;	
	}


	/**
	 * returns the count of minimum payments for an abo payment
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return int
	 */
	function getSofortdauerauftragMinimumPayments($i = 0) {
		if($i < 0 || $i >= $this->count || $this->response[$i]['payment_method'] != 'sa')
			return false;
		return $this->response[$i]['minimum_payments'];
	}


	/**
	 * returns the count of received payments for an abo payment
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return int
	 */
	function getSofortdauerauftragPaymentsReceived($i = 0) {
		if($i < 0 || $i >= $this->count || $this->response[$i]['payment_method'] != 'sa')
			return false;
			
		return $this->response[$i]['payments_received'];
	}


	/**
	 * 
	 * Enter description here ...
	 * @param int $i if you request multiple transactions at once you can set the number here
	 */
	function getSofortdauerauftragPaymentCount($i = 0) {
		if($i < 0 || $i >= $this->count || $this->response[$i]['payment_method'] != 'sa')
			return false;
			
		return count($this->response[$i]['payment']);
	}


	/**
	 * 
	 * Get the $j'th payment of $i'th transaction
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @param int $j int current payment
	 * @return boolean
	 */
	function getSofortdauerauftragPaymentReceived($i = 0, $j = 0) {
		if($i < 0 || $i >= $this->count || $this->response[$i]['payment_method'] != 'sa' || $j < 0 || ( $j > count($this->response[$i]['payment']) -1) )
			return false;
		return !empty($this->response[$i]['payment'][$j]['received']);	
	}


	/**
	 * 
	 * Only payments made in the past (or the current day) are respected ...
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return boolean
	 */
	function getSofortdauerauftragAllPaymentsReceived($i = 0) {
		if($i < 0 || $i >= $this->count || $this->response[$i]['payment_method'] != 'sa')
			return false;

		$receivedPayments = 0;
			
		$date = date('Y-m-d', time());
		$actDayTimestamp = strtotime($date);	
		
		foreach($this->response[$i]['payment'] as $payment) {

			if(!empty($payment['received'])) {
				$receivedPayments++;
			}
			
			if($payment['status'] == 'loss') 
				return false;
		}
		
		if(!$receivedPayments) return false;
		
		return true;
	}


	/**
	 * 
	 * Get the status (received true|false) of the last payment of the currenct transaction
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return boolean
	 */
	function getSofortdauerauftragLastPaymentReceived($i = 0) {
		if($i < 0 || $i >= $this->count || $this->response[$i]['payment_method'] != 'sa')
			return false;
			
		$lastPayment = count($this->response[$i]['payment']);
		// if the last payment is not the last payment in line: return false
		if($lastPayment != $this->response[$i]['total_payments']) 
			return false;
		else {
			// if the last payment in line is not empty return true, false otherwise
			return $this->response[$i]['payment'][$lastPayment-1]['status'] == 'received';
		}
	}


	/**
	 * returns the transaction id of a transaction
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string transaction id
	 */
	function getTransaction($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['transaction'];
	}


	/**
	 * 
	 * Returns an array containing all items of a transaction
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @ return array transactions items
	 */
	function getItems($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;
		return $this->response[$i]['item'];
	}
	
	/**
	 * 
	 * Returns an array containing reason of a transaction
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @ return array transaction reason
	 */
	function getReason($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;
		return $this->response[$i]['reason'];
	}
	
	
	/**
	 * calculate the total amount at pnag from the pnag-response
	 * @return float if ok ELSE -1
	 */
	function getPnagTotal() {
		$pnagTotal = -1;
		if(isset($this->response[0]) && isset($this->response[0]['amount'])) {
			return $this->response[0]['amount'];
		} else {
			return -1;
		}
	}

	/**
	 * returns the user variable of a transaction
	 * @param int $n number of the variable
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string the content of this variable
	 */
	function getUserVariable($n, $i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['user_variable'][$n];
	}


	/**
	 * returns the time of a transaction
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string time e.g. 2011-01-01T12:35:09+01:00 use strtotime() to convert it to unixtime
	 */
	function getTime($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['time'];
	}


	/**
	 * returns the project id of a transaction
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return int project id
	 */
	function getProjectId($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['project_id'];
	}


	/**
	 * you can request the url to the pdf of a sr-invoice with this function
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string url to the pdf
	 */
	function getInvoiceUrl($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['invoice_url'];
	}


	/**
	 * returns the status of an invoice
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string the status can be pending|received|reminder_1|reminder_2|reminder_3|encashment
	 */
	function getInvoiceStatus($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['invoice_status'];
	}


	/**
	 * returns the status of an invoice
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string the status can be pending|received|reminder_1|reminder_2|reminder_3|encashment
	 */
	function getInvoiceObjection($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['invoice_objection'];
	}


	/**
	 * checks if the transaction was a test
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return bool true|false
	 */
	function isTest($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['test'];
	}


	/**
	 * 
	 * check if the transaction was a sofortueberweisung
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return boolean true|false
	 */
	function isSofortueberweisung($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;
		return $this->response[$i]['payment_method'] == 'su';
	}


	/**
	 * 
	 * check if the transaction was a sofortvorkasse
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return boolean true|false
	 */
	function isSofortvorkasse($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;
		return $this->response[$i]['payment_method'] == 'sv';
	}

	/**
	 * 
	 * check if the transaction was a sofortlastschrift
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return boolean true|false
	 */
	function isSofortlastschrift($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;
		return $this->response[$i]['payment_method'] == 'sl';
	}

	/**
	 * 
	 * check if the transaction was a lastschrift by sofort 
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return boolean true|false
	 */
	function isLastschrift($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;
		return $this->response[$i]['payment_method'] == 'ls';
	}

	/**
	 * 
	 * check if the transaction was a sofortdauerauftrag
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return boolean true|false
	 */
	function isSofortdauerauftrag($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;
		return $this->response[$i]['payment_method'] == 'sa';
	}


	/**
	 * 
	 * check if the transaction was a sofortrechnung
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return boolean true|false
	 */
	function isSofortrechnung($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;
		return $this->response[$i]['payment_method'] == 'sr';
	}
	
	/**
	 * 
	 * check if status of transaction is received
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return boolean true|false
	 */
	function isReceived($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['status'] == 'received';
	}
	
	/**
	 * 
	 * check if status of transaction is loss
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return boolean true|false
	 */
	function isLoss($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['status'] == 'loss';
	}

	/**
	 * 
	 * check if status of transaction is pending
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return boolean true|false
	 */
	function isPending($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['status'] == 'pending';
	}	

	/**
	 * 
	 * check if status of transaction is refunded
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return boolean true|false
	 */
	function isRefunded($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['status'] == 'refunded';
	}	

	/**
	 * returns the holder of the receiving account
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string value
	 */
	function getRecipientHolder($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['recipient']['holder'];		 
	}	
	
	/**
	 * 
	 * returns the account number of the receiving account
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string value
	 */
	function getRecipientAccountNumber($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['recipient']['account_number'];		 
	}	
	
	/**
	 * 
	 * returns the bank code of the receiving account
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string value
	 */
	function getRecipientBankCode($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['recipient']['bank_code'];		 
	}	

	/**
	 * 
	 * returns the country code of the receiving account
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string value
	 */
	function getRecipientCountryCode($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['recipient']['country_code'];		 
	}		

	/**
	 * 
	 * returns the bank name of the receiving account
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string value
	 */
	function getRecipientBankName($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['recipient']['bank_name'];		 
	}		
	
	/**
	 * 
	 * returns the BIC of the receiving account
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string value
	 */
	function getRecipientBic($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['recipient']['bic'];		 
	}		
	
	/**
	 * 
	 * returns the IBAN of the receiving account
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string value
	 */
	function getRecipientIban($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['recipient']['iban'];		 
	}		

	/**
	 * returns the holder of the sending account
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string value
	 */
	function getSenderHolder($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['sender']['holder'];		 
	}	
	
	/**
	 * 
	 * returns the account number of the sending account
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string value
	 */
	function getSenderAccountNumber($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['sender']['account_number'];		 
	}	
	
	/**
	 * 
	 * returns the bank code of the sending account
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string value
	 */
	function getSenderBankCode($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['sender']['bank_code'];		 
	}	

	/**
	 * 
	 * returns the country code of the sending account
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string value
	 */
	function getSenderCountryCode($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['sender']['country_code'];		 
	}		

	/**
	 * 
	 * returns the bank name of the sending account
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string value
	 */
	function getSenderBankName($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['sender']['bank_name'];		 
	}		
	
	/**
	 * 
	 * returns the BIC of the sending account
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string value
	 */
	function getSenderBic($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['sender']['bic'];		 
	}		
	
	/**
	 * 
	 * returns the IBAN of the sending account
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string value
	 */
	function getSenderIban($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		return $this->response[$i]['sender']['iban'];		 
	}	
	
	/**
	 * returns the reason the customer needs to use when paying for "Rechnung by sofort"  
	 * @param int $n specify reason linenumber, can be 1 or 2; us 0 for an array with all reasons
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string|array reason
	 */
	function getInvoiceReason($n = 0, $i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;

		if($n == 1)
			return $this->response[$i]['reason_1'];
		if($n == 2)
			return $this->response[$i]['reason_2'];
			
		return array($this->response[$i]['reason_1'],$this->response[$i]['reason_2']);
	}

	/**
	 * get debitor text (Forderungsabtretung)
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string
	 */
	function getInvoiceDebitorText($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;
			
		return $this->response[$i]['debitor_text'];
	}

	/**
	 * 
	 * date of the invoice
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string
	 */
	function getInvoiceDate($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;
			
		return $this->response[$i]['invoice_date'];		
	}

	/**
	 * 
	 * due date of the invoice, only available for confirmed invoices
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string
	 */
	function getInvoiceDueDate($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;
			
		return $this->response[$i]['due_date'];		
	}

	/**
	 * 
	 * invoice number
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string
	 */
	function getInvoiceNumber($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;
			
		return $this->response[$i]['invoice_number'];		
	}	

	/**
	 * 
	 * invoice receiving bank account 
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string
	 */
	function getInvoiceBankHolder($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;
			
		return $this->response[$i]['recipient_bank_account']['holder'];		
	}	
	

	/**
	 * 
	 * invoice receiving bank account 
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string
	 */
	function getInvoiceBankAccountNumber($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;
			
		return $this->response[$i]['recipient_bank_account']['account_number'];		
	}	
	

	/**
	 * 
	 * invoice receiving bank account 
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string
	 */
	function getInvoiceBankCode($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;
			
		return $this->response[$i]['recipient_bank_account']['bank_code'];		
	}	
	

	/**
	 * 
	 * invoice receiving bank account 
	 * @param int $i if you request multiple transactions at once you can set the number here
	 * @return string
	 */
	function getInvoiceBankName($i = 0) {
		if($i < 0 || $i >= $this->count)
			return false;
			
		return $this->response[$i]['recipient_bank_account']['bank_name'];		
	}	
}