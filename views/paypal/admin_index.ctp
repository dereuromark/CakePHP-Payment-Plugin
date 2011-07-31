
<div class="orders view">

<h1><?php  __('Balance');?></h1>
Paypal-Account: <?php echo Configure::read('PayPal.username')?><br />
Live Mode: <?php echo $this->Common->yesNo(Configure::read('PayPal.live')); ?>

<h2>Current Balance</h2>

<?php
echo $this->Numeric->money($balance['AMOUNT']);
	echo pre($balance);
?>

<h2>Logo</h2>
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