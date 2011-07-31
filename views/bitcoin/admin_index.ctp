<h1>Bitcoin Admin Center</h1>
<div class="index">

<h2>Infos</h2>
<?php
	echo pre($infos);
?>


<h2>Details</h2>
<?php
	echo pre($details);
?>


<h2><?php echo __('Validate Address', true); ?></h2>

<div class="">
<?php echo $this->Form->create('BitcoinTransaction', array('url'=>'/'.$this->params['url']['url']));?>
	<fieldset>
		<legend><?php __('Validate');?></legend>

		<?php
			echo $this->Form->input('address', array());
		?>
	</fieldset>

<?php echo $this->Form->end(__('Submit',true));?>
</div>

</div>

<div class="actions">
<ul>
<li><?php echo $this->Html->link(__('Transactions', true), array('action'=>'transactions')); ?></li>
<li><?php echo $this->Html->link(__('Transfer', true), array('action'=>'transfer')); ?></li>
<li><?php echo $this->Html->link(__('Fee', true), array('action'=>'fee')); ?></li>
</ul>

</div>