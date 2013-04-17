<?php
/**
 * @mainpage
 * Base class for Payment Network XML-Api
 * This class implements basic http authentication and a xml-parser
 * for parsing response messages
 *
 * Requires libcurl and openssl
 *
 * Copyright (c) 2011 Payment Network AG
 *
 * $Date: 2011-12-07 18:03:08 +0100 (Wed, 07 Dec 2011) $
 * @version SofortLib 1.4.0  $Id: sofortLib.php 2613 2011-12-07 17:03:08Z poser $
 * @author Payment Network AG http://www.payment-network.com (integration@sofort.com)
 *
 */

define('VERSION','1.4.0');

require_once dirname(__FILE__).'/sofortLib_abstract.inc.php';
require_once dirname(__FILE__).'/sofortLib_confirm_sr.inc.php';
require_once dirname(__FILE__).'/sofortLib_ideal_banks.inc.php';
require_once dirname(__FILE__).'/sofortLib_cancel_sa.inc.php';
require_once dirname(__FILE__).'/sofortLib_debit.inc.php';
require_once dirname(__FILE__).'/sofortLib_http.inc.php';
require_once dirname(__FILE__).'/sofortLib_multipay.inc.php';
require_once dirname(__FILE__).'/sofortLib_notification.inc.php';
require_once dirname(__FILE__).'/sofortLib_refund.inc.php';
require_once dirname(__FILE__).'/sofortLib_transaction_data.inc.php';
require_once dirname(__FILE__).'/sofortLib_Logger.inc.php';

/** Include any available helper here **/
require_once dirname(__FILE__).'/helper/class.abstract_document.inc.php';
require_once dirname(__FILE__).'/helper/class.invoice.inc.php';

/**
 *
 * Basic PHP Library for communication with multipay API and related products of sofort.com
 * @author payment-network.com
 *
 */
class SofortLib {
	var $apiKey;
	var $userId;
	var $request;
	var $errorPos = 'global'; //or su, sr, sv...
	var $errors = array();
	var $warnings = array();
	var $enableLogging = false;
	var $errorCountTemp = 0;
	var $logger = null;

