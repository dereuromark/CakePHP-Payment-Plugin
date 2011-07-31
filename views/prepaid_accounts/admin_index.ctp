<div class="prepaidAccounts index">
<h1><?php __('Prepaid Accounts');?></h1>

<table class="list">
<tr>
	<th><?php echo $this->Paginator->sort('user_id');?></th>
	<th><?php echo $this->Paginator->sort('amount');?></th>
	<th><?php echo $this->Paginator->sort('created');?></th>
	<th><?php echo $this->Paginator->sort('modified');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($prepaidAccounts as $prepaidAccount):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $this->Html->defaultLink($prepaidAccount['User']['username'], array('controller' => 'members', 'action' => 'view', $prepaidAccount['User']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Numeric->money($prepaidAccount['PrepaidAccount']['amount']); ?>
		</td>
		<td>
			<?php echo $this->Datetime->niceDate($prepaidAccount['PrepaidAccount']['created']); ?>
		</td>
		<td>
			<?php echo $this->Datetime->niceDate($prepaidAccount['PrepaidAccount']['modified']); ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link($this->Common->icon('view'), array('action'=>'view', $prepaidAccount['PrepaidAccount']['id']), array('escape'=>false)); ?>
			<?php echo $this->Html->link($this->Common->icon('edit'), array('action'=>'edit', $prepaidAccount['PrepaidAccount']['id']), array('escape'=>false)); ?>
			<?php echo $this->Html->link($this->Common->icon('delete'), array('action'=>'delete', $prepaidAccount['PrepaidAccount']['id']), array('escape'=>false), sprintf(__('Are you sure you want to delete # %s?', true), $prepaidAccount['PrepaidAccount']['id'])); ?>
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
		<li><?php echo $this->Html->link(sprintf(__('Add %s', true), __('Prepaid Account', true)), array('action' => 'add')); ?></li>
	</ul>
</div>