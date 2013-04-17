<?php

App::import('Vendor', array('Payment.bitcoin/bitcoin'));
if (!defined('BITCOIN_CERTIFICATE')) {
	define('BITCOIN_CERTIFICATE', APP . 'Config' . DS.'server.cert');
}

/**
 * CakePHP1.3 Wrapper for PHP Bitcoin Library (Vendor)
 * - adds more methods
 * - offline mode available for localhost development (does not through exceptions if not connected to bitcoind daemon)
 * - automagic via configure::write()
 * - wraps the http://blockexplorer.com/q API in offline mode and where the daemon is not able to handle it
 *
 *****
 * If you find this library useful, your donation of Bitcoins to address
 * 161AcnPykE42e4ErQNR9B73Bb78Jy81AN6 would be greatly appreciated. Thanks! Mark Scherer
 *****
 *
 * v1.0
 * @author Mark Scherer
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 2011-07-19 ms
 */
class BitcoinLib extends Bitcoin {

	public $C = null;
	public $info = null;

	public $defaults = array(
		'scheme' => 'http', # should be https (but http works)
		'username' => '',
		'password' => '',
		'certificate' => '', # path (absolute)
		'port' => 8332,
		'debug' => 0, # logs everything in debug mode (not only successfull transactions) TODO
		'address' => 'localhost',
		'account' => '', # default account to work on (do not use the empty account!)
		'minconf' => 6, # official minconf, but can be lowered down to 3 just fine
		'daemon' => true, # set to false if you can't run daemon (will automatically happen if you dont provide username+password)
	);

	public $settings = array();

	public function __construct($settings = array()) {
		$this->defaults = array_merge($this->defaults, (array)Configure::read('Bitcoin'));
		$this->settings = array_merge($this->defaults, $settings);
		extract($this->settings, EXTR_OVERWRITE);

		if ($this->settings['certificate'] === true) {
			$this->settings['certificate'] = BITCOIN_CERTIFICATE;
		}

		if (!Configure::read('Bitcoin.username') || !Configure::read('Bitcoin.password')) {
			# offline mode - will result in incomplete checks (as some methods cannot completely be replaced by webservice)
			$this->settings['daemon'] = false;
			return;
		}

		if (!$this->C) {
			$this->C = new BitcoinClient($scheme, $username, $password, $address, $port, $certificate, $debug);
		}
	}

	/**
	 * return info about your wallet
	 * 2011-07-13 ms
	 */
	public function getInfo() {
		if ($this->info !== null) {
			return $this->info;
		}
		if (!$this->settings['daemon']) {
			$this->info = array();
		} else {
			$this->info = $this->C->getinfo();
		}
		return $this->info;
	}

	public function getAddressesByAccount($account = null) {
		if (!$this->settings['daemon']) {
			return array();
		}
		if ($account === null) {
			$account = $this->settings['account'];
		}
		return $this->C->getaddressesbyaccount($account);
	}

	public function getAccountAddress($account = null) {
		if (!$this->settings['daemon']) {
			return false;
		}
		if ($account === null) {
			$account = $this->settings['account'];
		}
		return $this->C->getaccountaddress($account);
	}

	public function getAccount($address) {
		return $this->C->getaccount($address);
	}

	/**
	 * moves the address to a differenc account
	 * 2011-07-20 ms
	 */
	public function setAccount($address, $account = null) {
		return $this->C->setaccount($address, $account = null);
	}

	public function backupWallet($dest) {
		return $this->C->backupwallet($dest);
	}

	/**
	 * total amount received by address
	 * 2011-07-16 ms
	 */
	public function getReceivedByAddress($address, $minconf = 1) {
		if ($minconf === null) {
			$minconf = $this->settings['minconf'];
		}
		if (true || !$this->settings['daemon']) { # bug in daemon v32400!!! returns always 0 for no good reason
			return $this->_getReceivedByAddress($address, $minconf);
		}
		return $this->C->getreceivedbyaddress($address, $minconf);
	}

