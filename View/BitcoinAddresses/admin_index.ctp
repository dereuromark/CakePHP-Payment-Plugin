<div class="page index">
<h2><?php echo __('Bitcoin Addresses');?></h2>

<table class="list">
<tr>
	<th><?php echo $this->Paginator->sort('user_id');?></th>
	<th><?php echo $this->Paginator->sort('model');?></th>
	<th><?php echo $this->Paginator->sort('foreign_id');?></th>
	<th><?php echo $this->Paginator->sort('address');?></th>
	<th><?php echo $this->Paginator->sort('amount');?></th>
	<th><?php echo $this->Paginator->sort('confirmations');?></th>
	<th><?php echo $this->Paginator->sort('details');?></th>
	<th><?php echo $this->Paginator->sort('status');?></th>
	<th><?php echo $this->Paginator->sort('refund_address');?></th>
	<th><?php echo $this->Paginator->sort('created');?></th>
	<th><?php echo $this->Paginator->sort('modified');?></th>
	<th class="actions"><?php echo __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($bitcoinAddresses as $bitcoinAddress):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $this->Html->link($bitcoinAddress['User']['username'], array('controller' => 'users', 'action' => 'view', $bitcoinAddress['User']['id'])); ?>
		</td>
		<td>
			<?php echo h($bitcoinAddress['BitcoinAddress']['model']); ?>
		</td>
		<td>
			<?php echo h($bitcoinAddress['BitcoinAddress']['foreign_id']); ?>
		</td>
		<td>
			<?php echo h($bitcoinAddress['BitcoinAddress']['address']); ?>
		</td>
		<td>
			<?php echo h($bitcoinAddress['BitcoinAddress']['amount']); ?>
		</td>
		<td>
			<?php echo h($bitcoinAddress['BitcoinAddress']['confirmations']); ?>
		</td>
		<td>
			<?php echo h($bitcoinAddress['BitcoinAddress']['details']); ?>
		</td>
		<td>
			<?php echo h($bitcoinAddress['BitcoinAddress']['status']); ?>
		</td>
		<td>
			<?php echo h($bitcoinAddress['BitcoinAddress']['refund_address']); ?>
		</td>
		<td>
			<?php echo $this->Datetime->niceDate($bitcoinAddress['BitcoinAddress']['created']); ?>
		</td>
		<td>
			<?php echo $this->Datetime->niceDate($bitcoinAddress['BitcoinAddress']['modified']); ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link($this->Format->icon('view'), array('action'=>'view', $bitcoinAddress['BitcoinAddress']['id']), array('escape'=>false)); ?>
			<?php echo $this->Html->link($this->Format->icon('edit'), array('action'=>'edit', $bitcoinAddress['BitcoinAddress']['id']), array('escape'=>false)); ?>
			<?php echo $this->Form->postLink($this->Format->icon('delete'), array('action'=>'delete', $bitcoinAddress['BitcoinAddress']['id']), array('escape'=>false), __('Are you sure you want to delete # %s?', $bitcoinAddress['BitcoinAddress']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('Add %s', __('Bitcoin Address')), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List %s', __('Users')), array('controller' => 'users', 'action' => 'index')); ?> </li>
	</ul>
</div>