<div class="discounts form">
<h2><?php echo __('Edit %s', __('Discount')); ?></h2>
<?php echo $this->Form->create('Discount');?>
	<fieldset>
 		<legend><?php echo __('Edit %s', __('Discount')); ?></legend>
		<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('validity_days');
		echo $this->Form->input('factor');
		echo $this->Form->input('amount');
		echo $this->Form->input('unlimited');
		echo $this->Form->input('min', array('type'=>'number', 'label'=>__('Minimum Order Value')));

		echo $this->Form->datetime('valid_from', array('dateFormat'=>'DMY', 'timeFormat'=>24, 'empty'=>'--'));
		echo $this->Form->datetime('valid_until', array('dateFormat'=>'DMY', 'timeFormat'=>24, 'empty'=>'--'));
		//echo $this->Form->input('model');
		//echo $this->Form->input('foreign_id');

		echo $this->Form->input('details');
		?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<br /><br />
<div class="actions">
	<ul>
		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Discount.id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('Discount.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List %s', __('Discounts')), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List %s', __('Discount Codes')), array('controller' => 'discount_codes', 'action' => 'index')); ?> </li>
	</ul>
</div>