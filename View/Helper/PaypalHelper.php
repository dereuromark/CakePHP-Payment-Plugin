<?php
App::uses('AppHelper', 'View/Helper');

/** Paypal Helper
 *
 * @author Mark Scherer
 * @link http://www.dereuromark.de
 * @license MIT
 */
class PaypalHelper extends AppHelper {

	public $helpers = array('Html', 'Form');

	public $settings = array();

	public $formOptions = array();

	public $_defaults = array(
		'maxDecimals' => 2,
		'dec' => '.',
		'sep' => ',',
		'live' => false,
		'amount' => null,
	'notify_url' => '{HTTP_BASE}/paypal_ipn/process',
	'cancel_return' => true,
	'currency_code' => 'EUR', //Currency
	'locale' => 'DE', //Locality
	'shipping' => false # do not ask for address etc
	);

	/**
	 *  http://www.paypalobjects.com/de_DE/html/IntegrationCenter/ic_std-variable-reference.html
	 *  Setup the config based on Config settings
	 */
	public function __construct(View $View, $settings = array()) {
		$this->settings = $this->_defaults;
		if ($x = Configure::read('Localization.decimalPoint')) {
			$this->settings['dec'] = $x;
		}
		if ($x = Configure::read('Localization.thousandsSeparator')) {
			$this->settings['sep'] = $x;
		}
		$this->settings = array_merge($this->settings, (array)Configure::read('PayPal'));
		if ($this->settings['live']) {
			$this->formOptions['server'] = 'https://www.paypal.com';
		} else {
			$this->formOptions['server'] = 'https://www.sandbox.paypal.com';
		}
		$data = array('HTTP_HOST' => HTTP_HOST, 'HTTP_BASE' => HTTP_BASE);
		$this->formOptions['notify_url'] = String::insert($this->settings['notify_url'], $data, array('before' => '{', 'after' => '}', 'clean' => true));
		$this->formOptions['business'] = $this->settings['email'];
		$this->formOptions['lc'] = $this->settings['locale'];
		$this->formOptions['amount'] = $this->settings['amount'];
		$this->formOptions['no_shipping'] = (int)(!$this->settings['shipping']);
		$this->formOptions['currency_code'] = $this->settings['currency_code'];
		if ($this->settings['cancel_return']) {
			$this->formOptions['cancel_return'] = Router::url(null, true);
		}
		//pr($this->formOptions); die();
		parent::__construct($View, $settings);
	}

	public function value($amount, $maxDecimals = null) {
		if ($maxDecimals === null) {
			$maxDecimals = $this->settings['maxDecimals'];
		}
		return number_format($amount, $maxDecimals, $this->settings['dec'], $this->settings['sep']);
	}

/** from PaypalIpn Plugin **/

