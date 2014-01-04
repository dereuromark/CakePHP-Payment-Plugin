<h2><?php echo __('Add %s', __('Payment Method')); ?></h2>

<div class="page form">
<?php echo $this->Form->create('PaymentMethod');?>
	<fieldset>
		 		<legend><?php echo __('Add %s', __('Payment Method')); ?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('alias', array('placeholder'=>'leer = automatisch'));
		echo $this->Form->input('description', array('type'=>'textarea'));
		echo $this->Form->input('duration', array());

		echo $this->Form->input('set_rate', array('after'=>' €'));
		echo $this->Form->input('rel_rate', array('after'=>' %/100'));
		echo $this->Form->input('sort');
		echo $this->Form->input('active');
		echo BR;
		echo $this->Form->input('hook', array('empty'=>'- keiner -', 'options'=>PaymentMethod::hooks()));

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