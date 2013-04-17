<script>
$(document).ready(function() {
	$("#historyDetailsShow").click(function () {
		$('.historyDetails').toggle(500);
	});
	$('.historyDetails').hide();
});
</script>

<?php if (Configure::read('Project.code') == 'OF') { ?>
<div class="actions"><ul>
	<?php echo $this->element('navigation'.DS.'sidebar_your_area'); ?>
</ul></div>
<?php } ?>

<div class="page view">

<?php if (Configure::read('Project.code') != 'OF') { ?>
<h2><?php echo __('Prepaid Account');?></h2>
<?php } ?>

<h3>Vorteile</h3>
Folgende Vorteile hat ein <?php echo __('Prepaid Account');?> gegenüber anderen Zahlungsmitteln:
<ul class="advantages">
<li><b>Schnell und einfach</b><br />Null Wartezeit - die Bezahlung ist sofort abgeschlossen.</li>

<?php if (Configure::read('Project.code') == 'OF') { ?>
<li><b>Sicher und anonym</b><br />Wie Sie Geld einzahlen, bleibt Ihre Entscheidung. Bis zu 100% anonym (maximal eine E-Mail ist anzugeben). Das Geld verfällt nicht und wird bei uns sicher verwahrt.</li>
<li><b>Bares Geld sparen</b><br />Bei größeren Einzahlungen bekommen Sie von uns obendrauf Geld geschenkt. Denn über fast alle Zahlungsmittel werden bei kleineren Beträgen relativ hohe Gebühren fällig, die so wegfallen. Und das wollen wir natürlich an Sie als Kunden zurückgeben.</li>
<?php } else { ?>
<li><b>Sicher und anonym</b><br />Wie du Geld einzahlst, ist deine Sache. Bis zu 100% anonym (maximal eine E-Mail ist anzugeben). Das Geld verfällt nicht und wird bei uns sicher verwahrt.</li>
<li><b>Bares Geld sparen</b><br />Bei größeren Einzahlungen bekommst du von uns obendrauf Geld geschenkt. Denn über fast alle Zahlungsmittel werden bei kleineren Beträgen relativ hohe Gebühren fällig, die so wegfallen. Und das wollen wir natürlich an dich als Kunde zurückgeben.</li>
<?php } ?>

<li><b>Vertrauensvoll</b><br />Nur zufriedene Kunden kommen wieder. Wenn irgendetwas schief läuft, helfen wir sofort und unkompliziert.</li>
</ul>
<?php if (false && Configure::read('Project.code') == 'OF') { ?>
Wenn Sie Ihr <?php echo __('Prepaid Account');?> irgendwann wieder auflösen wollen, überweisen wir Ihnen Ihren dort gespeicherten Betrag natürlich zurück.
<?php } elseif(false) { ?>
Wenn du dein <?php echo __('Prepaid Account');?> irgendwann wieder auflösen willst, überweisen wir dir deinen dort gespeicherten Betrag natürlich zurück.
<?php } ?>
<br />

<?php if (false) { ?>
<h3></h3>

<?php } ?>


<h3>Status</h3>
<?php echo __('Your current balance is')?> <b><?php echo $this->Numeric->price($amount);?></b>.

<?php
	if (!empty($transactions)) {
?>
<div class="history" style="margin-top: 10px;">
<?php echo $this->Html->link($this->Format->cIcon(ICON_DETAILS, 'Details').' '.h(__('Transaction History')), 'javascript:void(0);', array('id'=>'historyDetailsShow', 'escape'=>false)); ?>
<div class="historyDetails">
<fieldset style="margin-top: 6px;">
	<legend>Die letzten <?php echo count($transactions); ?><?php if (count($transactions) < $transactionTotal) { echo ' von '.$transactionTotal; }?> <?php echo __('Transactions'); ?> (neuste oben)</legend>
<?php foreach ($transactions as $transaction) { ?>
	<?php
		$value = $this->Numeric->money($transaction['Transaction']['amount'], array('signed'=>true));
		//$value = $this->Format->pad($value, 9, '&nbsp;', STR_PAD_LEFT);
		if ($transaction['Transaction']['status'] == Transaction::STATUS_COMPLETED || $transaction['Transaction']['payment_status'] == 'Completed') {
			$value = '<b>'.$this->Format->ok($value, $transaction['Transaction']['amount'] >= 0).'</b>';
		} else {
			$value .= ' [<em>'.Transaction::statuses($transaction['Transaction']['status']).'</em>]';
		}
		/*
		if (Transaction::is(Transaction::STATUS_COMPLETED, $transaction['Transaction'])) {

		}
		if (Transaction::is(Transaction::STATUS_ABORTED, $transaction['Transaction'])) {
			$value .= ' [<em>'.__('Aborted').'</em>]';
		} elseif (Transaction::is(Transaction::STATUS_PENDING, $transaction['Transaction'])) {
			$value .= ' [<em>'.__('Pending').'</em>]';
		} elseif (Transaction::is(Transaction::STATUS_NEW, $transaction['Transaction'])) {
			$value .= ' [<em>'.__('Initiation').'</em>]';
		}
		*/
	?>
	<div><?php echo $this->Datetime->niceDate($transaction['Transaction']['created']);?>: <?php echo $value; ?> (<?php echo h($transaction['Transaction']['title']);?>)</div>
<?php } ?>
</fieldset>
</div>
</div>
<?php
	}
?>

<br /><br />

<h3><?php echo __('Deposit now'); ?></h3>
<?php echo $this->Form->create('PrepaidAccount');?>
	<fieldset>
 		<legend><?php echo __('Loading of %s', __('Prepaid Account')); ?></legend>
	<?php
		echo $this->Form->input('charge_amount', array('options'=>$amounts, 'legend'=>__('Amount'), 'type'=>'radio'));

		foreach($paymentMethods as $key=>$p)
			if($p['PaymentMethod']['alias'] == 'pay_on_site')
				unset($paymentMethods[$key]);
		$paymentTypes = $this->PrepaidAccount->radioButtons($paymentMethods);

		echo $this->Form->input('payment_type', array('options'=>$paymentTypes, 'legend'=>__('Payment Method'), 'type'=>'radio'));

		echo $this->Form->input('confirm', array('type'=>'checkbox', 'label'=>__('paymentAcceptTermsText %s', $this->Html->link(__('AGBs'), array('plugin'=>'', 'controller'=>'pages', 'action'=>'terms-of-use'), array('target'=>'_blank')))));

		echo $this->Form->submit(__('Submit'));
	?>
	</fieldset>
<?php echo $this->Form->end();?>
</div>

<div class="actions">
<?php if (Configure::read('Project.code') != 'OF') { ?>
	<ul>
		<li><?php echo $this->Html->defaultLink(__('Back'), array('controller'=>'members', 'action' => 'home')); ?> </li>
	</ul>
<?php } ?>
</div>