	/**
	 * Constructor
	 * @param int $userId
	 * @param string $apiKey
	 * @param string $apiUrl
	 */
	function SofortLib($userId, $apiKey, $apiUrl) {
		$this->apiKey = $apiKey;
		$this->userId = $userId;
		$this->request = new SofortLib_Http($apiUrl, $this->_getHeaders());
		$this->logger = new SofortLibLogger();
		$this->enableLogging = (getenv('sofortDebug') == 'true') ? true : false;
	}
	
	
	/**
	 * check if warnings from pnag came and returns them
	 *
	 * @return empty array if no warnings exists ELSE array with warning-codes and warning-messages
	 * @public
	 */
	function getWarnings($paymentMethod = 'all', $message = '') {
		if($message == '') {
			$message = $this->warnings;
		}else{
			$message = $this->_parseErrorresponse($message);
		}
		
		$supportedPaymentMethods = array('all', 'sr', 'su', 'sv', 'sa', 'ls', 'sl', 'sf');
		if(!in_array($paymentMethod, $supportedPaymentMethods)) {
			$paymentMethod = 'all';
		}
		if($this->isWarning($paymentMethod, $message)) {
			if($paymentMethod == 'all') {
				return $message;
			}else{
				$returnArray = array();
				if(isset($message['global']) && !empty($message['global'])) {
					$returnArray['global'] = $message['global'];
				}
				$returnArray[$paymentMethod] = $message[$paymentMethod];
				return $returnArray;
			}
		} else {
			return array();
		}
	}
	
	
	/**
	 * check if errors from pnag came and returns them
	 *
	 * @param (optional) array $message response array
	 * @return emtpy array if no error exist ELSE array with error-codes and error-messages
	 * @public
	 */
	function getErrors($paymentMethod = 'all', $message = '') {
		if($message == ''){
			$message = $this->errors;
		}else{
	 		$message = $this->_parseErrorresponse($message);
	 	}

		if(!$this->isError($paymentMethod, $message)) {
			return array();
		}

		$supportedPaymentMethods = array('global', 'sr', 'su', 'sv', 'sa', 'ls', 'sl', 'sf');
		if(!in_array($paymentMethod, $supportedPaymentMethods)) {
			$paymentMethod = 'all';
		}
		$returnArray = array();
		//return global + selected payment method
		foreach($supportedPaymentMethods as $pm) {
			if(($paymentMethod == 'all' || $pm == 'global' || $paymentMethod == $pm)
			&& array_key_exists($pm, $message)) {
				$returnArray = array_merge($returnArray, $message[$pm]);
			}
		}

		return $returnArray;
	}
	
	
	/**
	 * returns one errormessage (as String!)
	 * @see getErrors() for more detailed errors
	 * @param array $message response array
	 * @return string errormessage ELSE false
	 * @public
	 */
	function getError($paymentMethod = 'all', $message = '') {
		if($message == ''){
			$message = $this->errors;
		}else{
	 		$message = $this->_parseErrorresponse($message);
	 	}

		$supportedPaymentMethods = array('all', 'sr', 'su', 'sv', 'sa', 'ls', 'sl', 'sf');
		if(!in_array($paymentMethod, $supportedPaymentMethods)) {
			$paymentMethod = 'all';
		}
		if(is_array($message)) {
			if($paymentMethod == 'all') {
				foreach($message as $key => $error) {
				if(is_array($error) && !empty($error)){
						return 'Error: ' . $error[0]['code'] . ':' . $error[0]['message'];
					}
				}
			}else{
				foreach($message as $key => $error) {
					if($key != 'global' && $key != $paymentMethod) {
						continue;
					}

					if(is_array($error) && !empty($error)){
						return 'Error: ' . $error[0]['code'] . ':' . $error[0]['message'];
					}
				}
			}
		}
		return false;
	}
	
	
	/**
	 *
	 * checks (response)-array for warnings
	 * @param array $message response array
	 * @param string $paymentMethod - 'all', 'sr', 'su', 'sv', 'sa', 'ls', 'sl', 'sf' (if unknown then it uses "all")
	 * @return boolean true if warnings found ELSE false
	 * @public
	 */
	function isWarning($paymentMethod = 'all', $message = '' ) {
		return $this->isError($paymentMethod, $message);
	}
	
	
	/**
	 * checks (response)-array for error
	 * @param array $message response array
	 * @param string $paymentMethod - 'all', 'sr', 'su', 'sv', 'sa', 'ls', 'sl', 'sf' (if unknown then it uses "all")
	 * @return boolean true if errors found (in given payment-method or in 'global') ELSE false
	 * @public
	 */
	function isError($paymentMethod = 'all', $message = '') {
		if($message == '')
			$message = $this->errors;

		if(empty($message)) {
			return false;
		}

		$supportedPaymentMethods = array('all', 'sr', 'su', 'sv', 'sa', 'ls', 'sl', 'sf');
		if(!in_array($paymentMethod, $supportedPaymentMethods)) {
			$paymentMethod = 'all';
		}

		if($paymentMethod == 'all') {
			if(is_array($message)) {
				foreach($message as $error) {
					if(!empty($error)) {
						return true;
					}
				}
			}
		} else {
			//paymentMethod-specific search
			if(is_array($message)) {
				if((isset($message[$paymentMethod]) && !empty($message[$paymentMethod])) ||
				   (isset($message['global']) && !empty($message['global']))) {
					return true;
				}
			}
		}
		return false;
	}
	
	
	/*
	 * set Errors
	 * later use getError(), getErrors() or isError() to retrieve them
	 * @param string $msg - Detailinformationen about the error
	 * @param string $pos - Position in the errors-array, must be one of: 'global', 'sr', 'su', 'sv', 'sa', 'ls', 'sl', 'sf'
	 * @param string $errorCode - a number or string to specify the errors in the module
	 * @param string $field - if $errorCode deals with a field
	 */
	function setError($msg, $pos = 'global', $errorCode = '-1', $field = '') {
		$supportedErrorsPos = array('global', 'sr', 'su', 'sv', 'sa', 'ls', 'sl', 'sf');
		if(!in_array($pos, $supportedErrorsPos)) {
			$paymentMethod = 'global';
		}

		if ( !isset( $this->errors[$pos] ) ) {
			$this->errors[$pos] = array();
		}

		$error = array ('code' => $errorCode, 'message' => $msg, 'field' => $field);
		$this->errors[$pos][] = $error;
	}
	
	
	/**
	 * delete all warnings
	 * @public
	 */
	function deleteAllWarnings() {
		$this->errorPos = 'global';
		$this->errorCountTemp = 0;
		$this->warnings = array();
	}
	
	
	/**
	 * delete all errors
	 * @public
	 */
	function deleteAllErrors() {
		$this->errorPos = 'global';
		$this->errorCountTemp = 0;
		$this->errors = array();
	}
	
	
	/**
	 * NOT IMPLEMENTED - CURRENTLY NOT IN USE
	 * if errors-array is given to the sofortLib, it must be "casted" into the structure of $this->errors
	 * @param unknown_type $message
	 * @private
	 */
	function _parseErrorresponse($message){
		return $message;
	}
	
	
	/**
	 * internal send-method, will check http-errorcode and return body
	 * @param String $message message to post
	 * @return string error or body
	 * @private
	 */
	function _sendMessage($message) {
		$response = $this->request->post($message);
		if($response === false) {
			return $this->request->error;
		}

		$http = $this->request->getHttpCode();
		if($http['code'] != 200) {
			return $http['message'];
		}

		return $response;
	}
	
	
	/**
	 * @private
	 * define all headers here
	 */
	function _getHeaders() {
		$header = array();
		$header[] = 'Authorization: Basic '.base64_encode($this->userId.':'.$this->apiKey);
		$header[] = 'Content-Type: application/xml; charset=UTF-8';
		$header[] = 'Accept: application/xml; charset=UTF-8';
		$header[] = 'User-Agent: SofortLib-php/'.VERSION;
		$header[] = 'X-Powered-By: PHP/'.phpversion();
		return $header;
	}
	
	
	// Valuable resource
	var $_p;