	/**
	 *  function button will create a complete form button to Pay Now, Donate, Add to Cart, or Subscribe using the paypal service.
	 *  Configuration for the button is in /config/paypal_ip_config.php
	 *
	 *  for this to work the option 'item_name' and 'amount' must be set in the array options or default config options.
	 *
	 *  Example:
	 *     $this->Paypal->button('Pay Now', array('amount' => '12.00', 'item_name' => 'test item'));
	 *     $this->Paypal->button('Subscribe', array('type' => 'subscribe', 'amount' => '60.00', 'term' => 'month', 'period' => '2'));
	 *     $this->Paypal->button('Donate', array('type' => 'donate', 'amount' => '60.00'));
	 *     $this->Paypal->button('Add To Cart', array('type' => 'addtocart', 'amount' => '15.00'));
	 *     $this->Paypal->button('View Cart', array('type' => 'viewcart'));
	 *     $this->Paypal->button('Unsubscribe', array('type' => 'unsubscribe'));
	 *     $this->Paypal->button('Checkout', array(
	 *      'type' => 'cart',
	 *      'items' => array(
	 *         array('item_name' => 'Item 1', 'amount' => '120', 'quantity' => 2, 'item_number' => '1234'),
	 *         array('item_name' => 'Item 2', 'amount' => '50'),
	 *         array('item_name' => 'Item 3', 'amount' => '80', 'quantity' => 3),
	 *       )
	 *     ));
	 *  Test Example:
	 *     $this->Paypal->button('Pay Now', array('test' => true, 'amount' => '12.00', 'item_name' => 'test item'));
	 *
	 * @param String $title takes the title of the paypal button (default "Pay Now" or "Subscribe" depending on option['type'])
	 * @param Array $options takes an options array defaults to (configuration in /config/paypal_ipn_config.php)
	 *
	 *   helper_options:
	 *      test: true|false switches default settings in /config/paypal_ipn_config.php between settings and testSettings
	 *      type: 'paynow', 'addtocart', 'donate', 'unsubscribe', 'cart', or 'subscribe' (default 'paynow')
	 *
	 *    You may pass in api name value pairs to be passed directly to the paypal form link.  Refer to paypal.com for a complete list.
	 *    some paypal API examples:
	 *      amount: float value
	 *      notify_url: string url
	 *      item_name: string name of product.
	 *      etc...
	 */
	public function button($title = null, $options = array()) {
		if (is_array($title)) {
			$options = $title;
			$title = isset($options['label']) ? $options['label'] : null;
		}
		$options = array_merge($this->formOptions, $options);
		$options['type'] = (isset($options['type'])) ? $options['type'] : "paynow";

		switch ($options['type']) {
			case 'subscribe': //Subscribe
				$options['cmd'] = '_xclick-subscriptions';
				$defaultTitle = 'Subscribe';
				$options['no_note'] = 1;
				$options['no_shipping'] = 1;
				$options['src'] = 1;
				$options['sra'] = 1;
				$options = $this->__subscriptionOptions($options);
				break;
			case 'addtocart': //Add To Cart
				$options['cmd'] = '_cart';
				$options['add'] = '1';
				$defaultTitle = 'Add To Cart';
				break;
			case 'viewcart': //View Cart
				$options['cmd'] = '_cart';
				$options['display'] = '1';
				$defaultTitle = 'View Cart';
				break;
			case 'donate': //Doante
				$options['cmd'] = '_donations';
				$defaultTitle = 'Donate';
				break;
			case 'unsubscribe': //Unsubscribe
				$options['cmd'] = '_subscr-find';
				$options['alias'] = $options['username'];
				$defaultTitle = 'Unsubscribe';
				break;
			case 'cart': //upload cart
				$options['cmd'] = '_cart';
				$options['upload'] = 1;
				$defaultTitle = 'Checkout';
				$options = $this->__uploadCartOptions($options);
				break;
			default: //Pay Now
				$options['cmd'] = '_xclick';
				$defaultTitle = 'Pay Now';
				break;
		}

		$title = (empty($title)) ? $defaultTitle : $title;
		$retval = "<form action='{$options['server']}/cgi-bin/webscr' method='post'><div class='paypal-form'>";
		unset($options['server']);
		foreach ($options as $name => $value) {
			$retval .= $this->__hiddenNameValue($name, $value);
		}
		$retval .= $this->__submitButton($title);

		return $retval;
	}

	/**
	 *  __hiddenNameValue constructs the name value pair in a hidden input html tag
	 * @param String name is the name of the hidden html element.
	 * @param String value is the value of the hidden html element.
	 * @return Html form button and close form
	 */
	public function __hiddenNameValue($name, $value) {
		return "<input type='hidden' name='$name' value='$value' />";
	}

	/**
	 *  __submitButton constructs the submit button from the provided text
	 * @param String text | text is the label of the submit button.  Can use plain text or image url.
	 * @return Html form button and close form
	 */
	public function __submitButton($text) {
		return "</div>" . $this->Form->end(array('label' => $text));
	}

	/**
	 * __subscriptionOptions conversts human readable subscription terms
	 * into paypal terms if need be
	 * @param array options | human readable options into paypal API options
	 *     INT period //paypal api period of term, 2, 3, 1
	 *     String term //paypal API term //month, year, day, week
	 *     Float amount //paypal API amount to charge for perioud of term.
	 * @return array options
	 */
	public function __subscriptionOptions($options = array()) {
		//Period... every 1, 2, 3, etc.. Term
		if (isset($options['period'])) {
			$options['p3'] = $options['period'];
			unset($options['period']);
		}
		//Mount billed
		if (isset($options['amount'])) {
			$options['a3'] = $options['amount'];
			unset($options['amount']);
		}
		//Terms, Month(s), Day(s), Week(s), Year(s)
		if (isset($options['term'])) {
			switch ($options['term']) {
				case 'month':
					$options['t3'] = 'M';
					break;
				case 'year':
					$options['t3'] = 'Y';
					break;
				case 'day':
					$options['t3'] = 'D';
					break;
				case 'week':
					$options['t3'] = 'W';
					break;
				default:
					$options['t3'] = $options['term'];
			}
			unset($options['term']);
		}

		return $options;
	}

	/**
	 * __uploadCartOptions converts an array of items into paypal friendly name/value pairs
	 * @param array of options that will be returned with proper paypal friendly name/value pairs for items
	 * @return array options
	 */
	public function __uploadCartOptions($options = array()) {
		if (isset($options['items']) && is_array($options['items'])) {
			$count = 1;
			foreach ($options['items'] as $item) {
				foreach ($item as $key => $value) {
					$options[$key . '_' . $count] = $value;
				}
				$count++;
			}
			unset($options['items']);
		}
		return $options;
	}

}
