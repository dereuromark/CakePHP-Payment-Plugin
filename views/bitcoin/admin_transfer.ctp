<h1>Transfer Bitcoins</h1>

<div style="float: right;">
<?php echo $this->Form->create('Bitcoin', array('url'=>'/'.$this->params['url']['url']));?>
<?php echo $this->Form->input('own_account_id', array('empty'=>'- pleaseSelect - '));?>
<?php echo $this->Form->end(__('Change', true));?>
</div>

<h2></h2>
<?php
if (!empty($this->data['BitcoinTransaction']['address'])) {
	echo 'Aktuelle Addresse: '.h($this->data['BitcoinTransaction']['address']).BR;
	$this->QrCode->setSize(100);
	$this->QrCode->setLevel('H');
	$string = $this->QrCode->format('bitcoin', $this->data['BitcoinTransaction']['address']);
	echo $this->QrCode->image($string);
}
?>

<div class="">
<?php echo $this->Form->create('BitcoinTransaction', array('url'=>'/'.$this->params['url']['url']));?>
	<fieldset>
		<legend><?php __('Request payment');?></legend>
		<?php
			echo $this->Form->hidden('request', array('value'=>1));
			echo $this->Form->input('address', array('options'=>$ownAddresses));
			//echo $this->Form->input('amount', array());# not supported yet
			//echo $this->Form->input('currency', array('options'=>array('BTC - Bitcoins')));
			//echo $this->Form->input('label', array()); # not supported yet
			//echo $this->Form->input('commment', array()); # not supported yet
		?>
	</fieldset>

<?php echo $this->Form->end(__('Submit',true));?>
</div>


<table><tr><td>

<h2>Move inside Wallet</h2>
<div class="">
<?php echo $this->Form->create('BitcoinTransaction', array('url'=>'/'.$this->params['url']['url']));?>
	<fieldset>
		<legend><?php __('Transfer money from one account to another');?></legend>
		<?php
if (!empty($this->data['Bitcoin']['own_account_id'])) {
	echo 'Aktueller Account: '.h($this->data['Bitcoin']['own_account_id']).BR;
}
			echo $this->Form->hidden('move', array('value'=>1));
			echo $this->Form->input('amount', array());
			echo $this->Form->error('from_account');
			//echo $this->Form->input('from_account', array());
			echo $this->Form->input('to_account', array('options'=>$ownAccounts, 'empty'=>'- pleaseSelect - '));
			echo $this->Form->input('comment', array());
		?>
	</fieldset>

<?php echo $this->Form->end(__('Submit',true));?>
</div>

</td><td>

<h2>Send to Address</h2>
<div class="">
<?php echo $this->Form->create('BitcoinTransaction', array('url'=>'/'.$this->params['url']['url']));?>
	<fieldset>
		<legend><?php __('Send Money');?></legend>
		<?php
if (isset($infos['balance'])) {
	echo __('Available', true).': ' . h($infos['balance']) . ' BTC'.BR;
}
if (!empty($this->data['Bitcoin']['own_account_id'])) {
	echo 'Aktueller Account: '.h($this->data['Bitcoin']['own_account_id']).BR;
}
if (false && !empty($this->data['BitcoinTransaction']['address'])) {
	echo 'Aktuelle Adresse: '.h($this->data['BitcoinTransaction']['address']).BR;
}
			echo $this->Form->hidden('send', array('value'=>1));
			echo $this->Form->input('amount', array());
			echo $this->Form->error('from_account');
			echo $this->Form->input('currency', array('options'=>array('BTC - Bitcoins')));
			echo $this->Form->input('to_address', array());
			echo $this->Form->input('comment', array());
			echo $this->Form->input('comment_to', array());
			if (Configure::read('Bitcoin.key')) {
				echo $this->Form->input('pwd', array('label'=>'Your Bitcoin Confirmation Key'));
			}

		?>
	</fieldset>

<?php echo $this->Form->end(__('Submit',true));?>
</div>


</td></tr></table>