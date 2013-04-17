<div class="page index">
<h2><?php echo __('Payment Methods');?></h2>

<table class="list"><tr>
	<th><?php echo $this->Paginator->sort('name');?></th>
	<th><?php echo $this->Paginator->sort('set_rate');?></th>
	<th><?php echo $this->Paginator->sort('rel_rate');?></th>
	<th><?php echo $this->Paginator->sort('description');?></th>

	<th><?php echo $this->Paginator->sort('active');?></th>
	<th><?php echo $this->Paginator->sort('created');?></th>
	<th><?php echo $this->Paginator->sort('modified');?></th>
	<th class="actions"><?php echo __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($paymentMethods as $paymentMethod):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo h($paymentMethod['PaymentMethod']['name']); ?>
			<br />
			<?php if (!empty($paymentMethod['PaymentMethod']['alias'])) {
				echo '<small>('.h($paymentMethod['PaymentMethod']['alias']).')</small>';
			} ?>
		</td>
		<td>
			<?php
			if ($paymentMethod['PaymentMethod']['set_rate'] == 0) {
				echo '---';
			} else {
				echo $this->Numeric->format($paymentMethod['PaymentMethod']['set_rate'], null, array('currency'=>true));
			}	?>
		</td>
		<td>
			<?php if ($paymentMethod['PaymentMethod']['rel_rate'] == 0) {
				echo '---';
			} else {
				echo $this->Numeric->format($paymentMethod['PaymentMethod']['rel_rate']*100).'%';
			} ?>
		</td>
		<td>
			<?php echo h($paymentMethod['PaymentMethod']['description']); ?>
		</td>
			<td>
			<?php echo $this->Format->yesNo($paymentMethod['PaymentMethod']['active']); ?>
		</td>
		<td>
			<?php echo $this->Datetime->niceDate($paymentMethod['PaymentMethod']['created']); ?>
		</td>
		<td>
			<?php echo $this->Datetime->niceDate($paymentMethod['PaymentMethod']['modified']); ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link($this->Format->icon('view'), array('action'=>'view', $paymentMethod['PaymentMethod']['id']), array('escape'=>false)); ?>
			<?php echo $this->Html->link($this->Format->icon('edit'), array('action'=>'edit', $paymentMethod['PaymentMethod']['id']), array('escape'=>false)); ?>
			<?php echo $this->Form->postLink($this->Format->icon('delete'), array('action'=>'delete', $paymentMethod['PaymentMethod']['id']), array('escape'=>false), __('Are you sure you want to delete # %s?', $paymentMethod['PaymentMethod']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>

<div class="pagination-container">
<?php echo $this->element('pagination', array(), array('plugin'=>'tools')); ?></div>

</div>

<br /><br />
Negative Werte sind Gutschriften/Rabatte, positive Werte sind Abzüge/Kosten.

<br /><br />

<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('Add %s', __('Payment Method')), array('action' => 'add')); ?></li>
	</ul>
</div>