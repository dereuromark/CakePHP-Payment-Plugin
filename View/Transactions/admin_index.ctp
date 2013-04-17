<div class="page index">
<h2><?php echo __('Transactions');?></h2>

<table class="list">
<tr>
	<th><?php echo $this->Paginator->sort('title');?></th>
	<th><?php echo $this->Paginator->sort('type');?></th>
	<th><?php echo $this->Paginator->sort('transaction_type');?></th>
	<th><?php echo $this->Paginator->sort('amount');?></th>
	<th><?php echo $this->Paginator->sort('fee_amount');?></th>
	<th><?php echo $this->Paginator->sort('tax_amount');?></th>
	<th><?php echo $this->Paginator->sort('currency_code');?></th>
	<th><?php echo $this->Paginator->sort('payment_type');?></th>
	<th><?php echo $this->Paginator->sort('payment_status');?></th>
	<th><?php echo $this->Paginator->sort('order_time');?></th>
	<th><?php echo $this->Paginator->sort('created');?></th>
	<th class="actions"><?php echo __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($transactions as $transaction):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo h($transaction['Transaction']['title']); ?>
		</td>
		<td>
			<?php echo h($transaction['Transaction']['type']); ?>
		</td>
		<td>
			<?php echo h($transaction['Transaction']['transaction_type']); ?>
		</td>
		<td>
			<?php echo h($transaction['Transaction']['amount']); ?>
		</td>
		<td>
			<?php echo h($transaction['Transaction']['fee_amount']); ?>
		</td>
		<td>
			<?php echo h($transaction['Transaction']['tax_amount']); ?>
		</td>
		<td>
			<?php echo h($transaction['Transaction']['currency_code']); ?>
		</td>
		<td>
			<?php echo h($transaction['Transaction']['payment_type']); ?>
		</td>
		<td>
			<?php echo h($transaction['Transaction']['payment_status']); ?>
		</td>
		<td>
			<?php echo $this->Datetime->niceDate($transaction['Transaction']['order_time']); ?>
		</td>
		<td>
			<?php echo $this->Datetime->niceDate($transaction['Transaction']['created']); ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link($this->Format->icon('view'), array('action'=>'view', $transaction['Transaction']['id']), array('escape'=>false)); ?>
			<?php echo $this->Html->link($this->Format->icon('edit'), array('action'=>'edit', $transaction['Transaction']['id']), array('escape'=>false)); ?>
			<?php echo $this->Form->postLink($this->Format->icon('delete'), array('action'=>'delete', $transaction['Transaction']['id']), array('escape'=>false), __('Are you sure you want to delete # %s?', $transaction['Transaction']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('Add %s', __('Transaction')), array('action' => 'add')); ?></li>
	</ul>
</div>