	public function listReceivedByAddress($minconf = 1, $includeempty = FALSE) {
		if ($minconf === null) {
			$minconf = $this->settings['minconf'];
		}
		return $this->C->listreceivedbyaddress($minconf, $includeempty);
	}

	public function listReceivedByAccount($minconf = 1, $includeempty = FALSE) {
		if ($minconf === null) {
			$minconf = $this->settings['minconf'];
		}
		return $this->C->listreceivedbyaccount($minconf, $includeempty);
	}

	public function listReceivedByLabel($minconf = 1, $includeempty = FALSE) {
		if ($minconf === null) {
			$minconf = $this->settings['minconf'];
		}
		return $this->C->listreceivedbylabel($minconf, $includeempty);
	}

	public function listAccounts() {
		if (!$this->settings['daemon']) {
			return array();
		}
		return $this->C->query('listaccounts');
	}

	/**
	 * @param string $account (defaults to own)
	 * @param string $type (defaults to all: receive, send, move)
	 * 2011-07-21 ms
	 */
	public function listTransactions($account = null, $type = null) {
		if (!$this->settings['daemon']) {
			return array();
		}
		if ($account === null) {
			$account = $this->settings['account'];
		}
		if (!$account) {
			return $this->C->query('listtransactions');
		}
		return $this->C->query('listtransactions', $account);
	}


	/**
	 * gets the balance of a specific account in your wallet
	 * @param string $account (defaults to own)
	 * @return int $amount or bool FALSE if offline or account not found
	 * 2011-07-19 ms
	 */
	public function getBalance($account = null) {
		if (!$this->settings['daemon']) {
			return false;
		}
		if ($account === null) {
			$account = $this->settings['account'];
		}
		$accounts = $this->listAccounts();
		foreach ($accounts as $ownAccount => $amount) {
			if ($ownAccount == $account) {
				return $amount;
			}
		}
		//throw new BitcoinClientException('invalid account given');
		return false;
	}


	/**
	 * @param string $transaction
	 * @param bool $isMine (defaults to null): false => has to be foreign, true => has to be own
	 * @return bool $success
	 * 2011-07-19 ms
	 */
	public function validateTransaction($txid, $isMine = null) {
		if (empty($txid) || strlen($txid) != 64 || !preg_match('/^[0-9a-fA-F]+$/', $txid)) {
			return false;
		}
		return true;
	}


	/**
	 * @param string $address
	 * @param bool $isMine (defaults to null): false => has to be foreign, true => has to be own
	 * @return bool $success
	 * make sure an address is correct (length, network availability)
	 * note: if in offline mode it will only check the length and chars
	 * 2011-07-18 ms
	 */
	public function validateAddress($address, $isMine = null) {
		/*
		if (!preg_match('/^[a-z0-9]{33,34}$/i', $address)) {
			return false;
		}
		*/
		if (!Bitcoin::checkAddress($address)) {
			return false;
		}
		if (!$this->settings['daemon']) {
			return true;
		}
		$res = $this->C->validateaddress($address);
		if (empty($res['isvalid'])) {
			return false;
		}
		if ($isMine !== null) {
			return $res['ismine'] == $isMine;
		}
		return true;
	}

	public function getHashesPerSec() {
		return $this->C->gethashespersec();
	}

	public function getTransaction($tx) {
		if (!$this->settings['daemon']) {
			return array();
		}
		return $this->C->gettransaction($tx);
	}

	public function getNewAddress($account = null) {
		if (!$this->settings['daemon']) {
			return false;
		}
		if ($account === null) {
			$account = $this->settings['account'];
		}
		return $this->C->getnewaddress($account);
	}

	public function sendFrom($fromAccount = null, $toAddress, $amount, $minconf = 1, $comment = NULL, $comment_to = NULL) {
		if ($fromAccount === null) {
			$fromAccount = $this->settings['account'];
		}
		return $this->C->sendfrom($fromAccount, $toAddress, $amount, $minconf = 1, $comment = NULL, $comment_to = NULL);
	}

	public function sendToAddress($address, $amount, $comment = NULL, $comment_to = NULL) {
		if (!$this->settings['daemon']) {
			return false;
		}
		return $this->C->sendtoaddress($address, $amount, $comment = NULL, $comment_to);
	}

