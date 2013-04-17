<h2><?php echo __('Edit %s', __('Bitcoin Address')); ?></h2>

<div class="page form">
<?php echo $this->Form->create('BitcoinAddress');?>
	<fieldset>
 		<legend><?php echo __('Edit %s', __('Bitcoin Address')); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('user_id', array('empty'=>' - [ '.__('pleaseSelect').' ] - '));
		echo $this->Form->input('model');
		echo $this->Form->input('foreign_id');
		echo $this->Form->input('address');
		echo $this->Form->input('amount');
		echo $this->Form->input('confirmations');
		echo $this->Form->input('details');
		echo $this->Form->input('status');
		echo $this->Form->input('refund_address');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>

<br /><br />

<div class="actions">
	<ul>
		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('BitcoinAddress.id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('BitcoinAddress.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List %s', __('Bitcoin Addresses')), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List %s', __('Users')), array('controller' => 'users', 'action' => 'index')); ?> </li>
	</ul>
</div>