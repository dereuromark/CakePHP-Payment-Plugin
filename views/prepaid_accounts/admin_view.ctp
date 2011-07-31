<div class="prepaidAccounts view">
<h1><?php  __('Prepaid Account');?></h1>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('User'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->defaultLink($prepaidAccount['User']['username'], array('controller' => 'members', 'action' => 'view', $prepaidAccount['User']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Amount'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Numeric->money($prepaidAccount['PrepaidAccount']['amount']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Datetime->niceDate($prepaidAccount['PrepaidAccount']['created']); ?>
			&nbsp;
		</dd>
<?php if ($prepaidAccount['PrepaidAccount']['created'] != $prepaidAccount['PrepaidAccount']['modified']) { ?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Datetime->niceDate($prepaidAccount['PrepaidAccount']['modified']); ?>
			&nbsp;
		</dd>
<?php } ?>
	</dl>
	
<h2><?php echo __('Logs', true); ?></h2>	
<?php
foreach ($logEntries as $logEntry) {
	echo '<li class="logEntry">';
	echo '<details><summary>'.$this->Datetime->niceDate($logEntry['Log']['created']).': '.h($logEntry['Log']['description']).'</summary>'.h($logEntry['Log']['change']).'</details>';
	echo '</li>';
}
?>	
</div>

<br /><br />

<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(sprintf(__('Edit %s', true), __('Prepaid Account', true)), array('action' => 'edit', $prepaidAccount['PrepaidAccount']['id'])); ?> </li>
		<li><?php echo $this->Html->link(sprintf(__('Delete %s', true), __('Prepaid Account', true)), array('action' => 'delete', $prepaidAccount['PrepaidAccount']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $prepaidAccount['PrepaidAccount']['id'])); ?> </li>
		<li><?php echo $this->Html->link(sprintf(__('List %s', true), __('Prepaid Accounts', true)), array('action' => 'index')); ?> </li>
	</ul>
</div>