	//stack of tags
	var $stack = array();

	//data of tag we're currently working on when parsing cdata
	var $currentData = '';


	/**
	 * @internal
	 * Init the XML parser
	 * @param $ns
	 * @param $encoding
	 * @param $separator
	 * @private
	 */
	function _initParser($ns=false,$encoding='UTF-8',$separator=null) {
		$this->_p = $ns	? xml_parser_create_ns($encoding,$separator) :	xml_parser_create($encoding);
		xml_set_object($this->_p, $this);
		xml_set_default_handler($this->_p,'_default');
		xml_set_element_handler($this->_p, '_tagOpen', '_tagClose');
		xml_set_character_data_handler($this->_p, '_cdata');
		xml_set_start_namespace_decl_handler($this->_p,'_nsStart');
		xml_set_end_namespace_decl_handler($this->_p,'_nsEnd');
		xml_set_external_entity_ref_handler($this->_p,'_entityRef');
		xml_set_processing_instruction_handler($this->_p,'_pi');
		xml_set_notation_decl_handler($this->_p,'_notation');
		xml_set_unparsed_entity_decl_handler($this->_p,'_unparsedEntity');
		$this->setOption(XML_OPTION_CASE_FOLDING, false);
	}
	
	
	/**
	 *
	 * Free an XML parser and set it NULL
	 * @internal
	 * @private
	 */
	function _SofortXmlBase() {
		xml_parser_free($this->_p);
		$this->_p=null;
	}
	
	
	/**
	 * @abstract
	 * @internal
	 * @private
	 * @param $parser
	 * @param $data
	 */
	function _default($parser,$data){}
	
	
	/**
	 * do something, when the given xml-tag was just opened
	 * @param unknown_type $parser
	 * @param string $tag
	 * @param unknown_type $attribs
	 * @private
	 */
	function _tagOpen($parser, $tag, $attribs) {
		$this->currentData = '';
		array_push($this->stack, $tag);
		$this->onTagOpen($tag);

		//xml-structure of errors and warnings are the same
		//we can use $this->errosPos and $this->errorsCountTemp for creating $this->errors AND $this->warnings - @see _tagClose();
		if($this->_getParentTag() == 'errors' || $this->_getParentTag() == 'warnings'){
			switch ($tag) {
				case 'sr':
				case 'su':
				case 'sv':
				case 'sa':
				case 'ls':
				case 'sl':
				case 'sf':
					$this->errorPos = $tag;
					$this->errorCountTemp = 0;
					break;
			}
		}
	}
	
	
	/**
	 * do something, when the given xml-tag was just opened - override if needed!
	 * @abstract
	 * @param string $tag
	 */
	function onTagOpen($tag) {
	}
	
	
	/**
	 * do something, when the given xml-tag was just closed
	 * saves currently all warnings and errors
	 * @param $parser
	 * @param $tag
	 * @private
	 */
	function _tagClose($parser,$tag) {
		//handle errors
		if($this->_getParentTag() == 'error') {
			$this->_createErrorarrayStructure();
			$this->errors[$this->errorPos][$this->errorCountTemp][$tag] = $this->currentData;
		}
		if($tag == 'error'){
			//following line works, if log is enabled!
			$this->logError('Error found while parsing XML: ' . print_r($this->errors[$this->errorPos][$this->errorCountTemp], true));
			$this->errorCountTemp++;
		}

		//handle warnings
		if($this->_getParentTag() == 'warning') {
			$this->_createWarningarrayStructure();
			$this->warnings[$this->errorPos][$this->errorCountTemp][$tag] = $this->currentData;
		}
		if($tag == 'warning'){
			//following line works, if log is enabled!
			$this->logWarning('Warning found while parsing XML: ' . print_r($this->warnings[$this->errorPos][$this->errorCountTemp], true));
			$this->errorCountTemp++;
		}

		$this->onParseTag($this->currentData, $tag);
		$this->currentData = '';
		array_pop($this->stack);
	}
	
	
	/**
	 * prepare $this->errors for insertion of errors
	 * @private
	 */
	function _createErrorarrayStructure() {
		if(!isset($this->errors[$this->errorPos])) {
			$this->errors[$this->errorPos] = array();
		}
		if(!isset($this->errors[$this->errorPos][$this->errorCountTemp])) {
			$this->errors[$this->errorPos][$this->errorCountTemp] = array();
		}
	}
	
	
	/**
	 * prepare $this->warnings for insertion of errors
	 * @see _createErrorsarrayStructure();
	 * @private
	 */
	function _createWarningarrayStructure() {
		if(!isset($this->warnings[$this->errorPos])) {
			$this->warnings[$this->errorPos] = array();
		}
		if(!isset($this->warnings[$this->errorPos][$this->errorCountTemp])) {
			$this->warnings[$this->errorPos][$this->errorCountTemp] = array();
		}
	}
	
	
	/**
	 * Override this callback
	 * its being called everytime we find a closing xml-tag
 	 * @protected
 	 * @abstract
	 * @param string $data data of this tag
	 * @param string $tag name of this tag
	 */
	function onParseTag($data, $tag) {}
	
	
	/**
	 * @param unknown_type $parser
	 * @param unknown_type $data
	 * @private
	 */
	function _cdata($parser,$data) {
		$this->currentData .= $data;
	}
	
	
	/**
	 * @param $parser
	 * @param $userData
	 * @param $prefix
	 * @param $uri
	 * @private
	 */
	function _nsStart($parser,$userData,$prefix,$uri) {}
	
	
	/**
	 * @abstract
	 * @param $parser
	 * @param $userData
	 * @param $prefix
	 * @private
	 */
	function _nsEnd($parser,$userData,$prefix) {}
	
	
	/**
	 * @abstract
	 * @param unknown_type $parser
	 * @param unknown_type $openEntityNames
	 * @param unknown_type $base
	 * @param unknown_type $systemID
	 * @param unknown_type $publicID
	 * @private
	 */
	function _entityRef($parser,$openEntityNames,$base,$systemID,$publicID){}
	
	
	/**
	 * @abstract
	 * @param $parser
	 * @param $target
	 * @param $data
	 * @private
	 */
	function _pi($parser,$target,$data){}
	
	
	/**
	 * @abstract
	 * @param unknown_type $parser
	 * @param unknown_type $notationName
	 * @param unknown_type $base
	 * @param unknown_type $systemID
	 * @param unknown_type $publicID
	 * @private
	 */
	function _notation($parser,	$notationName,$base,$systemID,$publicID){}
	
	
	/**
	 * @abstract
	 * @param $parser
	 * @param $entityName
	 * @param $base
	 * @param $systemID
	 * @param $publicID
	 * @param $notationName
	 * @private
	 */
	function _unparsedEntity($parser,$entityName,$base,$systemID,$publicID,$notationName){}
	
	
	/**
	 * parse xml message
	 * @private
	 * @param $data xml
	 * @param $final
	 */
	function _parse($data,$final=false) {
		if(!xml_parse($this->_p,$data,$final)) {
			//check if this looks like a XML-String and display detailed error
			if(!strpos($data, '<') === FALSE) {
				$this->fatalError($data." ".sprintf('XML error %d:"%s" at line %d column %d byte %d',
				xml_get_error_code($this->_p),
				xml_error_string($this->_p),
				xml_get_current_line_number($this->_p),
				xml_get_current_column_number($this->_p),
				xml_get_current_byte_index($this->_p)));
			}
			else {
				$this->fatalError($data." ");
			}
		}
		xml_parser_free($this->_p);
		$this->_p=null;
	}
	
	
	/**
	 * display stacktrace
	 * @private
	 * @param $provideObject
	 */
	function _backtrace($provideObject=false){
		$last = '';
		$file=__FILE__;
		$args='';
		$msg = '';
		foreach(debug_backtrace($provideObject) as $row){
			if($last!=$row['file'])
				$msg .= "File: $file<br>\n";

			$last=$row['file'];
			$msg .= ' Line: $row[line]: ';

			if($row['class']!='')
				$msg .= '$row[class]$row[type]$row[function]';
			else
				$msg .= '$row[function]';

			$msg .= '(';
			$msg .= join('', '',$args);
			$msg .= ")<br>\n";
		}
		return $msg;
	}
	
	
	/**
	 *
	 * @public
	 * @param unknown_type $msg
	 * @param unknown_type $fatal
	 */
	function error($msg,$fatal=false){
		$errorArray = array('message' => 'Error: '.$msg, 'code' => '10');
		$this->errors['global'][] = $errorArray;
	}
	
	
	/**
	 * @public
	 * error while parsing xml
	 * @param unknown_type $msg
	 */
	function fatalError($msg){
		return $this->error($msg,true);
	}
	
	
	/**
	 * set xml parser option with xml_parser_set_option()
	 * @final
	 * @param $option
	 * @param $value
	 */
	function setOption($option,$value){
		return xml_parser_set_option($this->_p,$option,$value);
	}
	
	
	/**
	 * get xml parser option
	 * @final
	 * @param $option
	 */
	function getOption($option)	{
		return xml_parser_get_option($this->_p,$option);
	}
	
	
	/**
	 * @final
	 * @param unknown_type $file
	 */
	function parseFile($file){
		if(($f=fopen($file,'r'))!=null)	{
			while(!feof($f))
				$this->_parse(fgets($f,1024));

			$this->_parseEnd();
		}
		else
			$this->fatalError('Unable to open file '.$file);
	}
	
	
	/**
	 * @private
	 */
	function _parseEnd(){
		$this->_parse(null,true);
	}
	
	
	/**
	 * @private
	 */
	function _getTag() {
		return end($this->stack);
	}
	
	
	/**
	 * @private
	 */
	function _getParentTag() {
		end($this->stack);
		return prev($this->stack);
	}
	
	
	/**
	 * convert this object to xml-string
	 * override this function
	 *
	 * @param Ambigous <array,string> $data
	 * @return string
	 * @private
	 */
	function _toXml($data) {
		$msg = '';
		if(is_array($data)) {
			foreach($data as $var => $value) {
				if(is_array($value) && array_key_exists(0, $value)) {
					$msg .= $this->_indexedArrayToXml($value, $var);
				} else {
					$msg .= '<'.$var.'>'.$this->_toXml($value).'</'.$var.'>'."\n";
				}
			}
		} else if(is_object($data)) {
			$msg = $data->toXml();
		} else {
			$msg = $data;
		}

		return $msg;
	}
	
	
	/**
	 * transforms an array into xml
	 * @private
	 * @param array|string $data content of xml-tag
	 * @param $name name of xml-tag
	 */
	function _arrayToXml($data, $name) {
		$msg = '';
		if(is_array($data)) {
			$msg .= '<'.$name.'>'."\n";
			foreach($data as $key => $var) {
				if(is_numeric($key)) {
					$msg .= $this->_arrayToXml($var, substr($name, 0, -1));
				} else if(is_array($var)) {
					$msg .= $this->_arrayToXml($var, $key);
				} else {
					$msg .= '<'.$key.'>'.$this->_escapeXML($var).'</'.$key.'>'."\n";
				}
			}
			$msg .= '</'.$name.">\n";
		} else {
				$msg .= '<'.$name.'>'.$this->_escapeXML($data).'</'.$name.'>'."\n";
		}
		return $msg;
	}
	
	
	/**
	 * escape data for embedding in xml (< and &)
	 * @private
	 * @param string $data
	 * @return string
	 */
	function _escapeXML($data) {
		$data = str_replace('&', '&amp;', $data);
		$data = str_replace('<', '&lt;', $data);
		
		return $data;
	}
	
	
	/**
	 * @private
	 * @param $data
	 * @param $name
	 */
	function _indexedArrayToXml($data, $name) {
		$msg = '';
		if(is_array($data) && count($data) > 0) {
			$msg .= '<'.$name."s>\n";
			foreach($data as $value) {
				$msg .= '<'.$name.'>'.$value.'</'.$name.'>'."\n";
			}
			$msg .= '</'.$name."s>\n";
		}
		return $msg;
	}
	
	
	/**
	 * @private
	 * @param $data
	 * @param $name
	 */
	function _indexedArrayToXmlList($data, $name) {
		$msg = '';
		if(is_array($data) && count($data) > 0) {
			foreach($data as $value) {
				$msg .= '<'.$name.'>'.$value.'</'.$name.'>'."\n";
			}
		}
		return $msg;
	}
	
	
	/**
	 * For debugging only, we don't need pretty xml for real transactions
	 * @public
	 * @param $xml
	 */
	function formatXmlString($xml) {

		// add marker linefeeds to aid the pretty-tokeniser (adds a linefeed between all tag-end boundaries)
		$xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);

