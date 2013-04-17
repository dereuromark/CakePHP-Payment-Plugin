<div class="discountCodes form">
<h2><?php echo __('Add %s', __('Discount Code')); ?></h2>
<?php echo $this->Form->create('DiscountCode');?>
	<fieldset>
 		<legend><?php echo __('Add %s', __('Discount Code')); ?></legend>
		<?php
		echo $this->Form->input('discount_id', array('empty'=>' - [ '.__('pleaseSelect').' ] - '));
		echo $this->Form->input('code');
		echo $this->Form->input('details');
		?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<br /><br />
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('List %s', __('Discount Codes')), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List %s', __('Discounts')), array('controller' => 'discounts', 'action' => 'index')); ?> </li>
	</ul>
</div>