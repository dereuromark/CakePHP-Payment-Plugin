<div class="page form">
<h2><?php echo __('Edit %s', __('Transaction')); ?></h2>

<?php echo $this->Form->create('Transaction');?>
	<fieldset>
 		<legend><?php echo __('Edit %s', __('Transaction')); ?></legend>
	<?php
		echo $this->Form->input('id');
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
		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Transaction.id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('Transaction.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List %s', __('Transactions')), array('action' => 'index'));?></li>
	</ul>
</div>