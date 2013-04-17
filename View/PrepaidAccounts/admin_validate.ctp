<div class="page index">
<h2><?php echo __('Validate %s', __('Prepaid Accounts'))?></h2>
<?php echo $stats['ok']; ?> OK, <?php echo $stats['error']; ?> ERRORS

<ul>
<?php
foreach ($prepaidAccounts as $prepaidAccount) {
	echo '<li style="margin-top: 4px; margin-bottom: 4px;">';
	echo $this->Format->yesNo($prepaidAccount['PrepaidAccount']['validates']).' ';
	echo $this->Html->link($prepaidAccount['User']['username'], array('plugin'=>false, 'controller' => 'members', 'action' => 'view', $prepaidAccount['User']['id'])).': ';
	echo $this->Numeric->money($prepaidAccount['PrepaidAccount']['amount']);

	if (!$prepaidAccount['PrepaidAccount']['validates']) {
		echo '<div>'.__('Actual value').': ';
		echo '<b>'.$this->Numeric->money($prepaidAccount['PrepaidAccount']['transaction_amount']).'</b>';

		echo ' '.$this->Form->postLink($this->Format->cIcon('update.png', __('Repair')), array('action'=>'repair', $prepaidAccount['PrepaidAccount']['id']), array('escape'=>false), __('Sure?'));

		echo '</div>';
	}

	echo '</li>';
	//$t = $transactions[$prepaidAccount['PrepaidAccount']['id']];



}
?>
</ul>

</div>

<br /><br />

<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('List %s', __('Prepaid Accounts')), array('action' => 'index')); ?></li>
	</ul>
</div>