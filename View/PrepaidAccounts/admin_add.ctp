<h2><?php echo __('Add %s', __('Prepaid Account')); ?></h2>

<div class="page form">
<?php echo $this->Form->create('PrepaidAccount');?>
	<fieldset>
 		<legend><?php echo __('Prepaid Account'); ?></legend>
	<?php
		echo $this->Form->input('user_id', array('empty'=>' - [ '.__('pleaseSelect').' ] - '));
		echo $this->Form->input('amount');
	?>
	</fieldset>
	<fieldset>
 		<legend><?php echo __('Transaction'); ?></legend>
	<?php
		echo $this->Form->input('reason');
	?>
	</fieldset>

	<?php
	if (Configure::read('MasterPassword.password')) {
		echo $this->element('master_password', array(), array('plugin'=>'tools'));
	}
	?>

<?php echo $this->Form->end(__('Submit'));?>
</div>

<br /><br />

<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('List %s', __('Prepaid Accounts')), array('action' => 'index'));?></li>
	</ul>
</div>