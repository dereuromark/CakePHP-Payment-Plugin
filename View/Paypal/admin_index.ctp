
<div class="page view">

<h2><?php echo __('Balance');?></h2>
Paypal-Account: <?php echo Configure::read('PayPal.username')?><br />
Live Mode: <?php echo $this->Format->yesNo(Configure::read('PayPal.live')); ?>

<h3>Current Balance</h3>

<?php
echo $this->Numeric->money($balance['AMOUNT']);
	echo pre($balance);
?>

<h3>Logo</h3>
<?php
	if (empty($image)) {
		echo 'kein Logo';
	} else {
		echo $this->Html->image($image['url']);

		//echo '<div>';
		if ($image['result']) {
			echo 'Bild ist OK';
		} else {
			echo 'Bild hat die falschen Maße';
		}
		//echo '</div>';
	}
?>

</div>