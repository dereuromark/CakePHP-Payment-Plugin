<div class="page index">

<div class="filterBox" style="float: right;">
<?php
echo $this->Form->create('PrepaidAccount');
echo $this->Form->input('user_id', array('empty'=>'- '.__('noRestriction').' -'));
echo $this->Form->submit(__('Search'), array());
echo $this->Form->end();
?>
</div>

<h2><?php echo __('Prepaid Accounts');?></h2>

<table class="list">
<tr>
	<th><?php echo $this->Paginator->sort('user_id');?></th>
	<th><?php echo $this->Paginator->sort('amount');?></th>
	<th><?php echo $this->Paginator->sort('created');?></th>
	<th><?php echo $this->Paginator->sort('modified');?></th>
	<th class="actions"><?php echo __('Actions');?></th>
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
			<?php echo $this->Html->defaultLink($prepaidAccount['User'][$userDisplayField], array('plugin'=>false, 'controller' => 'members', 'action' => 'view', $prepaidAccount['User']['id'])); ?>
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
			<?php echo $this->Html->link($this->Format->icon('view'), array('action'=>'view', $prepaidAccount['PrepaidAccount']['id']), array('escape'=>false)); ?>
			<?php echo $this->Html->link($this->Format->cIcon(ICON_FINANCIAL, __('Payout')), array('action'=>'payout', $prepaidAccount['PrepaidAccount']['id']), array('escape'=>false)); ?>
			<?php echo $this->Html->link($this->Format->icon('edit'), array('action'=>'edit', $prepaidAccount['PrepaidAccount']['id']), array('escape'=>false)); ?>
			<?php
			if (Configure::read('MasterPassword.password')) {
				echo $this->Html->link($this->Format->icon('delete'), array('action'=>'delete', $prepaidAccount['PrepaidAccount']['id']), array('escape'=>false));
			} else {
				echo $this->Form->postLink($this->Format->icon('delete'), array('action'=>'delete', $prepaidAccount['PrepaidAccount']['id']), array('escape'=>false), __('Are you sure you want to delete # %s?', $prepaidAccount['PrepaidAccount']['id']));
			}
			?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<div class="pagination-container">
<?php echo $this->element('pagination', array(), array('plugin'=>'tools')); ?></div>

</div>

<br /><br />

<?php
$userId = null;
if (!empty($this->request->data['PrepaidAccount']['user_id'])) {
	if (array_key_exists($this->request->data['PrepaidAccount']['user_id'], $users)) {
		$userId = $this->request->data['PrepaidAccount']['user_id'];
	}
}

?>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('Validate %s', __('Prepaid Accounts')), array('action' => 'validate')); ?></li>
		<li><?php echo $this->Html->link(__('Add %s', __('Prepaid Account')), array('action' => 'add', $userId)); ?></li>
	</ul>
</div>