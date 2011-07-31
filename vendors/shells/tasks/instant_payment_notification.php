<?php

class InstantPaymentNotificationTask extends Shell {

	public $timeout = 120;
	public $retries = 0;


	public function add() {
  $this->err('Queue Email Task cannot be added via Console.');
		$this->out('Please use createJob() on the QueuedTask Model to create a Proper Email Task.');
		$this->out('The Data Array should look something like this:');
		$this->out(var_export(array('settings' => array('to' => 'email@example.com', 'subject' => 'Email Subject', 'from' => 'system@example.com',
			'template' => 'sometemplate'), 'vars' => array('text' => 'hello world')), true));
	}

	public function execute() {
		$this->BitcoinAddress = ClassRegistry::init('Payment.BitcoinAddress');

		if ($this->BitcoinAddress->update()) {
			$this->log('Shell '.$this->name.' successfully completed', 'bitcoin');
			return true;
		}
		$this->log('Shell '.$this->name.' aborted', 'bitcoin');
		return false;
	}

}
