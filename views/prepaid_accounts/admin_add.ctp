<h1><?php printf(__('Add %s', true), __('Prepaid Account', true)); ?></h1>

<div class="prepaidAccounts form">
<?php echo $this->Form->create('PrepaidAccount');?>
	<fieldset>
 		<legend><?php printf(__('Add %s', true), __('Prepaid Account', true)); ?></legend>
	<?php
		echo $this->Form->input('user_id', array('empty'=>' - [ '.__('pleaseSelect', true).' ] - '));
		echo $this->Form->input('amount');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>

<br /><br />

<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(sprintf(__('List %s', true), __('Prepaid Accounts', true)), array('action' => 'index'));?></li>
	</ul>
</div>