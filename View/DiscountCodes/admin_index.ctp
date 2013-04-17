<div class="discountCodes index">
<h2><?php echo __('Discount Codes');?></h2>

<table class="list">
<tr>
	<th><?php echo $this->Paginator->sort('discount_id');?></th>
	<th><?php echo $this->Paginator->sort('code');?></th>
	<th><?php echo $this->Paginator->sort('used');?></th>
	<th><?php echo $this->Paginator->sort('details');?></th>
	<th><?php echo $this->Paginator->sort('created');?></th>
	<th><?php echo $this->Paginator->sort('modified');?></th>
	<th class="actions"><?php echo __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($discountCodes as $discountCode):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $this->Html->link($discountCode['Discount']['name'], array('controller' => 'discounts', 'action' => 'view', $discountCode['Discount']['id'])); ?>
		</td>
		<td>
			<?php echo h($discountCode['DiscountCode']['code']); ?>
		</td>
		<td>
			<?php echo $this->Format->yesNo($discountCode['DiscountCode']['used']); ?>
		</td>
		<td>
			<?php echo h($discountCode['DiscountCode']['details']); ?>
		</td>
		<td>
			<?php echo $this->Datetime->niceDate($discountCode['DiscountCode']['created']); ?>
		</td>
		<td>
			<?php echo $this->Datetime->niceDate($discountCode['DiscountCode']['modified']); ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link($this->Format->icon('view'), array('action'=>'view', $discountCode['DiscountCode']['id']), array('escape'=>false)); ?>
			<?php echo $this->Form->postLink($this->Format->icon('delete'), array('action'=>'delete', $discountCode['DiscountCode']['id']), array('escape'=>false), __('Are you sure you want to delete # %s?', $discountCode['DiscountCode']['id'])); ?>
			<?php echo $this->Html->link($this->Format->icon('edit'), array('action'=>'edit', $discountCode['DiscountCode']['id']), array('escape'=>false)); ?>
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
		<li><?php echo $this->Html->link(__('Add %s', __('Discount Code')), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List %s', __('Discounts')), array('controller' => 'discounts', 'action' => 'index')); ?> </li>
	</ul>
</div>