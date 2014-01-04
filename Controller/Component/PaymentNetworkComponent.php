<?php
App::uses('Component', 'Controller');
App::uses('Transaction', 'Payment.Model');

/**
 */
class PaymentNetworkComponent extends Component {

	public $components = array();

	public $Controller = null;

	public $urls = array(
		'sandbox' => array(
			'url' => '',
			'api' => 'https://api.sofort.com/api/xml'
		),
		'live' => array(
			'url' => '',
			'api' => 'https://api.sofort.com/api/xml'
		),
		'ok' => '',
		'nok' => ''
	);

	public $settings = array(
		'live' => false,
		'currency_code' => 'EUR',
		'locale' => 'DE',
		'key' => '',
		'user' => '',
		'project' => '',
		'password' => '', # project password for classic api
		'img' => '',
		'hash' => 'sha1' # sha1/sha256/sha512 - for classic api
	);

	const VERSION = 1.0;
	const LOGO_WIDTH = 150;
	const LOGO_HEIGHT = 60;

	public function __construct(ComponentCollection $collection, $settings = array()) {
		$configSettings = (array)Configure::read('PaymentNetwork');
		$settings = array_merge($this->settings, $configSettings, (array)$settings);
		$this->Controller = $collection->getController();
		parent::__construct($collection, $settings);
	}

	/**
	 * Initialize component
	 *
	 * @return array
	 * @author Daniel Quappe
	 */
	public function initialize(Controller $Controller) {
		$this->Controller = $Controller;

		//$res = App::import('Vendor', 'Payment.sofortlib/sofortLib');
		$res = App::import('Vendor', 'Payment.sofortlib/sofortLib_sofortueberweisung_classic');
		//$res2 = App::import('Vendor', 'Payment.sofortlib/sofortLib_classic_notification.inc');
		if (!$res) {
			trigger_error('sofortlib cannot be found');
		}
		parent::initialize($Controller);
	}

	/**
	 * Get current settings
	 *
	 * @param key
	 */
	public function get($key) {
		if (!isset($this->settings[$key])) {
			trigger_error('invalid setting value requested');
			return false;
		}
		return $this->settings[$key];
	}

	/**
	 * Go the express checkout
	 *
	 * @see https://www.paypal.com/en_US/ebook/PP_NVPAPI_DeveloperGuide/Appx_fieldreference.html#2830886
	 */
	public function redirect($token) {

		if ($sofort->isError()) {
			echo $sofort->getError();
		} else {
			$url = $sofort->getPaymentUrl();
			$transactionId = $sofort->getTransactionId();
		}

		//$url = $this->_url() .	Router::querystring(array('cmd' => '_express-checkout', 'token' => $token));
		$this->Controller->redirect($url, '302');
	}

	/**
	 * @param array   $dataArray
	 * - amount (required)
	 * - ...
	 * @return array res
	 */
	public function setExpressCheckout($data) {
		$defaults = array(
			'reason' => __('Sale'),
			'description' => '',
			//'project_id' => $this->settings['project'],
		);
		$data = array_merge($defaults, $data);

		//Router::url($dataArray['returnurl'], true);
		$Sofort = new SofortLib_Multipay($this->_configKey());

		$Sofort->new($data['amount'], $this->settings['currency_code']);

		App::uses('Inflector', 'Utility');
		$Sofort->setAmount($data['amount'], $this->settings['currency_code']);
		$Sofort->setReason(Inflector::slug($data['reason']), Inflector::slug($data['description']));
		$Sofort->setSofortueberweisung();
		$Sofort->setLanguageCode(strtolower($this->settings['locale']));
		if (false) {
			$Sofort->setSofortueberweisungCustomerprotection(true);
		}

		//die(returns($Sofort->parameters));

		//die(returns($Sofort->parameters));

		$res = $Sofort->validateRequest();
		die(returns($res));

		$res = $Sofort->sendRequest();
		die(returns($res));

		return;
	}

	public function getLastTransactions($limit = null) {
		$from = date(FORMAT_DB_DATE, time() - DAY);
		$to = date(FORMAT_DB_DATE);
		//die(returns($this->settings));
		$transactionDataObj = new SofortLib_TransactionData($this->_configKey());
		$transactionDataObj->setTime($from, $to);
		if ($limit) {
			$transactionDataObj->setNumber($limit);
		}
		$res = $transactionDataObj->sendRequest();
		if (!empty($res->errors)) {
			trigger_error(returns($this->errors));
			return false;
		}
		return $transactionDataObj->getTransaction(0);
	}

	/**
	 * Using the classic way (without gateway)
	 * - identification via foreign_id + model (transaction id yet unknown)
	 */
	public function setClassicExpressCheckout($data) {
		extract($this->settings);
		$Sofort = new SofortLib_SofortueberweisungClassic($user, $project, $password, $hash);
		$Sofort->setAmount($data['amount'], $currencyCode);
 		$Sofort->setReason(Inflector::slug($data['reason']), Inflector::slug($data['description']));

 		$url = array('plugin' => 'payment', 'admin' => false, 'controller' => 'payment_network');
 		$abortUrl = array_merge($url, array('action' => 'abort'));
 		$Sofort->setAbortUrl(Router::url($abortUrl, true));
 		$successUrl = array_merge($url, array('action' => 'success'));
 		$Sofort->setSuccessUrl(Router::url($successUrl, true));
 		$nUrl = array_merge($url, array('action' => 'notification'));
 		$Sofort->setNotificationUrl(Router::url($nUrl, true));
 		if (!empty($data['token'])) {
 			$Sofort->addUserVariable($data['token']);
 		} else {
 			$Sofort->addUserVariable($data['foreign_id']);
 			$Sofort->addUserVariable($data['model']);
 		}
 		//die(returns($Sofort->params));

		$url = $Sofort->getPaymentUrl();
		$token = $Sofort->params['hash'];
		return compact('url', 'token');
	}

	/**
	 * using the classic way (without gateway)
	 */
	public function classicRedirect($data) {
		$this->Controller->redirect($data['url'], '302');
	}

	public function classicResponse() {
		extract($this->settings);
 		$Sofort = new SofortLib_ClassicNotification($user, $project, $password, $hash);
 		return $Sofort->getNotification();
	}

	/**
	 * @param path
	 * @return boolean success
	 * The image has a minimum size of 150 pixels wide by 60 pixels high.
	 */
	public function validateImage($path) {
		list ($width, $height) = @getimagesize($path);
		if ($width > 0 && $height > 0 && $width >= self::LOGO_WIDTH && $height >= self::LOGO_HEIGHT) {
			return true;
		}
		return false;
	}

	/**
	 * not implementeed
	 */
	public function getBalance() {
	}

	public function translateStatus($statusCode) {
		switch ($statusCode) {
			case 'received':
				return Transaction::STATUS_COMPLETED;
			case 'pending':
				return Transaction::STATUS_PENDING;
			case 'loss':
				return Transaction::STATUS_ABORTED;
		}
		return 0;
	}

	/**
	 * A key combining user, project and api key for use in the sofort lib
	 *
	 * @return string
	 */
	protected function _configKey() {
		return $this->settings['user'] . ':' . $this->settings['project'] . ':' . $this->settings['key'];
	}

}
