<div class="discountCodes form">
<h2><?php echo h($discount['Discount']['name']); ?></h2>

<?php if (!empty($codes)) { ?>
<h3><?php echo __('%s Codes generated', count($codes)); ?></h3>

<?php if (false) { ?>
<div style="float: right;">
<?php echo $this->Html->link($this->Format->cIcon('export_document.gif', __('Export')), array('action'=>'add_multiple', 'ext'=>'csv', $discount['Discount']['id'], slug($discount['Discount']['name'])), array('escape'=>false)); ?>
<br />
other formats?
</div>
<?php } ?>

<pre style="padding: 10px; padding-left: 30px;">
<?php foreach ($codes as $code) { ?>
<?php echo $code['DiscountCode']['code'].PHP_EOL; ?>
<?php } ?>
</pre>

<?php } ?>

<h3><?php echo __('Add multiple %s', __('Discount Codes')); ?></h3>
<?php echo $this->Form->create('DiscountCode');?>
	<fieldset>
 		<legend><?php echo __('New %s', __('Discount Codes')); ?></legend>
	<?php
		echo $this->Form->input('quantity');
		//echo $this->Form->input('code');

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