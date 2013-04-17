<div class="page view">
<h2><?php echo __('Bitcoin Transaction');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Bitcoin Address'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($bitcoinTransaction['BitcoinAddress']['address'], array('controller' => 'bitcoin_addresses', 'action' => 'view', $bitcoinTransaction['BitcoinAddress']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Model'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['model']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Foreign Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['foreign_id']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Amount'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['amount']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Amount Expected'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['amount_expected']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Confirmations'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['confirmations']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Details'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['details']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Payment Fee'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['payment_fee']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Status'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['status']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Refund Address'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo h($bitcoinTransaction['BitcoinTransaction']['refund_address']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Datetime->niceDate($bitcoinTransaction['BitcoinTransaction']['created']); ?>
			&nbsp;
		</dd>
<?php if ($bitcoinTransaction['BitcoinTransaction']['created'] != $bitcoinTransaction['BitcoinTransaction']['modified']) { ?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Modified'); ?></dt>
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
		<li><?php echo $this->Html->link(__('Edit %s', __('Bitcoin Transaction')), array('action' => 'edit', $bitcoinTransaction['BitcoinTransaction']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete %s', __('Bitcoin Transaction')), array('action' => 'delete', $bitcoinTransaction['BitcoinTransaction']['id']), null, __('Are you sure you want to delete # %s?', $bitcoinTransaction['BitcoinTransaction']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List %s', __('Bitcoin Transactions')), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('List %s', __('Bitcoin Addresses')), array('controller' => 'bitcoin_addresses', 'action' => 'index')); ?> </li>
	</ul>
</div>