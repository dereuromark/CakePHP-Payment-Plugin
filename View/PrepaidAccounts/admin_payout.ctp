<div class="page form">
<h2><?php echo __('Payout'); ?></h2>
<div style="margin-bottom: 20px;">
<?php echo __('Available')?>: <b><?php echo $this->Numeric->money($prepaidAccount['PrepaidAccount']['amount']); ?></b>
</div>

<?php echo $this->Form->create('PrepaidAccount');?>
	<fieldset>
 		<legend><?php echo __('Payout'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('amount', array('type'=>'number', 'step'=>'0.01', 'min'=>0, 'max'=>$prepaidAccount['PrepaidAccount']['amount']));
	?>
	</fieldset>
	<fieldset>
 		<legend><?php echo __('Transaction'); ?></legend>
	<?php
		echo $this->Form->input('reason', array('label'=>__('Title')));
		echo $this->Form->input('note', array('type'=>'textarea', 'rows'=>3));
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