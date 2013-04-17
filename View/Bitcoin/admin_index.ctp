<div class="index">
<h2>Bitcoin Admin Center</h2>

<h3>Infos</h3>
<?php
	echo pre($infos);
?>


<h3>Details</h3>
<?php
	echo pre($details);
?>


<h3><?php echo __('Validate Address'); ?></h3>

<div class="">
<?php echo $this->Form->create('BitcoinTransaction');?>
	<fieldset>
		<legend><?php echo __('Validate');?></legend>

		<?php
			echo $this->Form->input('address', array());
		?>
	</fieldset>

<?php echo $this->Form->end(__('Submit'));?>
</div>

</div>

<div class="actions">
<ul>
<li><?php echo $this->Html->link(__('Transactions'), array('action'=>'transactions')); ?></li>
<li><?php echo $this->Html->link(__('Transfer'), array('action'=>'transfer')); ?></li>
<li><?php echo $this->Html->link(__('Fee'), array('action'=>'fee')); ?></li>
</ul>

</div>