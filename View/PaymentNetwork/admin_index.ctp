<div class="page view">

<h2><?php echo __('Payment Network');?></h2>
Account: <?php echo Configure::read('PaymentNetwork.user')?> (Project <?php echo Configure::read('PaymentNetwork.project')?>)<br />
Live Mode: <?php echo $this->Format->yesNo(Configure::read('PaymentNetwork.live')); ?>

<h3>Last Transactions</h3>

<?php

?>
...

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