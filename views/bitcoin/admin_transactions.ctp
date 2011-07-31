<div class="index">

<h1>Bitcoin Transactions</h1>

<div style="float: right;">
<?php echo $this->Form->create('Bitcoin', array('url'=>'/'.$this->params['url']['url']));?>
<?php echo $this->Form->input('account', array('empty'=>'- pleaseSelect - '));?>
<?php echo $this->Form->input('category', array('empty'=>'- all - ', 'options' => array('receive'=>'incoming', 'send'=>'outgoing', 'move' => 'moved')));?>
<?php echo $this->Form->button(__('Change', true));?>
<?php echo $this->Form->end();?>
</div>



<h2>All</h2>
<?php
	echo pre($transactions);
?>

</div>