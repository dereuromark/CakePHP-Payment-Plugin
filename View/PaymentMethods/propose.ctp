<h2><?php echo __('Propose %s', __('Payment Method')); ?></h2>

<div class="page form">
<?php echo $this->Form->create('PaymentMethod');?>
	<fieldset>
		 		<legend><?php echo __('Propose %s', __('Payment Method')); ?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('description', array('type'=>'textarea', 'class'=>'halfSize'));
		echo $this->Form->input('url', array('label'=>__('Website')));

		echo BR;
		echo 'Weißt du zufällig, was bei diese Zahlungsmethode pro Buchung für Gebühren anfallen?'.BR.BR;
		echo $this->Form->input('set_rate', array('after'=>' €'));
		echo $this->Form->input('rel_rate', array('after'=>' %'));

	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>

<br /><br />

<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('List %s', __('Payment Methods')), array('action' => 'index'));?></li>
	</ul>
</div>