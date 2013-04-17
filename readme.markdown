# CakePHP Payment Plugin

Containing **Bitcoin IPN** (Bitcoin Instant Payment Notification)

* Version 1.0
* Author: Mark Scherer
* Website: http://www.dereuromark.de
* License: MIT License (http://www.opensource.org/licenses/mit-license.php)
* For example implementation code see http://www.dereuromark.de/2011/07/20/bitcoins-and-cakephp/

## Requirement
CakePHP 2.x

## TODOS:
* full non-daemon "offline" mode? using the webservice completely without any local daemon necessary
* add other payment methods
* make it more independable (right now needs some of MY tools plugin stuff for the admin interface to work) - the lib itself should work just fine.


## Installation

* Clone/Copy the files in this directory into `app/Plugin/Payment`
* Don't forget to include the plugin in your bootstrap's `CakePlugin::load()` statement or use `CakePlugin::loadAll()`
* Run

		$ cake schema create payment -plugin payment


# Bitcoin Setup:
1. Download program at http://www.bitcoin.org/ for testing purposes
2. Set up Bitcoind daemon on your webserver (this is the most difficult step if you don't use the newest system)
3. Get some coins :)
4. Provide a config array in your configs: $config['Bitcoin'] = array(..) with your preferences and credentials

	### important ones are:

	* account
	* username
	* password

## Administration: (optional) If you want to use the built in admin access to IPNs:
1. Make sure you're logged in as an Administrator via the Auth component.
2. Navigate to `www.yoursite.com/admin/payment/bitcoin`


## Bitcoin Notification Callback:
Create a function in your `/app/AppModel.php` like so:

	public function afterBitcoinNotification($txnId){
		//Here is where you can implement code to apply the transaction to your app.
		//for example, you could now mark an order as paid, a subscription, or give the user premium access.
		//retrieve the transaction using the txnId passed and apply whatever logic your site needs.

		$transaction = ClassRegistry::init('Payment.BitcoinAddress')->findById($txnId);
		$this->log($transaction['BitcoinAddress']['id'], 'bitcoin');

		if(...) {
			//Yay!  We have the money!
		}	else {
			//Oh no, not enough... better look at this transaction to determine what to do; like email a decline letter.
		}
	}

## Bitcoin Helper: (optional)
1. Add `Payment.Bitcoin` to your helpers list in `AppController.php`

	public $helpers = array('Html','Form','Payment.Bitcoin');

### Usage: (view the actual /payment/View/Helpers/BitcoinHelper.php for more information)
		$this->Bitcoin->image(64);

		$this->Bitcoin->paymentBox(12.3, YOUR_ADDRESS);


# Tips
* The Lib itself has offline capacities. It is possible to use certain features even without having to run a local bitcoin daemon (at least for testing purposes on a local machine).
* Create your own admin interface for the payment methods. You can use the existing one as a template.
* Start playing around with "little" money. If sth goes wrong, your money might get lost.
* Use every bitcoin address only once! The default implementation will check only check the amount received. It can not (protocol!) distinguish between different people sending money to those addresses. therefore it cannot be used more than once if you don't want conflicts.

# Final notes
As tempting as it was to integrate the Paypal IPN Plugin which this plugin is based on, I decided not to do this at the moment.
This way they can be maintained separately.
Not only this plugin but also the technology/protocol itself is under heavy maintenance and still changing from time to time.

It could become a complete "Payment" plugin combining all methods and services one day...

I spent quite a few days developing this plugin and testing all features.
Feel free to donate if you use this plugin

* Address: 161AcnPykE42e4ErQNR9B73Bb78Jy81AN6

Enjoy!