	/**
	 * transfer money from one account to another
	 * 2011-07-20 ms
	 */
	public function move($fromAccount = null, $toAccount, $amount, $minconf = 1, $comment = NULL) {
		if (!$this->settings['daemon']) {
			return false;
		}
		if ($fromAccount === null) {
			$fromAccount = $this->settings['account'];
		}
		return $this->C->move($fromAccount, $toAccount, $amount, $minconf, $comment);
	}

	public function setFee($amount) {
		if (!$this->settings['daemon']) {
			return false;
		}
		return $this->C->query('settxfee', $amount);
	}


	/**
	 * get number of total bitcoins in circulation
	 * 2011-07-16 ms
	 */
	public function getTotalBitcoins() {
		return (int)$this->_query('totalbc');
	}


	/**
	 * shows the time at which an address was first seen on the network
	 * 2011-07-16 ms
	 */
	public function addressFirstSeen($address) {
		return $this->_query('addressfirstseen/'.$address);
	}

	/**
	 * Returns total BTC sent by an address. Using this data is almost always a very
bad idea, as the amount of BTC sent by an address is usually very different
from the amount of BTC sent by the person owning the address
	 * 2011-07-16 ms
	 */
	public function getTotalSentByAddress($address) {
		return $this->_query('getsentbyaddress/'.$address);
	}


	/**
	 * Returns all transactions sent or received by the period-separated Bitcoin
addresses in parameter 1. The optional parameter 2 contains a hexadecimal block
hash: transactions in blocks up to and including this block will not be returned.
	 * 2011-07-16 ms
	 */
	public function myTransactions($address, $block = null) {
		if ($block) {
			$address .= '/'.$block;
		}
		$res = $this->_query('mytransactions/'.$address);
		if (!empty($res)) {
			return (array)json_decode($res);
		}
	}

	/**
	 * shows the number of blocks in the longest block chain (not including the genesis block). Equivalent to Bitcoin's getblockcount
	 * @return int
	 * 2011-07-16 ms
	 */
	public function getBlockCount() {
		if (!$this->settings['daemon']) {
			return (int)$this->_query('getblockcount');
		}
		$this->getInfo();
		if (empty($this->info['blocks'])) {
			return 0;
		}
		return $this->info['blocks'];
	}

	/**
	 * shows the difficulty
	 * @return int
	 * 2011-07-16 ms
	 */
	public function getDifficulty() {
		if (!$this->settings['daemon']) {
			return (int)$this->_query('getdifficulty');
		}
		$this->getInfo();
		if (empty($this->info['difficulty'])) {
			return 0;
		}
		return (int)$this->info['difficulty'];
	}

/** backup methods if localhost is not running bitcoind service **/

	/**
	 * Returns total BTC received by an address. Sends are not taken into account.
The optional second parameter specifies the required number of confirmations for
transactions comprising the balance
	 * @return float $amount
	 * 2011-07-16 ms
	 */
	public function _getReceivedByAddress($address, $minconf = null) {
		if ($minconf) {
			$address .= '/'.$minconf;
		}
		return (float)$this->_query('getreceivedbyaddress/'.$address);
	}

	/**
	 * @deprecated
	 * note: probably not neccessary as validation is already implemented in bitcoin.php
	 * Returns 00 if the address is valid, something else otherwise.
	 */
	public function _checkAddress($address) {
		$res = $this->_query('checkaddress/'.$address);
		if (empty($res) || $res !== '00') {
			return false;
		}
		return true;
	}

	/**
	 * does the actual query
	 * 2011-07-16 ms
	 */
	public function _query($q) {
		$url = 'http://blockexplorer.com/q/';
		$res = file_get_contents($url.$q);

		if ($res === '') {
			trigger_error('Lookup Failed ('.$q.')');
			return '';
		} elseif (strpos($res, 'ERROR: ') === 0) {
			trigger_error(substr($res, 7));
			return '';
		}

		return $res;
	}

}