		// now indent the tags
		$token      = strtok($xml, "\n");
		$result     = ''; // holds formatted version as it is built
		$pad        = 0; // initial indent
		$matches    = array(); // returns from preg_matches()

		// scan each line and adjust indent based on opening/closing tags
		while ($token !== false) {
			// test for the various tag states
			// 1. open and closing tags on same line - no change
			if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) {
				$indent=0;
			// 2. closing tag - outdent now
			} elseif (preg_match('/^<\/\w/', $token, $matches)) {
				$pad--;
			// 3. opening tag - don't pad this one, only subsequent tags
			} elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) {
				$indent=1;
			// 4. no indentation needed
			} else {
				$indent = 0;
			}

			// pad the line with the required number of leading spaces
			$line    = str_pad($token, strlen($token)+$pad, ' ', STR_PAD_LEFT);
			$result .= $line . "\n"; // add to the cumulative result, with linefeed
			$token   = strtok("\n"); // get the next token
			$pad    += $indent; // update the pad size for subsequent lines
		}
		return $result;
	}
	
	
	/**
	 * @see SofortLib setLogEnabled
	 * @deprecated
	 * @public
	 */
	function enableLog() {
		$this->enableLogging = true;
		return $this;
	}
	
	
	/**
	 * @see SofortLib setLogDisabled
	 * @deprecated
	 * @public
	 */
	function disableLog() {
		$this->enableLogging = false;
		return $this;
	}
	
	
	/**
	 * @uses enableLog();
	 * @public
	 */
	function setLogEnabled() {
		$this->enableLogging = true;
		return $this;
	}
	
	
	/**
	 * @uses disableLog();
	 * @deprecated
	 * @public
	 */
	function setLogDisabled() {
		$this->enableLogging = false;
		return $this;
	}
	
	
	/**
	 * Set the logger object
	 * @param object $logger
	 * @public
	 */
	function setLogger($logger) {
		$this->logger = $logger;
	}
	
	
	/**
	 * log the given string into warning_log.txt
	 * use $this->enableLog(); to enable logging before!
	 * @param string $msg
	 */
	function logWarning($msg) {
		if($this->enableLogging) {
			$uri = dirname(__FILE__).'/logs/warning_log.txt';
			$this->logger->log($msg, $uri);
		}
	}
	
	
	/**
	 * log the given string into error_log.txt
	 * use $this->enableLog(); to enable logging before!
	 * @param string $msg
	 */
	function logError($msg) {
		if($this->enableLogging) {
			$uri = dirname(__FILE__).'/logs/error_log.txt';
			$this->logger->log($msg, $uri);
		}
	}
	
	
	/**
	 * log the given string into log.txt
	 * use $this->enableLog(); to enable logging before!
	 * @param string $msg
	 */
	function log($msg) {
		if($this->enableLogging) {
			$uri = dirname(__FILE__).'/logs/log.txt';
			$this->logger->log($msg, $uri);
		}
	}
}
/// @endcond