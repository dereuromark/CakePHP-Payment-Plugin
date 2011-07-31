<div class="bitcoinTransactions index">
<h1><?php __('Bitcoin Transactions');?></h1>

<table class="list">
<tr>
	<th><?php echo $this->Paginator->sort('address_id');?></th>
	<th><?php echo $this->Paginator->sort('model');?></th>
	<th><?php echo $this->Paginator->sort('foreign_id');?></th>
	<th><?php echo $this->Paginator->sort('amount');?></th>
	<th><?php echo $this->Paginator->sort('amount_expected');?></th>
	<th><?php echo $this->Paginator->sort('confirmations');?></th>
	<th><?php echo $this->Paginator->sort('details');?></th>
	<th><?php echo $this->Paginator->sort('payment_fee');?></th>
	<th><?php echo $this->Paginator->sort('status');?></th>
	<th><?php echo $this->Paginator->sort('refund_address');?></th>
	<th><?php echo $this->Paginator->sort('created');?></th>
	<th><?php echo $this->Paginator->sort('modified');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($bitcoinTransactions as $bitcoinTransaction):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $this->Html->link($bitcoinTransaction['BitcoinAddress']['address'], array('controller' => 'bitcoin_addresses', 'action' => 'view', $bitcoinTransaction['BitcoinAddress']['id'])); ?>
		</td>
		<td>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['model']); ?>
		</td>
		<td>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['foreign_id']); ?>
		</td>
		<td>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['amount']); ?>
		</td>
		<td>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['amount_expected']); ?>
		</td>
		<td>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['confirmations']); ?>
		</td>
		<td>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['details']); ?>
		</td>
		<td>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['payment_fee']); ?>
		</td>
		<td>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['status']); ?>
		</td>
		<td>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['refund_address']); ?>
		</td>
		<td>
			<?php echo $this->Datetime->niceDate($bitcoinTransaction['BitcoinTransaction']['created']); ?>
		</td>
		<td>
			<?php echo $this->Datetime->niceDate($bitcoinTransaction['BitcoinTransaction']['modified']); ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link($this->Common->icon('view'), array('action'=>'view', $bitcoinTransaction['BitcoinTransaction']['id']), array('escape'=>false)); ?>
			<?php echo $this->Html->link($this->Common->icon('edit'), array('action'=>'edit', $bitcoinTransaction['BitcoinTransaction']['id']), array('escape'=>false)); ?>
			<?php echo $this->Html->link($this->Common->icon('delete'), array('action'=>'delete', $bitcoinTransaction['BitcoinTransaction']['id']), array('escape'=>false), sprintf(__('Are you sure you want to delete # %s?', true), $bitcoinTransaction['BitcoinTransaction']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<p class="pagination">
<?php echo $this->element('pagination', array('plugin'=>'tools')); ?></p>

</div>

<br /><br />

<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(sprintf(__('Add %s', true), __('Bitcoin Transaction', true)), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(sprintf(__('List %s', true), __('Bitcoin Addresses', true)), array('controller' => 'bitcoin_addresses', 'action' => 'index')); ?> </li>
	</ul>
</div>