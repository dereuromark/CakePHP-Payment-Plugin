<div class="discountCodes view">
<h2><?php echo __('Discount Code');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Discount'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($discountCode['Discount']['name'], array('controller' => 'discounts', 'action' => 'view', $discountCode['Discount']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Code'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<div><?php echo h($discountCode['DiscountCode']['code']); ?></div>

			<?php echo $this->Discount->image($discountCode['DiscountCode']['code'])?>
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Used'); ?></dt>
		<dd>
			<?php echo $this->Format->yesNo($discountCode['DiscountCode']['used']); ?>
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Datetime->niceDate($discountCode['DiscountCode']['created']); ?>
			&nbsp;
		</dd>
<?php if ($discountCode['DiscountCode']['created'] != $discountCode['DiscountCode']['modified']) { ?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Datetime->niceDate($discountCode['DiscountCode']['modified']); ?>
			&nbsp;
		</dd>
<?php } ?>
	</dl>
</div>
<br /><br />
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('Edit %s', __('Discount Code')), array('action' => 'edit', $discountCode['DiscountCode']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete %s', __('Discount Code')), array('action' => 'delete', $discountCode['DiscountCode']['id']), null, __('Are you sure you want to delete # %s?', $discountCode['DiscountCode']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List %s', __('Discount Codes')), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('List %s', __('Discounts')), array('controller' => 'discounts', 'action' => 'index')); ?> </li>
	</ul>
</div>