<?php
/// \cond
/**
 * Encapsulates communication via HTTP
 *
 * requires libcurl and openssl
 *
 * Copyright (c) 2011 Payment Network AG
 *
 * $Date: 2011-11-30 10:40:45 +0100 (Wed, 30 Nov 2011) $
 * @version SofortLib 1.3.0  $Id: sofortLib_http.inc.php 2418 2011-11-30 09:40:45Z dehn $
 * @author Payment Network AG http://www.payment-network.com (integration@sofort.com)
 * @internal
 *
 */
class SofortLib_Http {
	var $headers;
	var $compression;
	var $proxy;
	var $url;
	var $info;
	var $error;

	var $httpStatus = 200;
	var $response = '';


	function SofortLib_Http($url, $headers, $compression=FALSE, $proxy='') {
		$this->url = $url;
		$this->headers = $headers;
		$this->compression=$compression;
		$this->proxy=$proxy;
	}


	function getinfo($opt = '') {
		if(!empty($opt)) {
			return $this->info[$opt];
		}
		else {
			return $this->info;
		}
	}


	/**
	 * send data to server with POST request
	 * @param string $data
	 * @param string $url
	 * @param string $headers
	 */
	function post($data, $url=FALSE, $headers=FALSE) {
		if(function_exists('curl_init')) {
			return $this->postCurl($data, $url, $headers);
		} else {
			return $this->postSocket($data, $url, $headers);
		}
	}


	/**
	 * post data using curl
	 * @param string $data
	 * @param string $url
	 * @param string $headers
	 */
	function postCurl($data, $url=FALSE, $headers=FALSE) {
		if($url === FALSE) $url = $this->url;
		if($headers === FALSE) $headers = $this->headers;
		$process = curl_init($url);
		curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($process, CURLOPT_POST, 1);
		curl_setopt($process, CURLOPT_HEADER, 0);
		if($this->compression !== FALSE) curl_setopt($process, CURLOPT_ENCODING , $this->compression);
		curl_setopt($process, CURLOPT_TIMEOUT, 15);
		if ($this->proxy) curl_setopt($process, CURLOPT_PROXY, $this->proxy);
		curl_setopt($process, CURLOPT_POSTFIELDS, $data);
		curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt($process, CURLOPT_SSL_VERIFYHOST, 0);
    	curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
		//curl_setopt($process, CURLOPT_SSL_VERIFYHOST, 2);
    	//curl_setopt($process, CURLOPT_SSL_VERIFYPEER, true);
    	//curl_setopt($process, CURLOPT_CAINFO, 'payment-network.com.crt');
		$return = curl_exec($process);

		$this->info = curl_getinfo($process);
		$this->error = curl_error($process);

		$this->httpStatus = $this->info['http_code'];
		$this->response = $return;

		curl_close($process);
		return $return;
	}


	/**
	 *
	 * HTTP error handling
	 * @return array(code, message, response[if available])
	 */
	function getHttpCode() {
		switch($this->httpStatus) {
			case(200):
				return array('code' => 200, 'message' => 'OK', 'response' => $this->response);
			break;
			case(401):
				$this->error = 'Unauthorized';
				return array('code' => 401, 'message' => 'Unauthorized', 'response' => $this->response);
			break;
			case(0):
			case(404):
				$this->httpStatus = 404;
				$this->error = 'URL not found '.$this->url;
				return array('code' => 404, 'message' => 'URL not found '.$this->url, 'response' => '');
			break;
			case(500):
				$this->error = 'An error occurred';
				return array('code' => 500, 'message' => 'An error occurred', 'response' => $this->response);
			break;
			default:
				return array('code' => 404, 'message' => 'URL not found '.$this->url, 'response' => $this->response);
			break;
		}
		return array('code' => 404, 'message' => 'Something went wrong');
	}


	/**
	 *
	 * Enter description here ...
	 */
	function getHttpStatusCode() {
		$status = $this->getHttpCode();
		return $status['code'];
	}


	function getHttpStatusMessage() {
		$status = $this->getHttpCode();
		return $status['message'];
	}


	/**
	 * this is a fallback with fsockopen if curl is not activated
	 * we still need openssl and ssl wrapper support (PHP >= 4.3.0)
	 * @param string $data
	 * @param string $url
	 * @param array $headers
	 * @return string body
	 */
	function postSocket($data, $url=FALSE, $headers=FALSE) {
		if($url === FALSE) $url = $this->url;
		if($headers === FALSE) $headers = $this->headers;
		$uri = parse_url($url);

		$out = 'POST '.$uri['path'].' HTTP/1.1'."\r\n";
		$out .= 'HOST: '.$uri['host']." \r\n";
		$out .= 'Content-Length: '. strlen($data)."\r\n";
		foreach ($headers as $header) {
			$out .= $header . "\r\n";
		}
		$out .= "\r\n".$data;

		//connect to webservice
		if (!$fp = fsockopen('ssl://'.$uri['host'], 443, $errno, $errstr, 15)) {
			$this->error = 'fsockopen() failed, enable ssl and curl: '. $errno . ' ' . $errstr;
			return false;
    	}

    	//send data
    	stream_set_timeout($fp, 15);
    	fwrite($fp, $out);

   		//read response
    	$return = '';
    	while (!feof($fp)) {
	        $return .= fgets($fp, 512);
	    }

    	fclose($fp);

    	//split header/body
    	preg_match('#^(.+?)\r\n\r\n(.*)#ms', $return, $body);
    	//get statuscode
    	preg_match('#HTTP/1.*([0-9]{3}).*#i', $body[1], $status);
    	$this->info['http_code'] = $status[1];
    	$this->httpStatus = $status[1];
    	return $body[2];
	}

	
	function error($error) {
		echo '<center><div style="width:500px;border: 3px solid #FFEEFF; padding: 3px; background-color: #FFDDFF;font-family: verdana; font-size: 10px"><b>cURL Error</b><br>'.$error.'</div></center>';
		die;
	}
}
/// \endcond