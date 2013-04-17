<div class="page view">
<h2><?php echo __('Transaction');?></h2>
	<h3><?php echo h($transaction['Transaction']['title']); ?></h3>

Aktueller Status: <b><?php echo Transaction::statuses($transaction['Transaction']['payment_status']); ?></b>


	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Order Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Datetime->niceDate($transaction['Transaction']['created']); ?>
			&nbsp;
		</dd>
	</dl>

<?php
if ($transaction['Transaction']['type'] == 'bank_transfer') {
?>
<?php echo $this->Form->create('Transaction');?>
<fieldset>
	<legend>Bitte die Überweisung ausführen, sofern noch nicht geschehen</legend>
	Nach erfolgreicher Überweisung kann mit der Bestätigung fortgefahren werden.<br />
	Sobald die Zahlung bei uns verbucht werden kann, wist du darüber benachrichtigt.<br />
	Es kann bei Banküberweisungen bis zu 2 Werktage dauern, bis das Geld tatsächlich transferiert wurde.

	<dl>
		<dt>Bankverbindung</dt>
		<dd>
			<?php echo Configure::read('BankTransfer.accountName')?>
			<br />
			<?php echo __('accountNumberShort')?> <?php echo Configure::read('BankTransfer.accountNumber')?>
			<br />
			<?php echo __('sortCodeShort')?> <?php echo Configure::read('BankTransfer.sortCode')?>
			<br />
			<?php echo Configure::read('BankTransfer.bankName')?>
		</dd>

		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Amount'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<b><?php echo $this->Numeric->money($transaction['Transaction']['amount']); ?></b>
			(<?php echo h($transaction['Transaction']['currency_code']); ?>)
		</dd>

		<dt>Betreff</dt>
		<dd>T<?php echo str_pad($transaction['Transaction']['id'], 4, '0', STR_PAD_LEFT);?></dd>

	</dl>

	<?php
		echo $this->Form->input('confirm', array('type'=>'hidden', 'value'=>1));
		echo $this->Form->submit(__('Confirm'));
	?>
	</fieldset>
<?php echo $this->Form->end();?>

<?php
}

?>

</div>

<br /><br />

<div class="actions">
	<ul>
<?php if ($transaction['Transaction']['model'] == 'PrepaidAccount') { ?>
		<li><?php echo $this->Html->link(__('View %s', __('Prepaid Account')), array('controller'=>'prepaid_accounts', 'action' => 'view')); ?> </li>
<?php } ?>
	</ul>
</div>
