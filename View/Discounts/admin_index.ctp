<div class="discounts index">
<h2><?php echo __('Discounts');?></h2>

<table class="list">
<tr>
	<th><?php echo $this->Paginator->sort('name');?></th>
	<th><?php echo $this->Paginator->sort('factor');?></th>
	<th><?php echo $this->Paginator->sort('amount');?></th>
	<th><?php echo $this->Paginator->sort('unlimited');?></th>
	<th><?php echo $this->Paginator->sort('min');?></th>
	<th><?php echo $this->Paginator->sort('validity_days');?></th>
	<th><?php echo $this->Paginator->sort('valid_from');?></th>
	<th><?php echo $this->Paginator->sort('valid_until');?></th>
	<th class="actions"><?php echo __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($discounts as $discount):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo h($discount['Discount']['name']); ?>
		</td>
		<td>
			<?php
				if ($discount['Discount']['factor']) {
					echo ($discount['Discount']['factor']).'%';
				} else {
					echo '---';
				}
			?>
		</td>
		<td>
			<?php
				if ($discount['Discount']['amount'] > 0) {
					echo $this->Numeric->price($discount['Discount']['amount']);
				} else {
					echo '---';
				}
			?>
		</td>
		<td>
			<?php echo $this->Format->yesNo($discount['Discount']['unlimited']); ?>
		</td>
		<td>
			<?php
				if ($discount['Discount']['min'] > 0) {
					echo $this->Numeric->price($discount['Discount']['min']);
				} else {
					echo '---';
				}
			?>
		</td>
		<td>
			<?php echo $discount['Discount']['validity_days']; ?>
		</td>
		<td>
			<?php echo $this->Datetime->niceDate($discount['Discount']['valid_from']); ?>
		</td>
		<td>
			<?php echo $this->Datetime->niceDate($discount['Discount']['valid_until']); ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link($this->Format->cIcon('add_code.png', __('New %s', __('Code'))), array('controller'=>'discount_codes', 'action'=>'add', $discount['Discount']['id']), array('escape'=>false)); ?>
			<?php echo $this->Html->link($this->Format->cIcon('sequence.png', __('Multiple new %s', __('Codes'))), array('controller'=>'discount_codes', 'action'=>'add_multiple', $discount['Discount']['id']), array('escape'=>false)); ?>
			<?php echo $this->Html->link($this->Format->icon('view'), array('action'=>'view', $discount['Discount']['id']), array('escape'=>false)); ?>
			<?php echo $this->Html->link($this->Format->icon('edit'), array('action'=>'edit', $discount['Discount']['id']), array('escape'=>false)); ?>
			<?php echo $this->Form->postLink($this->Format->icon('delete'), array('action'=>'delete', $discount['Discount']['id']), array('escape'=>false), __('Are you sure you want to delete # %s?', $discount['Discount']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<div class="pagination-container">
<?php echo $this->element('pagination', array(), array('plugin'=>'tools')); ?></div>

</div>
<br /><br />
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('Add %s', __('Discount')), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List %s', __('Discount Codes')), array('controller' => 'discount_codes', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('Test %s', __('Discount Codes')), array('action' => 'test')); ?> </li>
	</ul>
</div>