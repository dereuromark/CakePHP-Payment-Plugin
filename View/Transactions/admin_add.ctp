<div class="page form">
<h2><?php echo __('Add %s', __('Transaction')); ?></h2>

<?php echo $this->Form->create('Transaction');?>
	<fieldset>
 		<legend><?php echo __('Add %s', __('Transaction')); ?></legend>
	<?php
		echo $this->Form->input('title');
		echo $this->Form->input('type');
		echo $this->Form->input('transaction_type');
		echo $this->Form->input('amount');
		echo $this->Form->input('fee_amount');
		echo $this->Form->input('tax_amount');
		echo $this->Form->input('currency_code');
		echo $this->Form->input('payment_type');
		echo $this->Form->input('payment_status');
		echo $this->Form->input('order_time', array('empty'=>'- -', 'dateFormat'=>'DMY', 'timeFormat'=>24));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>

<br /><br />

<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('List %s', __('Transactions')), array('action' => 'index'));?></li>
	</ul>
</div>