<h2>Transaction Fee</h2>


<h3>Current Fee</h3>
<?php
if (isset($infos['paytxfee'])) {
	echo h($infos['paytxfee']);
}
?>

<h3>Change</h3>

<div class="">
<?php echo $this->Form->create('BitcoinAddress');?>
	<fieldset>
		<legend><?php echo __('Fee');?></legend>
		<?php
			echo $this->Form->input('amount', array('label'=>__('Fee')));
		?>
	</fieldset>

<?php echo $this->Form->end(__('Submit'));?>
</div>