# Payment Plugin containing Bitcoin IPN (Bitcoin Instant Payment Notification)
* Version 1.0
* Author: Mark Scherer
* Website: http://www.dereuromark.de

# Get it
* GIT: git@github.com:dereuromark/CakePHP-Payment-Plugin.git

# Required:
CakePHP 1.3.x


# TODOS:
* add other payment methods
* ...



# Install:
1. Copy plugin into your `/app/plugins/payment` directory
2. Run

		$ cake schema create payment -plugin payment


# Bitcoin Setup:
1. Download program at http://www.bitcoin.org/ for testing purposes
2. Set up Bitcoind daemon on your webserver
3. Get some coins :)

# Administration: (optional) If you want to use the built in admin access to IPNs:
1. Make sure you're logged in as an Administrator via the Auth component.
2. Navigate to `www.yoursite.com/admin/payment/bitcoin`


# Bitcoin Helper: (optional)
1. Add `Payment.Bitcoin` to your helpers list in `app_controller.php`

	var $helpers = array('Html','Form','Payment.Bitcoin');

## Usage: (view the actual /payment/views/helpers/bitcoin.php for more information)
		$this->Bitcoin->image(64);

		$this->Bitcoin->paymentBox(12.3, YOUR_ADDRESS);


# Bitcoin Notification Callback:
Create a function in your `/app/app_model.php` like so:

	function afterBitcoinNotification($txnId){
		//Here is where you can implement code to apply the transaction to your app.
		//for example, you could now mark an order as paid, a subscription, or give the user premium access.
		//retrieve the transaction using the txnId passed and apply whatever logic your site needs.

		$transaction = ClassRegistry::init('Payment.BitcoinAddress')->findById($txnId);
		$this->log($transaction['BitcoinAddress']['id'], 'bitcoin');

		if(...) {
			//Yay!  We have monies!
		}	else {
			//Oh no, better look at this transaction to determine what to do; like email a decline letter.
		}
	}
