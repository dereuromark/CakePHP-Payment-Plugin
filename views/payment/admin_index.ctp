<h1><?php __('Payment Plugin');?></h1>

<div class="index noactions">

This Plugin is mainly for Bitcoin Transactions. I tried to include some other Payment methods. But this is incomplete at the moment.
<br />Planned: Paypal, Paysafe, ...

<h2>Quick-Start Guide</h2>
@see README or my <a href="http://www.dereuromark.de/2011/07/20/bitcoins-and-cakephp/" target="_blank">BLOG</a>.

<br /><br />
<h2>Note</h2>
The admin crud actions might have Tools plugin dependencies. It might be best to use your own admin payment backend.<br />
The libraries should be totally independent, though.

<br /><br />

</div>

<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('Bitcoin Admin Center', true), array('plugin'=>'payment', 'controller'=>'bitcoin', 'action'=>'index'))?></li>
		<li><?php echo $this->Html->link(__('Bitcoin Addresses', true), array('plugin'=>'payment', 'controller'=>'bitcoin_addresses', 'action'=>'index'))?></li>
		<li><?php echo $this->Html->link(__('Bitcoin Transactions', true), array('plugin'=>'payment', 'controller'=>'bitcoin_transactions', 'action'=>'index'))?></li>
		<li><?php echo $this->Html->link(__('Paypal Transaction Notifications', true), array('plugin'=>'paypal_ipn', 'controller'=>'instant_payment_notifications', 'action'=>'index'))?> (if PaypalIPN installed)</li>
		
		<li><?php echo $this->Html->link(__('Currencies', true), array('plugin'=>'tools', 'controller'=>'currencies', 'action'=>'index'))?> (if Tools Currencies installed)</li>
		<li><?php echo $this->Html->link(__('Payment Methods', true), array('plugin'=>'tools', 'controller'=>'payment_methods', 'action'=>'index'))?> (if Tools Payment Methods installed)</li>
	</ul>
</div>