<h1>Transaction Fee</h1>


<h2>Current Fee</h2>
<?php
if (isset($infos['paytxfee'])) {
	echo h($infos['paytxfee']);
}
?>

<h2>Change</h2>

<div class="">
<?php echo $this->Form->create('BitcoinAddress', array('url'=>'/'.$this->params['url']['url']));?>
	<fieldset>
		<legend><?php __('Fee');?></legend>
		<?php
			echo $this->Form->input('amount', array('label'=>__('Fee', true)));
		?>
	</fieldset>

<?php echo $this->Form->end(__('Submit',true));?>
</div>
