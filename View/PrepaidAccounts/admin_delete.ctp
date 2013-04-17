<h2><?php echo __('Delete %s', __('Prepaid Account')); ?></h2>

<div class="page form">
<?php echo $this->Form->create('PrepaidAccount');?>

	<?php
	echo $this->element('master_password', array(), array('plugin'=>'tools'));
	?>

<?php echo $this->Form->end(__('Confirm'));?>
</div>

<br /><br />

<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('List %s', __('Prepaid Accounts')), array('action' => 'index'));?></li>
	</ul>
</div>