<div class="discounts view">
<h2><?php echo __('Discount');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo h($discount['Discount']['name']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Factor'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo h($discount['Discount']['factor']); ?>%
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Amount'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Numeric->money($discount['Discount']['amount']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Unlimited'); ?></dt>
		<dd>
			<?php echo $this->Format->yesNo($discount['Discount']['unlimited']); ?>
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Min'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Numeric->money($discount['Discount']['min']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Valid From'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Datetime->niceDate($discount['Discount']['valid_from']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Valid Until'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Datetime->niceDate($discount['Discount']['valid_until']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Details'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo pre($discount['Discount']['details']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Datetime->niceDate($discount['Discount']['created']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<br /><br />
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('Edit %s', __('Discount')), array('action' => 'edit', $discount['Discount']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete %s', __('Discount')), array('action' => 'delete', $discount['Discount']['id']), null, __('Are you sure you want to delete # %s?', $discount['Discount']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List %s', __('Discounts')), array('action' => 'index')); ?> </li>
	</ul>
</div>

<br /><br />


<div class="related view">
	<h3><?php echo __('Related %s', __('Discount Codes'));?></h3>
	<?php if (!empty($discount['DiscountCode'])):?>
	<table class="list">	<tr>
		<th><?php echo __('Code'); ?></th>
		<th><?php echo __('Used'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($discount['DiscountCode'] as $discountCode):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $discountCode['code'];?></td>
			<td><?php echo $this->Format->yesNo($discountCode['used']);?></td>
			<td><?php echo $discountCode['created'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'discount_codes', 'action' => 'view', $discountCode['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'discount_codes', 'action' => 'edit', $discountCode['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'discount_codes', 'action' => 'delete', $discountCode['id']), null, __('Are you sure you want to delete # %s?', $discountCode['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('Add %s', __('Discount Code')), array('controller' => 'discount_codes', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>