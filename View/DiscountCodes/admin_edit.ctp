<div class="discountCodes form">
<h2><?php echo __('Edit %s', __('Discount Code')); ?></h2>
<?php echo $this->Form->create('DiscountCode');?>
	<fieldset>
 		<legend><?php echo __('Edit %s', __('Discount Code')); ?></legend>
	<?php
		echo $this->Form->input('id');

		echo $this->Form->input('code');
		echo $this->Form->input('used');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<br /><br />
<div class="actions">
	<ul>
		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('DiscountCode.id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('DiscountCode.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List %s', __('Discount Codes')), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List %s', __('Discounts')), array('controller' => 'discounts', 'action' => 'index')); ?> </li>
	</ul>
</div>