<div class="page form">
<h2><?php echo __('Test %s', __('Discount')); ?></h2>

<h3>Check</h3>
<div class="discounts form">
<?php echo $this->Form->create('Discount');?>
	<fieldset>
 		<legend><?php echo __('Test %s', __('Code')); ?></legend>
	<?php
		echo $this->Form->input('amount');
		echo $this->Form->input('code');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>

<h3>Redeem</h3>

<div class="discounts form">
<?php echo $this->Form->create('Discount');?>
	<fieldset>
 		<legend><?php echo __('Redeem %s', __('Code')); ?></legend>
	<?php
		echo $this->Form->input('DiscountCode.code');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
</div>

<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('List %s', __('Discounts')), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List %s', __('Discount Codes')), array('controller' => 'discount_codes', 'action' => 'index')); ?> </li>
	</ul>
</div>

