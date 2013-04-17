<?php
/// \cond
/**
 * A basic implementation of logging mechanism intended for debugging
 *
 * Copyright (c) 2011 Payment Network AG
 *
 * $Date: 2011-11-30 10:40:45 +0100 (Wed, 30 Nov 2011) $
 * @version SofortLib 1.3.0  $Id: sofortLib_Logger.inc.php 2418 2011-11-30 09:40:45Z dehn $
 * @author Payment Network AG http://www.payment-network.com (integration@sofort.com)
 *
 */
class SofortLibLogger {

	var $fp = null;
	var $maxFilesize = 1048576;
	var $useCompression = true;

	function SofortLibLogger() {
		// intentionally left empty
	}
	
	
	/**
	 * Logs $msg to a file which path is being set by it's unified ressource locator
	 * @param String $msg
	 * @param URI $uri
	 */
	function log($msg, $uri) {
		if($this->logRotate($uri)) {
			$this->fp = fopen($uri, 'w');
			fclose($this->fp);
		}
		$this->fp = fopen($uri, 'a');
		fwrite($this->fp,  '['.date('Y-m-d H:i:s').'] ' . $msg . "\n");
		fclose($this->fp);
	}
	
	
	/**
	 * Copy the content of the logfile to a backup file if file size got too large
	 * Put the old log file into a tarball for later reference
	 * @param URI $uri
	 */
	function logRotate($uri) {
		$date = date('Y-m-d_h-i-s', time());
		if(file_exists($uri)) {
			if($this->fp != null && filesize($uri) != false && filesize($uri) >= $this->maxFilesize) {
				$oldUri = $uri;
				// file ending
				$ending = $ext = pathinfo($oldUri, PATHINFO_EXTENSION);
				$newUri = dirname($oldUri).'/log_'.$date.'.'.$ending;
				rename($oldUri, $newUri);
				if(file_exists($oldUri)) {
					unlink($oldUri);
				}
				if($this->useCompression) {
					$cmd = 'tar -zvcf '.dirname($oldUri).'/log_'.$date.'.tar '.$newUri;
					$retVal = '';
					$ret = system($cmd, $retVal);
					if(!empty($ret))unlink($newUri);
				}
				return true;
			}
		}
		return false;
	}

}
/// \endcond