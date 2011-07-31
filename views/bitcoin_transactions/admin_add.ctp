<h1><?php printf(__('Add %s', true), __('Bitcoin Transaction', true)); ?></h1>

<div class="bitcoinTransactions form">
<?php echo $this->Form->create('BitcoinTransaction');?>
	<fieldset>
 		<legend><?php printf(__('Add %s', true), __('Bitcoin Transaction', true)); ?></legend>
	<?php
		echo $this->Form->input('address_id', array('empty'=>' - [ '.__('pleaseSelect', true).' ] - '));
		echo $this->Form->input('model');
		echo $this->Form->input('foreign_id');
		echo $this->Form->input('amount');
		echo $this->Form->input('amount_expected');
		echo $this->Form->input('confirmations');
		echo $this->Form->input('details');
		echo $this->Form->input('payment_fee');
		echo $this->Form->input('status');
		echo $this->Form->input('refund_address');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>

<br /><br />

<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(sprintf(__('List %s', true), __('Bitcoin Transactions', true)), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(sprintf(__('List %s', true), __('Bitcoin Addresses', true)), array('controller' => 'bitcoin_addresses', 'action' => 'index')); ?> </li>
	</ul>
</div>