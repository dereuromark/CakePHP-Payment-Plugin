<div class="page view">
<h2><?php echo __('Prepaid Account');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('User'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->defaultLink($prepaidAccount['User']['username'], array('controller' => 'members', 'action' => 'view', $prepaidAccount['User']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Amount'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Numeric->money($prepaidAccount['PrepaidAccount']['amount']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Datetime->niceDate($prepaidAccount['PrepaidAccount']['created']); ?>
			&nbsp;
		</dd>
<?php if ($prepaidAccount['PrepaidAccount']['created'] != $prepaidAccount['PrepaidAccount']['modified']) { ?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Datetime->niceDate($prepaidAccount['PrepaidAccount']['modified']); ?>
			&nbsp;
		</dd>
<?php } ?>
	</dl>

<h3><?php echo __('Transactions'); ?></h3>
<ul>
<?php
foreach ($transactions as $transaction) {
	echo '<li>';
	echo $this->Datetime->niceDate($transaction['Transaction']['created']).': ';
	echo '<b>'.$this->Format->ok($this->Numeric->money($transaction['Transaction']['amount'], array('signed'=>true)), $transaction['Transaction']['amount'] >= 0).'</b>';
	echo ' - '. h($transaction['Transaction']['title']).'';
	echo '</li>';
}
?>
</ul>

<h3><?php echo __('Logs'); ?></h3>
<ul>
<?php
foreach ($logEntries as $logEntry) {
	echo '<li class="logEntry">';
	echo '<details><summary>'.$this->Datetime->niceDate($logEntry['Log']['created']).': '.h($logEntry['Log']['description']).'</summary>'.h($logEntry['Log']['change']).'</details>';
	echo '</li>';
}
?>
</ul>
</div>

<br /><br />

<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('Edit %s', __('Prepaid Account')), array('action' => 'edit', $prepaidAccount['PrepaidAccount']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete %s', __('Prepaid Account')), array('action' => 'delete', $prepaidAccount['PrepaidAccount']['id']), null, __('Are you sure you want to delete # %s?', $prepaidAccount['PrepaidAccount']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List %s', __('Prepaid Accounts')), array('action' => 'index')); ?> </li>
	</ul>
</div>