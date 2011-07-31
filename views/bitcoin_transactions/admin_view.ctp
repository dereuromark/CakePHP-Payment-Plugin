<div class="bitcoinTransactions view">
<h1><?php  __('Bitcoin Transaction');?></h1>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Bitcoin Address'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($bitcoinTransaction['BitcoinAddress']['address'], array('controller' => 'bitcoin_addresses', 'action' => 'view', $bitcoinTransaction['BitcoinAddress']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Model'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['model']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Foreign Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['foreign_id']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Amount'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['amount']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Amount Expected'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['amount_expected']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Confirmations'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['confirmations']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Details'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['details']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Payment Fee'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['payment_fee']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Status'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['status']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Refund Address'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['refund_address']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Datetime->niceDate($bitcoinTransaction['BitcoinTransaction']['created']); ?>
			&nbsp;
		</dd>
<?php if ($bitcoinTransaction['BitcoinTransaction']['created'] != $bitcoinTransaction['BitcoinTransaction']['modified']) { ?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Datetime->niceDate($bitcoinTransaction['BitcoinTransaction']['modified']); ?>
			&nbsp;
		</dd>
<?php } ?>
	</dl>
</div>

<br /><br />

<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(sprintf(__('Edit %s', true), __('Bitcoin Transaction', true)), array('action' => 'edit', $bitcoinTransaction['BitcoinTransaction']['id'])); ?> </li>
		<li><?php echo $this->Html->link(sprintf(__('Delete %s', true), __('Bitcoin Transaction', true)), array('action' => 'delete', $bitcoinTransaction['BitcoinTransaction']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $bitcoinTransaction['BitcoinTransaction']['id'])); ?> </li>
		<li><?php echo $this->Html->link(sprintf(__('List %s', true), __('Bitcoin Transactions', true)), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(sprintf(__('List %s', true), __('Bitcoin Addresses', true)), array('controller' => 'bitcoin_addresses', 'action' => 'index')); ?> </li>
	</ul>
</div>
